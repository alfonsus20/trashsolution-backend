<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PenjualanSampah;
use App\Models\Sampah;
use App\Models\Trashpicker;
use Illuminate\Http\Request;

class PenjualanSampahController extends Controller
{

    public function jualSampah(Request $request)
    {
        $available_trashpicker_count = Trashpicker::where("availability", true)->count();

        if ($available_trashpicker_count == 0) {
            return response()->json(['success' => false, "message" => "No trashpickers available"], 404);
        }

        $inputs = $request->only(['lat_pengguna', 'long_pengguna', 'daftar_sampah']);
        $daftar_sampah = $request->daftar_sampah;

        $penjualan = new Penjualan();
        $penjualan->lat_pengguna = $inputs['lat_pengguna'];
        $penjualan->long_pengguna = $inputs['long_pengguna'];
        $penjualan->lat_trashpicker = 0;
        $penjualan->long_trashpicker = 0;
        $penjualan->status = false;
        $penjualan->id_pengguna = auth()->id();

        $total_harga = 0;

        foreach ($daftar_sampah as $sampah) {
            $found_sampah = Sampah::find($sampah['id_sampah']);
            $total_harga += $found_sampah->harga * $sampah['kuantitas'];
        }
        $penjualan->total_harga = $total_harga;
        $penjualan->save();

        $id_penjualan = $penjualan->id;

        $func = function ($sampah) use ($id_penjualan) {
            return ["id_penjualan" => $id_penjualan, 'id_sampah' => $sampah['id_sampah'], 'kuantitas' => $sampah['kuantitas']];
        };

        $daftar_sampah_with_id_penjualan = array_map($func, $daftar_sampah);

        PenjualanSampah::insert($daftar_sampah_with_id_penjualan);

        return response()->json(['success' => true, 'message' => 'Penjualan Sampah Berhasil. Sedang mencari trashpicker']);
    }
}
