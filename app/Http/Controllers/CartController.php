<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;

class CartController extends Controller
{
    public function addToCart($id)
    {
        $product = Products::findOrFail($id);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "price" => $product->price,
                "quantity" => 1
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Producto agregado al carrito');
    }

    public function viewCart()
    {
        return view('pages.cart');
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

    public function clearCart()
    {
        session()->forget('cart');
        return redirect()->back()->with('success', 'Carrito vaciado');
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

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
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


}
