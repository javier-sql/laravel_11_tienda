<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;
use App\Models\Brand;
use App\Models\Products;
use Illuminate\Support\Facades\Auth;
class ProductoController extends Controller
{

    public function Formulario()
    {
        $categories = Categories::all();
        $brands = Brand::all();
        return view('adm.create', compact('categories','brands'));
    }

    public function Create(Request $request)
    {
        // validar datos
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
        ]);
        
        // Crear el producto
        Products::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'user_id' => Auth::user()->id,
        ]);
    
        // Redirigir de vuelta con un mensaje de éxito
        return back()->with('success', 'Marca creada exitosamente.');
    }

    public function Editar()
    {
        $products = Products::all();
        $categories = Categories::all();
        $brands = Brand::all();
        return view('adm.edit', compact('categories','brands','products'));
    }

    public function Actualizar(Request $request, $id)
    {
        $product = Products::findOrFail($id);

        $product->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'stock' => $request->input('stock'),
            'image' => $request->input('image'),
            'category_id' => $request->input('category_id'),
            'brand_id' => $request->input('brand_id'),
        ]);

        return redirect()->back()->with('success', 'Producto actualizado correctamente.');
    }
    
    public function destroy($id)
    {
        $product = Products::findOrFail($id);
        $product->delete();

        return redirect()->back()->with('success', 'Producto eliminado correctamente.');
    }

    public function Mostrar()
    {
        $products = Products::all();
        return view('pages.productos', compact('products'));
    }

}
