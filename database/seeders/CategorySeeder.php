<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Alimentos', 'description' => 'Productos alimenticios y bebidas'],
            ['name' => 'Ropa', 'description' => 'Prendas de vestir y accesorios'],
            ['name' => 'Hogar', 'description' => 'Artículos para el hogar y decoración'],
            ['name' => 'Tecnología', 'description' => 'Dispositivos electrónicos y accesorios'],
            ['name' => 'Belleza', 'description' => 'Productos de belleza y cuidado personal'],
            ['name' => 'Deportes', 'description' => 'Artículos deportivos y fitness'],
            ['name' => 'Juguetes', 'description' => 'Juguetes y juegos'],
            ['name' => 'Libros', 'description' => 'Libros y material educativo'],
            ['name' => 'Servicios', 'description' => 'Servicios profesionales y personales'],
            ['name' => 'Otros', 'description' => 'Otras categorías de productos'],
            ['name' => 'Bebidas', 'description' => 'Gaseosas y bebidas alcohólicas'],
            ['name' => 'Cuidado personal', 'description' => 'Cuidado personal y cosmética'],
            ['name' => 'Electrodomésticos', 'description' => 'Electrodomésticos y aparatos electrónicos'],
            ['name' => 'Herramientas', 'description' => 'Herramientas y equipos'],
            ['name' => 'Jardín', 'description' => 'Artículos para el jardín y el hogar'],
            ['name' => 'Muebles', 'description' => 'Muebles y decoración'],
            ['name' => 'Salud', 'description' => 'Productos de salud y bienestar'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
