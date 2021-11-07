<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\Trashpicker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function registerPengguna(Request $request)
    {
        $rules = [
            'nama' => 'required|max:255',
            'email' => 'required|unique:pengguna|max:255',
            'password' => 'required',
            'phone' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        $inputs = $request->only([
            'phone',
            'email',
            'nama',
            'password'
        ]);

        $newPengguna  = new Pengguna();
        $newPengguna->fill($inputs);
        $newPengguna->password = Hash::make($inputs['password']);

        $newPengguna->save();

        return response()->json(['success' => true, 'message' => 'Register user berhasil']);
    }

    public function loginPengguna(Request $request)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        $inputs = $request->only([
            'email',
            'password'
        ]);

        if (Pengguna::where('email', $inputs['email'])->count() <= 0) {
            return response()->json(['success' => false, 'message' => 'Email pengguna tidak ditemukan'], 400);
        };

        $foundPengguna = Pengguna::where('email', $inputs['email'])->first();

        if (password_verify($inputs['password'], $foundPengguna->password)) {
            return response()->json(['success' => true, 'message' => 'Login pengguna berhasil', 'token' => $foundPengguna->createToken('Personal Access Token', ['pengguna'])->accessToken], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Password salah']);
        }
    }

    public function registerTrashpicker(Request $request)
    {
        $rules = [
            'nama' => 'required|max:255',
            'email' => 'required|unique:trashpicker|max:255',
            'password' => 'required',
            'phone' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        $inputs = $request->only([
            'nama',
            'phone',
            'email',
            'password'
        ]);

        $newTrashpicker  = new Trashpicker();
        $newTrashpicker->fill($inputs);
        $newTrashpicker->password = Hash::make($inputs['password']);
        $newTrashpicker->availability = false;

        $newTrashpicker->save();

        return response()->json(['success' => true, 'message' => 'Register trashpicker berhasil']);
    }

    public function loginTrashpicker(Request $request)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        $inputs = $request->only([
            'email',
            'password'
        ]);

        if (Trashpicker::where('email', $inputs['email'])->count() <= 0) {
            return response()->json(['success' => false, 'message' => 'Email trashpicker tidak ditemukan'], 400);
        };

        $foundTrashpicker = Trashpicker::where('email', $inputs['email'])->first();

        if (password_verify($inputs['password'], $foundTrashpicker->password)) {
            return response()->json(['success' => true, 'message' => 'Login trashpicker berhasil', 'token' => $foundTrashpicker->createToken('Personal Access Token', ['trashpicker'])->accessToken], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Password salah']);
        }
    }

    public function getPenggunaData(){
        return auth()->user();
    }
}
