<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sampah;

class SampahController extends Controller
{
    public function getSampah()
    {
        $sampah = Sampah::all();
        return response()->json(['success' => true, 'message' => 'fetch sampah berhasil', 'data' => $sampah]);
    }
}
