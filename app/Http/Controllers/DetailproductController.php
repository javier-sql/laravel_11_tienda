<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;

class DetailproductController extends Controller
{
    
    public function show($id)
    {
        $product = Product::findOrFail($id);
        $brandName = $product->brand->name;

        return view('pages.detailproduct', compact('product','brandName'));
    }

}
