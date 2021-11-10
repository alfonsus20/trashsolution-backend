<?php

namespace App\Http\Controllers;

use App\Models\RiwayatPencairanSaldo;
use App\Models\Saldo;
use Illuminate\Http\Request;

class SaldoController extends Controller
{
    public function cairkanSaldo(Request $request)
    {
        $nominal = $request->nominal;
        $uang_elektronik = $request->uang_elektronik;

        $id_pengguna =  auth()->user()->id;
        $saldo = Saldo::where('id_pengguna', $id_pengguna)->first();

        if ($nominal > $saldo->nominal) {
            return response()->json(['success' => false, 'message' => 'Nominal pencairan tidak boleh lebih besar dari nominal sekarang'], 400);
        }

        $saldo->nominal = $saldo->nominal - $nominal;
        $saldo->save();

        $riwayat_pencairan = new RiwayatPencairanSaldo();
        $riwayat_pencairan->id_pengguna = $id_pengguna;
        $riwayat_pencairan->nominal = $nominal;
        $riwayat_pencairan->uang_elektronik = $uang_elektronik;
        $riwayat_pencairan->save();

        return response()->json(['success' => true, 'message' => 'Pencairan saldo berhasil']);
    }

    public function getRiwayatPencairanSaldo()
    {
        $riwayat_pencairan = RiwayatPencairanSaldo::where('id_pengguna', auth()->user()->id)->get();
        return response()->json(['success' => true, 'message' => 'Fetch riwayat pencairan saldo berhasil', 'data' => $riwayat_pencairan]);
    }
}
