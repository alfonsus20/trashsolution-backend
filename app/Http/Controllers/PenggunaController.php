<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index(){
        return response()->json(['message'=> 'pengguna index']);
    }
}
