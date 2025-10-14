<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class DeleteInactiveUsers extends Command
{
    protected $signature = 'users:delete-inactive';
    protected $description = 'Elimina usuarios que no activaron su cuenta después de 7 días';

    public function handle()
    {
        $limite = Carbon::now()->subDays(7);

        $usuarios = User::where('is_active', false)
                        ->where('created_at', '<', $limite)
                        ->get();

        foreach ($usuarios as $user) {
            $user->delete();
        }

        $this->info(count($usuarios) . ' usuarios inactivos eliminados.');
    }
}

