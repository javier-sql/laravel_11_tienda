<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    protected $fillable = ['name', 'price'];

    public $timestamps = false;

    // Una comuna puede estar asociada a muchas órdenes
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
