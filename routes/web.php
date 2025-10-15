<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DetailproductController;
use App\Http\Controllers\CheckoutController;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

Route::redirect('/','/inicio');

Route::get('/inicio', function () {
    $latestProducts = Product::orderBy('updated_at', 'desc')->take(6)->get();
    return view('pages.inicio', compact('latestProducts'));
});

// Ruta para mostrar el formulario de inicio de sesión
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');

// Ruta para procesar el inicio de sesión
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Ruta para cerrar sesión
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', function () {
    return view('auth.register');
});

// ruta para formulario
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
// ruta para registrar
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
// *** Rutas de administración ***

// Ruta de administracion
Route::middleware(['auth', RoleMiddleware::class])
    ->prefix('administrador')
    ->name('administrador.')
    ->group(function () {
        Route::get('/', function () {
            return view('adm.administrador');
        });
        // Mostrar formulario
        Route::get('/create', [ProductoController::class, 'Formulario'])->name('Create');
        // Procesar el formulario de producto
        Route::post('/create', [ProductoController::class, 'Create'])->name('Save');
        // Data editar   
        Route::get('/edit', [ProductoController::class, 'Editar'])->name('Editar');
        // Actualizar producto
        Route::post('/update/{id}', [ProductoController::class, 'Actualizar'])->name('Update');
        // Eliminar producto
        Route::delete('/delete/{id}', [ProductoController::class, 'destroy'])->name('Delete');

    });

Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');

Route::delete('/categories_destroy/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

// Ruta para almacenar una nueva brand
Route::post('/brand', [BrandController::class, 'store'])->name('brand.store');

Route::put('/brands/{id}', [BrandController::class, 'update'])->name('brand.update');
Route::delete('/brands_destroy/{id}', [BrandController::class, 'destroy'])->name('brand.destroy');


Route::put('/products/{id}', [ProductoController::class, 'Actualizar'])->name('products.Actualizar');

// *** fin de rutas de administración ***

//*** Rutas de Productos ***
Route::get('/productos', [ProductoController::class, 'Mostrar'])->name('productos');

// *** fin rutas de Productos ***


//*** Rutas Carrito ***
Route::get('/cart', [CartController::class, 'viewCart'])->name('cart.view');

Route::post('/add-to-cart/{id}', [CartController::class, 'addToCart'])->name('cart.add');


Route::get('/remove-from-cart/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');

Route::get('/clear-cart', [CartController::class, 'clearCart'])->name('cart.clear');

Route::post('/cart/decrease', [CartController::class, 'decreaseFromCartAjax'])->name('cart.decrease.ajax');
Route::post('/cart/increase', [CartController::class, 'increaseFromCartAjax'])->name('cart.increase.ajax');


Route::post('/checkout/save-address', [CheckoutController::class, 'saveAddress'])->name('checkout.saveAddress');

//*** Fin ruta Carrito  ***


//*** Rutas Detalle Producto ***
Route::get('/product/{id}', [DetailproductController::class, 'show'])->name('product.show');
//*** Fin Ruta Detalle Producto */


//*** Rutas CheckOut  */

Route::get('/checkout', [CheckoutController::class, 'view'])->name('checkout.view');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

Route::match(['get', 'post'], 'flow/retorno', [CheckoutController::class, 'flowReturn']);
Route::match(['get', 'post'], 'flow/confirmacion', [CheckoutController::class, 'flowConfirmation']);


Route::post('/checkout/calcular-tarifa', [CheckoutController::class, 'calcularTarifa'])->name('checkout.calcularTarifa');
Route::get('/ciudades/{region}', [CheckoutController::class, 'getCiudades']);
Route::get('/sucursales/{ciudadCodigo}', [CheckoutController::class, 'getSucursales']);

//*** Fin Ruta CheckOut */


///*** Ruta Activacion de cuenta */
Route::get('/activar-cuenta/{token}', [AuthController::class, 'activateAccount'])->name('activate.account');
///*** Fin Ruta Activacion de cuenta */



Route::get('/prueba-success', function () {
    return redirect('/login')->with('success', 'Sesión creada correctamente.');
});
