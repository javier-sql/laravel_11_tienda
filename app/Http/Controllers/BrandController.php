<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function store(Request $request)
    {
        // Validar el nombre de la marca
        $request->validate([
            'name' => 'required|unique:brands,name|max:255',
        ]);

        // Crear la nueva marca
        Brand::create([
            'name' => $request->name,
        ]);

        // Redirigir de vuelta con un mensaje de éxito
        return back()->with('success', 'Marca creada exitosamente.');
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        // Validar que el nombre sea único excepto para la marca actual
        $request->validate([
            'name' => 'required|max:255|unique:brands,name,' . $brand->id,
        ]);

        $brand->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Marca actualizada correctamente.');
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);

        if ($brand) {
            $brand->delete();
        }

        return redirect()->back()->with('success', 'Marca eliminada correctamente.');
    }
}
