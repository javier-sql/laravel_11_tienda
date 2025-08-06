<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        '/flow/confirmacion',
        '/flow/retorno',
        'flow/confirmacion/*',
        'flow/retorno/*',
    ];

}