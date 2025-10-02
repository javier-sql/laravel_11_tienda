<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image',
        'category_id',
        'brand_id',
        'user_id',
        'weight',  
        'length',   
        'width',    
        'height'    
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
