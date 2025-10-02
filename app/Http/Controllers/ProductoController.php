<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;
use App\Models\Brand;
use App\Models\Product;
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
        Product::create([
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
        $products = Product::all();
        $categories = Categories::all();
        $brands = Brand::all();
        return view('adm.edit', compact('categories','brands','products'));
    }

public function Actualizar(Request $request, $id)
{
    $product = Product::findOrFail($id);

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
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->back()->with('success', 'Producto eliminado correctamente.');
    }


public function Mostrar(Request $request)
{
    $query = Product::query();

    // Búsqueda por texto
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Filtrar categorías
    $categoryIds = $request->query('category_ids', []);
    if (!is_array($categoryIds)) {
        $categoryIds = [$categoryIds];
    }
    if (!empty($categoryIds)) {
        $query->whereIn('category_id', $categoryIds);
    }

    // Filtrar marcas
    $brandIds = $request->query('brand_ids', []);
    if (!is_array($brandIds)) {
        $brandIds = [$brandIds];
    }
    if (!empty($brandIds)) {
        $query->whereIn('brand_id', $brandIds);
    }

    // Filtrar precios predefinidos
    $priceRanges = $request->query('price_ranges', []);
    if (!empty($priceRanges)) {
        $query->where(function($q) use ($priceRanges) {
            foreach ($priceRanges as $range) {
                switch ($range) {
                    case '0-5000': $q->orWhereBetween('price', [0, 5000]); break;
                    case '5000-10000': $q->orWhereBetween('price', [5000, 10000]); break;
                    case '10000-20000': $q->orWhereBetween('price', [10000, 20000]); break;
                    case '20000-40000': $q->orWhereBetween('price', [20000, 40000]); break;
                    case '60000+': $q->orWhere('price', '>=', 60000); break;
                }
            }
        });
    }

    $products = $query->paginate(12)->withQueryString();

    if ($request->ajax()) {
        return view('pages.productslist', compact('products'))->render();
    }

    $categories = Categories::all();
    $brands = Brand::all();

    return view('pages.productos', compact('products','categories','brands'));
}











}
