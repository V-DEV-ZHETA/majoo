<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Elektronik',
            'Pakaian Pria',
            'Pakaian Wanita',
            'Pakaian Anak',
            'Sepatu',
            'Tas',
            'Aksesoris',
            'Olahraga',
            'Buku',
            'Kesehatan & Kecantikan',
        ];

        foreach ($categories as $name) {
            Category::create(['name' => $name]);
        }
    }
}

