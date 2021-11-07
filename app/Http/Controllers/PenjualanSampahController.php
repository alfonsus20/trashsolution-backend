<?php

namespace App\Http\Controllers;

use App\Events\PenjualanSampahPenggunaNotification;
use App\Models\Pengguna;
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

        $daftar_sampah = $request->daftar_sampah;

        $penjualan = new Penjualan();
        $penjualan->lat_pengguna = auth()->user()->lat;
        $penjualan->long_pengguna =  auth()->user()->long;
        $penjualan->lat_trashpicker = 0;
        $penjualan->long_trashpicker = 0;
        $penjualan->status = "mencari trashpicker";
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


    public function getDaftarPermintaanPenjemputan()
    {
        $daftar_permintaan_penjemputan = Penjualan::select("penjualan.id", "nama")->join("pengguna", "id_pengguna", "=", "pengguna.id")->where("status", "mencari trashpicker")->get();
        return response()->json(['success' => true, 'message' => 'Fetch daftar permintaan berhasil', 'data' => $daftar_permintaan_penjemputan]);
    }

    public function getDetailPermintaanPenjemputan($id_penjualan)
    {
        $info_penjualan = Penjualan::find($id_penjualan);
        $pengguna = Penjualan::find($id_penjualan)->pengguna()->get();
        $penjualan_sampah = PenjualanSampah::where("id_penjualan", $id_penjualan)->get();

        $penjualan_sampah_with_sampah = [];

        foreach ($penjualan_sampah as $penjualan) {
            $sampah = Sampah::find($penjualan->id_sampah);
            $penjualan_sampah_with_sampah[] = ["kuantitas" => $penjualan->kuantitas, "sampah" => $sampah];
        }

        return response()->json(['success' => true, 'message' => 'Fetch detail permintaan penjemputan berhasil', 'data' => ["penjualan" => $info_penjualan, "pengguna" => $pengguna, "daftar_sampah" => $penjualan_sampah_with_sampah]]);
    }

    public function ubahStatusPenjualan($id, $status)
    {
        $penjualan = Penjualan::find($id);
        // check perubahan status masih perlu ditambahkan

        if ($status == 'tunggu') {
            $penjualan->status = 'menunggu trashpicker';
            $penjualan->id_trashpicker = auth()->user()->id;
            $penjualan->lat_trashpicker = auth()->user()->lat;
            $penjualan->long_trashpicker = auth()->user()->long;
            $penjualan->save();

            event(new PenjualanSampahPenggunaNotification($penjualan, "Update status ke menunggu trashpicker"));

            return response()->json(['success' => true, 'message' => "Ubah status ke menunggu trashpicker berhasil"]);
        } else if ($status == 'tiba') {
            $penjualan->status = 'tiba';
            $penjualan->save();

            event(new PenjualanSampahPenggunaNotification($penjualan, "Update status ke tiba di lokasi"));
            return response()->json(['success' => true, 'message' => "Ubah status ke tiba berhasil"]);
        } else if ($status == 'selesai') {
            $penjualan->status = 'selesai';
            $penjualan->save();

            // kurang tambah saldo di sini

            event(new PenjualanSampahPenggunaNotification($penjualan, "Update status ke selesai"));
            return response()->json(['success' => true, 'message' => "Ubah status ke selesai berhasil"]);
        } else {
            return response()->json(['success' => false, 'message' => "Status tidak ditemukan"], 400);
        }
    }
}