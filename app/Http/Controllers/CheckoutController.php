<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Products;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class CheckoutController extends Controller
{
    public function view()
    {
        if (empty(session('cart'))) {
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

    DB::beginTransaction();

    try {
        $validatedCart = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = Products::where('id', $productId)->lockForUpdate()->first();

            if (!$product) {
                throw new \Exception("Producto no encontrado.");
            }

            if ($product->stock < $item['quantity']) {
                throw new \Exception("El producto {$product->name} no tiene stock suficiente.");
            }

            $realPrice = $product->price;
            $quantity = $item['quantity'];
            $subtotal = $realPrice * $quantity;

            if ($item['price'] != $realPrice) {
                Log::info("⚠️ Precio manipulado - Producto ID: $productId - Precio sesión: {$item['price']} - Precio real: $realPrice");
                echo "<script>console.warn('⚠️ Precio alterado detectado. ID: {$productId}, sesión: {$item['price']}, real: {$realPrice}');</script>";
            }

            $product->stock -= $quantity;
            $product->save();

            $validatedCart[$productId] = [
                'product'  => $product,
                'quantity' => $quantity,
                'price'    => $realPrice,
                'subtotal' => $subtotal
            ];

            $total += $subtotal;
        }

        $order = Order::create([
            'user_id'        => Auth::check() ? Auth::id() : null,
            'customer_name'  => $request->input('name'),
            'customer_email' => $request->input('email'),
            'total'          => intval($total),
            'status'         => 'pendiente',
        ]);

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

    $secretKey = 'b88ae8b531819d471a566db89438bf842833bdad';
    $apikey = '5E41EFA7-A785-4F19-9F7A-75826F7L7370';
    $urlngrok = 'https://6def0f55088e.ngrok-free.app';

    $params = [
        "apiKey" => $apikey,
        "amount" => $order->total,
        "currency" => "CLP",
        "commerceOrder" => $order->id,
        "email" => $order->customer_email,
        "subject" => "Compra de prueba",
        "urlConfirmation" => "$urlngrok/flow/confirmacion",
        "urlReturn" => "$urlngrok/flow/retorno",
        "service" => "payment/create"
    ];

    //ordenar las claves alfabéticamente
    $keys = array_keys($params);
    sort($keys);

    //concatenar nombre y valor
    $toSign = '';
    foreach ($keys as $key) {
        $toSign .= $key . $params[$key];
    }

    //generar firma HMAC SHA256
    $signature = hash_hmac('sha256', $toSign, $secretKey);

    // Agregar firma al payload
    $params['s'] = $signature;

    // Hacer POST
    $response = Http::asForm()->post('https://sandbox.flow.cl/api/payment/create', $params);

    // Verificar respuesta
    if ($response->successful()) {
        //return $response->json(); // o redirigir al link
        return redirect($response->json()['url'] . '?token=' . $response->json()['token']);

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



public function flowReturn(Request $request)
{
    Log::info("🌐 Entró a flowReturn");

    $token = $request->input('token');

    if (!$token) {
        Log::error("Token no recibido en flowReturn");
        return response('Token no recibido', 400);
    }

    $secretKey = env('FLOW_SECRET_KEY');
    $apikey = env('FLOW_API_KEY'); // Asegúrate de que este valor esté definido en tu archivo .env

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

        if ($status == 2) {
            // Pago exitoso
            return redirect()->route('cart.clear', [
                'success' => '1',
                'message' => $message,
            ]);
        } else {
            // Pago rechazado
            return redirect()->route('cart.clear', [
                'success' => '0',
                'message' => $message,
            ]);
        }

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

    $secretKey = 'b88ae8b531819d471a566db89438bf842833bdad';
    $apikey = '5E41EFA7-A785-4F19-9F7A-75826F7L7370';

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

        if ($order && $order->status !== 'pagado' && $status == 2) {
            $order->status = 'pagado';
            $order->flow_response = json_encode($data);
            $order->save();
            session()->forget('cart');

            Log::info("💾 Orden actualizada", [
                'order_id' => $order->id,
                'nuevo_estado' => $order->status
            ]);
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

