<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class DetailproductController extends Controller
{
    
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('pages.detailproduct', compact('product'));
    }

}
