<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $sellerRole = Role::firstOrCreate(['name' => 'seller']);
        $buyerRole = Role::firstOrCreate(['name' => 'buyer']);

        // Asignar rol a usuarios (ejemplo)
        $admin = User::find(1);
        if ($admin) {
            $admin->assignRole('admin');
        }

        $seller = User::find(2);
        if ($seller) {
            $seller->assignRole('seller');
        }

        $buyer = User::find(3);
        if ($buyer) {
            $buyer->assignRole('buyer');
        }
    }
}
