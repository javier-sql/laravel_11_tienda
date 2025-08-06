<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;
use App\Models\Brand;
use App\Models\Products;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        

        // Subir imagen (si viene)
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('productos', 'public');
            // Esto la guarda en: storage/app/public/productos/
            // Y puedes acceder a ella desde: /storage/productos/archivo.jpg
        }


        // Crear el producto
        Products::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'user_id' => Auth::user()->id,
            'image' => $imagePath,
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

    // Validación básica
    $request->validate([
        'name' => 'required|max:255',
        'description' => 'required',
        'price' => 'required|numeric',
        'stock' => 'required|integer',
        'category_id' => 'required|exists:categories,id',
        'brand_id' => 'required|exists:brands,id',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    // Subir nueva imagen si se proporciona
    if ($request->hasFile('image')) {
        // Eliminar imagen anterior si existe
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        // Subir nueva imagen
        $imagePath = $request->file('image')->store('productos', 'public');
        $product->image = $imagePath;
    }

    // Actualizar campos
    $product->name = $request->name;
    $product->description = $request->description;
    $product->price = $request->price;
    $product->stock = $request->stock;
    $product->category_id = $request->category_id;
    $product->brand_id = $request->brand_id;

    $product->save();

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
