<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Product;

class CartController extends Controller
{

public function addToCart($id, Request $request)
{
    $product = Product::findOrFail($id);
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
            "imagen" => $product->image,
            "stock" => $product->stock,
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
            'total_price' => $totalPrice,
            'product_quantity' => $cart[$id]['quantity'],
        ]);
    }

    return redirect()->back();
}

// public function clearCart(Request $request)
// {
//         Log::debug('borrando)');
//         // Recibir desde query string
//         $success = $request->query('success') === '1';
//         $message = $request->query('message', '');

//         if ($success) {
//             session()->forget('cart');
//             return view('flow.return', compact('success', 'message'));
//         }
        
//         session()->forget('cart');
//         return redirect()->back()->with('success', 'Carrito vaciado correctamente');
// }

public function clearCart(Request $request)
{
    Log::debug('Borrando carrito');

    session()->forget('cart');

    if ($request->ajax()) {
        // Renderizamos el HTML actualizado del carrito
        $html = view('pages.cart')->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'message' => 'Carrito vaciado correctamente'
        ]);
    }

    // Lógica original para requests normales
    $success = $request->query('success') === '1';
    $message = $request->query('message', '');

    if ($success) {
        return view('flow.return', compact('success', 'message'));
    }

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




public function increaseFromCartAjax(Request $request)
{
    $id = $request->input('id');
    $cart = session()->get('cart', []);
    $product = Product::find($id);

    if (!$product) {
        return response()->json([
            'success' => false,
            'message' => 'Producto no encontrado',
        ], 404);
    }

    // 🔹 Verificar stock antes de agregar al carrito
    if ($product->stock <= 0) {
        return response()->json([
            'success' => false,
            'message' => 'No hay stock disponible',
        ]);
    }

    if (!isset($cart[$id])) {
        // Producto aún no agregado, inicializamos
        $cart[$id] = [
            "id" => $product->id,
            "name" => $product->name,
            "price" => $product->price,
            "quantity" => 1,
            "imagen" => $product->image,
            "stock" => $product->stock,
            "weight" => $product->weight,
            "height" => $product->height,
            "length" => $product->length,
            "width" => $product->width,
        ];
    } else {
        $currentQuantity = $cart[$id]['quantity'];
        if ($currentQuantity < $product->stock) {
            $cart[$id]['quantity'] = $currentQuantity + 1;
        } else {
            return response()->json([
                'success' => false,
                'quantity' => $cart[$id]['quantity'],
                'message' => 'No hay más stock disponible'
            ]);
        }
    }

    session()->put('cart', $cart);

    $totalQuantity = array_sum(array_column($cart, 'quantity'));
    $totalPrice = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));

    return response()->json([
        'success' => true,
        'quantity' => $cart[$id]['quantity'],
        'totalQuantity' => $totalQuantity,
        'total_price' => $totalPrice
    ]);
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

        $totalQuantity = array_sum(array_column($cart, 'quantity'));
        $totalPrice = 0;

        foreach ($cart as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'quantity' => $quantity,
            'totalQuantity' => $totalQuantity,
            'cart_count' => $totalQuantity,
            'total_price' => $totalPrice
        ]);
    }





}
