<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Products;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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

        // Validar stock
        foreach ($cart as $productId => $item) {
            $product = Products::find($productId);
            if (!$product || $product->stock < $item['quantity']) {
                return redirect()->route('cart.view')->with('error', "El producto {$item['name']} no tiene stock suficiente.");
            }
        }

        DB::beginTransaction();
        try {
            // Crear orden
            $order = Order::create([
                'user_id' => Auth::check() ? Auth::id() : null,
                'customer_name' => $request->input('name'),
                'customer_email' => $request->input('email'),
                'total' => collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']),
                'status' => 'pendiente',
            ]);

            // Crear order_items
            foreach ($cart as $productId => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'product_name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();

            // FLOW: Crear transacción
            $flowData = [
                'commerceOrder' => $order->id,
                'subject' => 'Compra en tu tienda',
                'currency' => 'CLP',
                'amount' => $order->total,
                'email' => $order->customer_email,
                'urlReturn' => route('flow.return'),
                'urlConfirmation' => route('flow.confirmation'),
            ];

            // Aquí se envía a Flow (simulación básica, usar Flow SDK oficial si puedes)
            $response = Http::asForm()->post('https://sandbox.flow.cl/api/payment/create', [
                'apiKey' => env('FLOW_API_KEY'),
                'commerceId' => env('FLOW_COMMERCE_ID'),
                'paymentData' => json_encode($flowData),
            ]);

            $result = $response->json();
            if (!$response->successful() || empty($result['url'])) {
                return redirect()->route('cart.view')->with('error', 'No se pudo iniciar el pago con Flow.');
            }

            session()->put('flow_order_id', $order->id);
            return redirect()->away($result['url']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.view')->with('error', 'Error al procesar tu compra.');
        }
    }

    public function flowReturn(Request $request)
    {
        return redirect()->route('cart.view')->with('success', 'Gracias por tu compra. Pronto recibirás un correo de confirmación.');
    }

    public function flowConfirmation(Request $request)
    {
        $orderId = $request->input('commerceOrder');
        $status = $request->input('status'); // pagado, fallido, etc.

        $order = Order::find($orderId);
        if ($order && $order->status !== 'pagado' && $status === 'pagado') {
            // Descontar stock
            foreach ($order->items as $item) {
                $product = Products::find($item->product_id);
                if ($product) {
                    $product->stock -= $item->quantity;
                    $product->save();
                }
            }

            $order->status = 'pagado';
            $order->flow_response = json_encode($request->all());
            $order->save();
        }

        return response('OK', 200);
    }
}
