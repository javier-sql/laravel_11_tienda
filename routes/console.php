<?php

use Illuminate\Support\Facades\Schedule;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// --- Tarea para órdenes vencidas ---
Schedule::call(function () {
    $expiredTime = Carbon::now()->subMinute();

    $expiredOrders = Order::with('items')
        ->where('status', 'pendiente')
        ->where('created_at', '<', $expiredTime)
        ->get();

    $deletedCount = 0;

    foreach ($expiredOrders as $order) {
        DB::transaction(function () use ($order, &$deletedCount) {
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->stock += $item->quantity;
                    $product->save();
                }
            }
            $order->delete();
            $deletedCount++;
        });
    }

    logger("🧹 Órdenes pendientes eliminadas: $deletedCount (y stock restaurado)");

})->everyMinute();

// --- Tarea para usuarios inactivos ---
Schedule::call(function () {
    $limite = Carbon::now()->subDays(7);

    $usuarios = User::where('is_active', false)
                    ->where('created_at', '<', $limite)
                    ->get();

    $deleted = 0;
    foreach ($usuarios as $user) {
        $user->delete();
        $deleted++;
    }

    logger("🧹 Usuarios inactivos eliminados: $deleted");

})->daily(); // se ejecuta una vez al día
