<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Commune;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\AddressService;



class CheckoutController extends Controller
{
    protected $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    public function view()
    {

        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.view')->with('error', 'Tu carrito está vacío.');
        }

        return view('pages.checkout');
    }

    public function process(Request $request)
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.view')->with('error', 'Tu carrito está vacío.');
        }

        // Tomar el costo de envío desde la sesión
        $shipping = session('checkout.shipping', 0);
        $address = session('checkout.address', []);

        DB::beginTransaction();

        try {
            $validatedCart = [];
            $subtotalProducts = 0;

            foreach ($cart as $productId => $item) {
                $product = Product::where('id', $productId)->lockForUpdate()->first();

                if (!$product) {
                    DB::rollBack();
                    return redirect()->route('cart.view')->with('error', "Producto no encontrado.");
                }

                // Validar stock
                if ($product->stock < $item['quantity']) {
                    DB::rollBack();

                    // ⚠️ Quitar el producto del carrito de la sesión
                    $cart = session('cart', []);
                    unset($cart[$productId]);
                    session(['cart' => $cart]);

                    $msg = "El producto {$product->name} no tiene stock suficiente y será borrado de tu carrito. Stock actual: {$product->stock}";

                    // Si la petición viene por fetch/AJAX
                    if ($request->expectsJson()) {
                        return response()->json([
                            'errorstock' => $msg,
                            'redirect_url' => route('cart.view'),
                        ]);
                    }

                    // Si es form normal
                    return redirect()->route('cart.view')->with('errorstock', $msg);
                }





                $realPrice = $product->price;
                $quantity = $item['quantity'];
                $subtotal = $realPrice * $quantity;

                // Detectar manipulación de precios
                if ($item['price'] != $realPrice) {
                    Log::warning("⚠️ Precio manipulado - Producto ID: $productId - Precio sesión: {$item['price']} - Precio real: $realPrice");
                }

                // Actualizar stock
                $product->stock -= $quantity;
                $product->save();

                $validatedCart[$productId] = [
                    'product'  => $product,
                    'quantity' => $quantity,
                    'price'    => $realPrice,
                    'subtotal' => $subtotal
                ];

                $subtotalProducts += $subtotal;
            }

            // Total final incluye envío
            $totalWithShipping = $subtotalProducts + $shipping;

            $order = Order::create([
                'user_id'           => Auth::check() ? Auth::id() : null,
                'customer_name'     => $request->input('name'),
                'customer_email'    => $request->input('email'),
                'shipping_name'     => $request->input('name'),
                'shipping_email'    => $request->input('email'),
                'shipping_phone'    => $address['phone'] ?? null,
                'shipping_street'   => $address['street'] ?? null,
                'shipping_number'   => $address['number'] ?? null,
                'shipping_unit'     => $address['property_number'] ?? $address['apartment'] ?? null,
                'shipping_city'     => $address['property_type'] ?? null,
                'shipping_commune'  => $address['commune_name'] ?? null,
                'shipping_zip'      => $address['zip'] ?? null,
                'total'             => $totalWithShipping,
                'shipping_type'     => $address['property_type'] ?? 'prepagado',
                'shipping_cost'     => $shipping,
                'status'            => 'pendiente',
            ]);



            // Crear items de la orden
            foreach ($validatedCart as $productId => $itemData) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $productId,
                    'product_name' => $itemData['product']->name,
                    'price'        => $itemData['price'],
                    'quantity'     => $itemData['quantity'],
                ]);
            }

            DB::commit();

            // Preparar pago Flow
            $secretKey = env('FLOW_SECRET_KEY');
            $apikey = env('FLOW_API_KEY');
            $urlApp = env('APP_URL');

            $params = [
                "apiKey" => $apikey,
                "amount" => $order->total,  // incluye envío
                "currency" => "CLP",
                "commerceOrder" => $order->id,
                "email" => $order->customer_email,
                "subject" => "Compra de prueba",
                "urlConfirmation" => "$urlApp/flow/confirmacion",
                "urlReturn" => "$urlApp/flow/retorno",
                "service" => "payment/create"
            ];

            // Generar firma HMAC
            $keys = array_keys($params);
            sort($keys);
            $toSign = '';
            foreach ($keys as $key) {
                $toSign .= $key . $params[$key];
            }
            $params['s'] = hash_hmac('sha256', $toSign, $secretKey);

            // Crear pago en Flow
            $response = Http::asForm()->post('https://sandbox.flow.cl/api/payment/create', $params);
            if ($response->successful()) {
                return response()->json([
                    'redirect_url' => $response->json()['url'] . '?token=' . $response->json()['token']
                ]);
            } else {
                return response()->json([
                    'error' => 'Error al crear pago',
                    'body' => $response->body(),
                    'status' => $response->status()
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en checkout', ['error' => $e->getMessage(), 'cart' => $cart]);
            return redirect()->route('cart.view')->with('error', 'Error al procesar tu compra: ' . $e->getMessage());
        }
    }
    
    public function saveAddress(Request $request)
    {
        $request->validate([
            'commune_id'       => 'required|exists:communes,id',
            'street'           => 'required|string',
            'number'           => 'required|string',
            'property_type'    => 'nullable|string', 
            'property_number'  => 'nullable|string',
            'phone'            => 'required|string',
            'shipping'         => 'required|numeric',
        ]);

        $commune = Commune::find($request->commune_id);

        // Validar que la dirección coincida con la comuna
        //$isValid = $this->validateCommuneAddress($commune->name, $request->street, $request->number);
        $isValid = $this->addressService->validateCommuneAddress($commune->name, $request->street, $request->number);


        if (!$isValid) {
            Log::warning('Intento de direccion incorrecta', [
                'commune_selected' => $commune->name,
                'street' => $request->street,
                'number' => $request->number
            ]);
            return response()->json([
                'success' => false,
                'error' => 'La dirección no coincide con la comuna seleccionada o numero. Si es Avenida comience con "Av." o "Avenida". Si es Pasaje comience con "Pje." o "Pasaje". Si es calle, no use prefijos.'
            ]);
        }

        // Guardamos todo en sesión, incluyendo tipo de propiedad y número
        session([
            'checkout.address' => [
                'commune_id'       => $request->commune_id,
                'commune_name'     => Commune::find($request->commune_id)->name,
                'street'           => $request->street,
                'number'           => $request->number,
                'property_type'    => $request->property_type,
                'property_number'  => $request->property_number,
                'apartment'        => $request->apartment,
                'phone'            => $request->phone,
                'zip'              => $request->zip ?? null,
            ],
            'checkout.shipping' => $request->shipping,
        ]);


        Log::info('Dirección guardada en sesión', [
            'commune' => $commune->name,
            'street' => $request->street,
            'number' => $request->number,
            'property_type' => $request->property_type,
            'property_number' => $request->property_number,
            'apartment' => $request->apartment,
            'phone' => $request->phone,
            'shipping' => $request->shipping,
        ]);

        return response()->json(['success' => true]);
    }

    // private function validateCommuneAddress($communeName, $street, $number)
    // {
    //     sleep(2);
    //     // Construimos la dirección completa
    //     $address = "$number $street, Chile";

    //     // Consulta a Nominatim
    //     $response = Http::withHeaders([
    //         'User-Agent' => 'consulta'
    //     ])->get('https://nominatim.openstreetmap.org/search', [
    //         'q' => $address,
    //         'format' => 'json',
    //         'addressdetails' => 1,
    //         'limit' => 1
    //     ]);

    //         $data = $response->json();
    //         $osmSuburb = $data[0]['address']['suburb'] ?? null;

    //         Log::info('Comuna detectada por API', ['suburb' => $osmSuburb]);

    //         if ($osmSuburb) {
    //             return mb_strtolower($osmSuburb) === mb_strtolower($communeName);
    //         }

    //     return false;
    // }

    public function flowReturn(Request $request)
    {
        Log::info("🌐 Entró a flowReturn");

        $token = $request->input('token');

        if (!$token) {
            Log::error("Token no recibido en flowReturn");
            return response('Token no recibido', 400);
        }

        $secretKey = env('FLOW_SECRET_KEY');
        $apikey = env('FLOW_API_KEY');

        $params = [
            'token' => $token,
            'apiKey' => $apikey,
        ];

        // Firmar
        $keys = array_keys($params);
        sort($keys);
        $toSign = '';
        foreach ($keys as $key) {
            $toSign .= $key . $params[$key];
        }
        $signature = hash_hmac('sha256', $toSign, $secretKey);
        $params['s'] = $signature;

        // Consultar estado
        $url = 'https://sandbox.flow.cl/api/payment/getStatus?' . http_build_query($params);
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            Log::info("🔁 Respuesta en flowReturn", $data);

            $orderId = $data['commerceOrder'] ?? null;
            $status = $data['status'] ?? null;
            $message = $data['message'] ?? 'No se recibió motivo.';

            if ($orderId) {
                $order = Order::find($orderId);

                if ($order && $status != 2) {
                    // No fue exitoso => actualizar estado y guardar mensaje
                    $order->status = 'rechazado';
                    $order->flow_response = json_encode($data);
                    $order->save();

                    Log::info("❌ Orden rechazada", [
                        'order_id' => $orderId,
                        'motivo' => $message
                    ]);
                }

                return redirect()->route('cart.view', [
                    'success' => $status == 2 ? '2' : '0',
                    'message' => $status == 2 ? 'Pago aprobado' : 'Pago rechazado'
                ]);


            }

            return response('Orden no encontrada', 404);
        }

        Log::error("❌ Error en flowReturn", [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        return response('Error al consultar Flow', 500);
    }

    public function flowConfirmation(Request $request)
    {
        Log::info("✅ Entró a flowConfirmation");

        $token = $request->input('token');

        Log::info("🪙 Token recibido", ['token' => $token]);

        if (!$token) {
            Log::error("Token no recibido en flowConfirmation");
            return response('Bad Request', 400);
        }

        $secretKey = env('FLOW_SECRET_KEY');
        $apikey = env('FLOW_API_KEY');

        // Parámetros sin firma
        $params = [
            'token' => $token,
            'apiKey' => $apikey,
        ];

        // Ordenar claves y concatenar para firmar
        $keys = array_keys($params);
        sort($keys);

        $toSign = '';
        foreach ($keys as $key) {
            $toSign .= $key . $params[$key];
        }

        // Generar firma HMAC SHA256
        $signature = hash_hmac('sha256', $toSign, $secretKey);

        // Agregar firma a parámetros
        $params['s'] = $signature;

        // Armar URL con query params
        $url = 'https://sandbox.flow.cl/api/payment/getStatus?' . http_build_query($params);

        // Hacer la petición GET (igual que curl en el ejemplo)
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            Log::info("✅ Respuesta de Flow", $data);

            $orderId = $data['commerceOrder'] ?? null;
            $status = $data['status'] ?? null;

            $order = Order::find($orderId);

        if ($order && $order->status !== 'pagado') {
            if ($status == 2) {
                // Pago aprobado → actualizar estado
                $order->status = 'pagado';
                $order->flow_response = json_encode($data);
                $order->save();
            } else {
                $order->status = 'rechazado';
                $order->flow_response = json_encode($data);
                $order->save();

                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->stock += $item->quantity;
                        $product->save();
                    }
                }
            }
        }

            return response('OK', 200);
        } else {
            Log::error("❌ Error al consultar estado en Flow", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return response('Error', 500);
        }
    }


}

