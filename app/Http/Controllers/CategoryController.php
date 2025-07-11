<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        // Validar el nombre de la categoría
        $request->validate([
            'name' => 'required|unique:categories,name|max:255',
        ]);

        // Crear la nueva categoría
        Categories::create([
            'name' => $request->name,
        ]);

        // Redirigir de vuelta con un mensaje de éxito
        return back()->with('success', 'Categoría creada exitosamente.');
    }

    public function update(Request $request, $id)
    {
        $category = Categories::findOrFail($id);

        $category->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy($id)
    {      
        $category = Categories::findOrFail($id);
        
        if($category){
            $category->delete();
        }
        
        
        return redirect()->back()->with('success', 'Categoría eliminada correctamente.');
    }
}