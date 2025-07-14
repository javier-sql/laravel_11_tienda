<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;

class DetailproductController extends Controller
{
    
    public function show($id)
    {
        $product = Products::findOrFail($id);
        return view('pages.detailproduct', compact('product'));
    }

}
