<?php

namespace App\Http\Controllers;

use App\Events\PenjualanSampahPenggunaNotification;
use App\Models\Penjualan;
use App\Models\PenjualanSampah;
use App\Models\Saldo;
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

        if(!$info_penjualan){
            return response()->json(['success'=> false, 'message'=> "Penjualan sampah tidak ditemukan"], 404);
        }

        $pengguna = Penjualan::find($id_penjualan)->pengguna()->first();
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
        $current_status = $penjualan['status'];
        $trashpicker = Trashpicker::find(auth()->user()->id);

        if (!$penjualan) {
            return response()->json(['success' => false, 'message' => "Permintaan penjemputan tidak ditemukan"], 404);
        }

        if ($status == 'tunggu') {
            if ($current_status != 'mencari trashpicker') {
                return response()->json(['success' => false, 'message' => "Status hanya dapat diubah dari keadaan mencari trashpicker"], 400);
            }

            $trashpicker->availability = false;
            $trashpicker->save();

            $penjualan->status = 'menunggu trashpicker';
            $penjualan->id_trashpicker = auth()->user()->id;
            $penjualan->lat_trashpicker = auth()->user()->lat;
            $penjualan->long_trashpicker = auth()->user()->long;
            $penjualan->save();

            event(new PenjualanSampahPenggunaNotification($penjualan, "tunggu"));

            return response()->json(['success' => true, 'message' => "Ubah status ke menunggu trashpicker berhasil"]);
        } else if ($status == 'tiba') {
            if ($current_status != 'menunggu trashpicker') {
                return response()->json(['success' => false, 'message' => "Status hanya dapat diubah dari keadaan menunggu trashpicker"], 400);
            }
            $penjualan->status = 'tiba';
            $penjualan->save();

            event(new PenjualanSampahPenggunaNotification($penjualan, "tiba"));

            return response()->json(['success' => true, 'message' => "Ubah status ke tiba berhasil"]);
        } else if ($status == 'selesai') {
            if ($current_status != 'tiba') {
                return response()->json(['success' => false, 'message' => "Status hanya dapat diubah dari keadaan tiba"], 400);
            }

            $penjualan->status = 'selesai';
            $penjualan->save();

            $saldo = Saldo::where('id_pengguna', $penjualan->id_pengguna)->first();

            $saldo->nominal = $saldo->nominal + $penjualan->total_harga;
            $saldo->save();

            $trashpicker->availability = true;
            $trashpicker->save();

            event(new PenjualanSampahPenggunaNotification($penjualan, "selesai"));

            return response()->json(['success' => true, 'message' => "Ubah status ke selesai berhasil"]);
        } else {
            return response()->json(['success' => false, 'message' => "Status tidak ditemukan"], 404);
        }
    }

    public function editDataSampah(Request $request, $id)
    {
        // get penjualan
        $penjualan = Penjualan::find($id);


        // get data sampah
        $daftar_sampah = $request->daftar_sampah;

        // count & assign new total harga
        $total_harga = 0;

        foreach ($daftar_sampah as $sampah) {
            $found_sampah = Sampah::find($sampah['id_sampah']);
            $total_harga += $found_sampah->harga * $sampah['kuantitas'];
        }
        $penjualan->total_harga = $total_harga;
        $penjualan->save();

        // delete old data sampah in PenjualanSampah
        $id_penjualan = $penjualan->id;
        $penjualan_sampah = PenjualanSampah::where('id_penjualan', $id_penjualan);
        if ($penjualan_sampah) {
            $penjualan_sampah->delete();
        }

        // assign data sampah to PenjualanSampah
        $func = function ($sampah) use ($id_penjualan) {
            return ["id_penjualan" => $id_penjualan, 'id_sampah' => $sampah['id_sampah'], 'kuantitas' => $sampah['kuantitas']];
        };

        $daftar_sampah_with_id_penjualan = array_map($func, $daftar_sampah);

        PenjualanSampah::insert($daftar_sampah_with_id_penjualan);

        return response()->json(['success' => true, 'message' => 'Edit sampah berhasil']);
    }

    public function getRiwayatPenjualanSampah()
    {
        $daftar_penjualan = Penjualan::where('id_pengguna', auth()->user()->id)->where('status', 'selesai')->select('penjualan.id', 'penjualan.created_at as tanggal', 'trashpicker.nama as nama_trashpicker', 'lat_pengguna', 'long_pengguna', 'lat_trashpicker', 'long_trashpicker')->join('trashpicker', 'id_trashpicker', '=', 'trashpicker.id')->get();
        $structured = [];

        foreach ($daftar_penjualan as $penjualan) {
            $daftar_sampah = PenjualanSampah::where('id_penjualan', $penjualan->id)->join('sampah', 'id_sampah', '=', 'sampah.id')->get();
            $structured[] = ['penjualan' => $penjualan, 'daftar_sampah' => $daftar_sampah];
        }

        return response()->json(['success' => true, 'message' => 'fetch riwayat penjualan sampah berhasil', 'data' => $structured]);
    }

    public function getTrashpickerCurrentPenjemputan()
    {
        $penjemputan = Penjualan::where('id_trashpicker', auth()->user()->id)->where('status', '!=', 'mencari trashpicker')->where('status', '!=', 'selesai')->first();

        if ($penjemputan) {
            return response()->json(['success' => true, 'message' => 'trashpicker sedang menjemput sampah', 'data' => $penjemputan]);
        } else {
            return response()->json(['success' => true, 'message' => 'trashpicker sedang idle', 'data' => null]);
        }
    }

    public function getPenggunaCurrentPenjualan()
    {
        $penjualan = Penjualan::where('id_pengguna', auth()->user()->id)->where('status', '!=', 'selesai')->first();
        if ($penjualan) {
            $trashpicker = Trashpicker::find($penjualan->id_trashpicker);
            return response()->json(['success' => true, 'message' => 'pengguna sedang melakukan penjualan sampah', 'data' => ['penjualan' => $penjualan, 'trashpicker' => $trashpicker]]);
        } else {
            return response()->json(['success' => true, 'message' => 'pengguna tidak sedang melakukan penjualan sampah', 'data' => null]);
        }
    }
}
