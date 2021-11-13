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
        DB::table('sampah')->insert([
            'nama' => 'Karton/kardus 1kg',
            'jenis' => 'Kertas',
            'ukuran' => '1kg',
            'harga' => 1500,
            'gambar' => ''
        ]);

        DB::table('sampah')->insert([
            'nama' => 'Kaleng alumunium <200g',
            'jenis' => 'Logam',
            'ukuran' => '<200ml',
            'harga' => 150,
            'gambar' => ''
        ]);

        DB::table('sampah')->insert([
            'nama' => 'Kaleng alumunium 200-500ml',
            'jenis' => 'Logam',
            'ukuran' => '200-500ml',
            'harga' => 200,
            'gambar' => ''
        ]);

        DB::table('sampah')->insert([
            'nama' => 'Kaleng alumunium 500-800ml',
            'jenis' => 'Logam',
            'ukuran' => '500-800ml',
            'harga' => 300,
            'gambar' => ''
        ]);

        DB::table('sampah')->insert([
            'nama' => 'Kaleng alumunium 800-1000ml',
            'jenis' => 'Logam',
            'ukuran' => '800-1000ml',
            'harga' => 500,
            'gambar' => ''
        ]);
    }
}
