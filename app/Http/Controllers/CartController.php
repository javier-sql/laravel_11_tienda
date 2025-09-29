<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Products;

class CartController extends Controller
{

public function addToCart($id, Request $request)
{
    $product = Products::findOrFail($id);
    $cart = session()->get('cart', []);

    $currentQuantity = $cart[$id]['quantity'] ?? 0;

    if ($currentQuantity >= $product->stock) {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'No hay más stock disponible para este producto.'], 400);
        }

        return redirect()->back()->with('error', 'No hay más stock disponible para este producto.');
    }

    if (isset($cart[$id])) {
        $cart[$id]['quantity']++;
    } else {
        $cart[$id] = [
            "id" => $product->id,
            "name" => $product->name,
            "price" => $product->price,
            "quantity" => 1,
            "weight" => $product->weight,
            "height" => $product->height,
            "length" => $product->length,
            "width" => $product->width,
        ];

    }

    session()->put('cart', $cart);
    
    $totalQuantity = array_sum(array_column($cart, 'quantity'));
    $totalPrice = 0;
    foreach ($cart as $item) {
        $totalPrice += $item['price'] * $item['quantity'];
    }


    if ($request->expectsJson()) {
        return response()->json([
            'success' => 'Producto agregado al carrito',
            'cart_count' => $totalQuantity,
            'total_price' => $totalPrice
        ]);
    }

    return redirect()->back();
}

public function clearCart(Request $request)
{
        Log::debug('borrando)');
        // Recibir desde query string
        $success = $request->query('success') === '1';
        $message = $request->query('message', '');

        if ($success) {
            session()->forget('cart');
            return view('flow.return', compact('success', 'message'));
        }
        
        session()->forget('cart');
        return redirect()->back()->with('success', 'Carrito vaciado correctamente');
}

public function viewCart(Request $request)
{
    $success = $request->query('success'); // viene de Flow
    $message = $request->query('message') ?? '';

    return view('pages.cart')->with([
        'success' => $success === '2' ? $message : null,
        'error' => $success === '0' ? $message : null,
    ]);
}


public function removeFromCart($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Producto eliminado del carrito');
    }


public function decreaseFromCartAjax(Request $request)
    {
        $id = $request->input('id');
        $cart = session()->get('cart', []);

        if (isset($cart[$id]) && $cart[$id]['quantity'] > 1) {
            $cart[$id]['quantity']--;
            session()->put('cart', $cart);
        }

        $totalQuantity = array_sum(array_column($cart, 'quantity'));
        $quantity = $cart[$id]['quantity'] ?? 0;

        return response()->json([
            'success' => true,
            'quantity' => $quantity,
            'totalQuantity' => $totalQuantity,
        ]);
    }


    public function increaseFromCartAjax(Request $request)
    {
        $id = $request->input('id');
        $cart = session()->get('cart', []);

        $product = Products::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado',
            ], 404);
        }

        $currentQuantity = $cart[$id]['quantity'] ?? 0;

        if ($currentQuantity < $product->stock) {
            $cart[$id]['quantity'] = $currentQuantity + 1;
            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'quantity' => $cart[$id]['quantity'],
                'totalQuantity' => array_sum(array_column($cart, 'quantity')),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'quantity' => $currentQuantity,
                'message' => 'No hay más stock disponible',
            ]);
        }
    }


}
