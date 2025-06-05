<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Asignar roles directamente al campo 'role'
        $admin = User::find(1);
        if ($admin) {
            $admin->role = 'admin';
            $admin->save();
        }

        $seller = User::find(2);
        if ($seller) {
            $seller->role = 'seller';
            $seller->save();
        }

        $buyer = User::find(3);
        if ($buyer) {
            $buyer->role = 'buyer';
            $buyer->save();
        }
    }
}
