<?php

use Illuminate\Support\Facades\Schedule;
use App\Models\Order;
use Carbon\Carbon;
use App\Models\Products;
use Illuminate\Support\Facades\DB;

Schedule::call(function () {
    $expiredTime = Carbon::now()->subMinute();

    // Obtener órdenes pendientes vencidas
    $expiredOrders = Order::with('items')
        ->where('status', 'pendiente')
        ->where('created_at', '<', $expiredTime)
        ->get();

    $deletedCount = 0;

    foreach ($expiredOrders as $order) {
        DB::transaction(function () use ($order, &$deletedCount) {
            foreach ($order->items as $item) {
                $product = Products::find($item->product_id);
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
