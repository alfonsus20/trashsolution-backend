<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        DB::table('sampah')->insert([
            'nama' => 'Gelas plastik <120ml',
            'jenis' => 'Plastik',
            'ukuran' => '<120ml',
            'harga' => 50,
            'gambar' => ''
        ]);

        DB::table('sampah')->insert([
            'nama' => 'Gelas plastik 120-240ml',
            'jenis' => 'Plastik ',
            'ukuran' => '120-240ml',
            'harga' => 100,
            'gambar' => ''
        ]);

        DB::table('sampah')->insert([
            'nama' => 'Gelas plastik 240-360ml',
            'jenis' => 'Plastik',
            'ukuran' => '240-360ml',
            'harga' => 150,
            'gambar' => ''
        ]);

        DB::table('sampah')->insert([
            'nama' => 'Gelas plastik 360-500ml',
            'jenis' => 'Plastik',
            'ukuran' => '360-500ml',
            'harga' => 200,
            'gambar' => ''
        ]);

        DB::table('sampah')->insert([
            'nama' => 'Botol plastik <350ml',
            'jenis' => 'Plastik',
            'ukuran' => '<350ml',
            'harga' => 200,
            'gambar' => ''
        ]);

        DB::table('sampah')->insert([
            'nama' => 'Botol plastik 350-750ml',
            'jenis' => 'Plastik',
            'ukuran' => '350-750ml',
            'harga' => 300,
            'gambar' => ''
        ]);

        DB::table('sampah')->insert([
            'nama' => 'Botol plastik 750-1200ml',
            'jenis' => 'Plastik',
            'ukuran' => '750-1200ml',
            'harga' => 400,
            'gambar' => ''
        ]);

        DB::table('sampah')->insert([
            'nama' => 'Gelas plastik 1200-2000ml',
            'jenis' => 'Plastik',
            'ukuran' => '1200-2000ml',
            'harga' => 500,
            'gambar' => ''
        ]);

        DB::table('sampah')->insert([
            'nama' => 'Kertas koran 100g',
            'jenis' => 'Kertas',
            'ukuran' => '100g',
            'harga' => 100,
            'gambar' => ''
        ]);

        DB::table('sampah')->insert([
            'nama' => 'Kertas koran 500g',
            'jenis' => 'Kertas',
            'ukuran' => '500g',
            'harga' => 500,
            'gambar' => ''
        ]);

        DB::table('sampah')->insert([
            'nama' => 'Kertas koran 1kg',
            'jenis' => 'Kertas',
            'ukuran' => '1kg',
            'harga' => 1000,
            'gambar' => ''
        ]);
    }
}
