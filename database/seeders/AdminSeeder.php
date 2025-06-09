<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Insertar usuario administrador
        DB::table('users')->insert([
            'name' => 'Administrador',
            'email' => 'admin@aps.com',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Verificar que se creó correctamente
        $admin = DB::table('users')->where('email', 'admin@aps.com')->first();
        if (!$admin) {
            throw new \Exception('No se pudo crear el usuario administrador');
        }

        // Asegurarnos de que el rol esté correctamente asignado
        DB::table('users')
            ->where('email', 'admin@aps.com')
            ->update(['role' => 'admin']);
    }
} 