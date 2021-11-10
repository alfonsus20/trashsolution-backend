<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\Saldo;
use App\Models\Trashpicker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Nexmo\Laravel\Facade\Nexmo;
use App\Models\OTP;

class AuthController extends Controller
{
    public function registerPengguna(Request $request)
    {
        $phone = $request->phone;

        $otp = OTP::where('phone', $phone)->first();

        if (!$otp || $otp->otp != $request->otp) {
            return response()->json(['success' => false, 'message' => 'Kode OTP salah!'], 400);
        } else {
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


            $saldo = new Saldo();
            $saldo->nominal = 0;
            $saldo->id_pengguna = $newPengguna->id;
            $saldo->save();

            return response()->json(['success' => true, 'message' => 'Register user berhasil']);
        }
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
        $phone = $request->phone;

        $otp = OTP::where('phone', $phone)->first();

        if ($otp->otp != $request->otp) {
            return response()->json(['success' => false, 'message' => 'Kode OTP salah!'], 400);
        } else {
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

    public function getPenggunaProfile()
    {
        return auth()->user();
    }

    public function getTrashpickerProfile()
    {
        return  auth()->user();
    }

    public function sendPhoneNumberOTP($phoneNumber)
    {
        $otp = (string) mt_rand(1000, 9999);

        // send otp sms
        Nexmo::message()->send([
            'to' => $phoneNumber,
            'from' => 'TRASHOLUTION',
            'text' => "OTP untuk registrasi Trasholution: $otp"
        ]);

        // check if phone already exist
        $tmp = OTP::where('phone', $phoneNumber)->first();

        if ($tmp) {
            $tmp->delete();
        }

        // add otp data to otp table
        $otpData = new OTP();
        $otpData->phone = $phoneNumber;
        $otpData->otp = $otp;
        $otpData->save();

        return response()->json(['success' => true, 'message' => 'SMS OTP terkirim!']);
    }

    public function verifyPhoneNumberOTP(Request $request)
    {
        if ($request->userInput == $request->otp) {
            $newTrashpicker  = new Trashpicker();
            $newTrashpicker->fill($request->inputs);
            $newTrashpicker->password = Hash::make($request->input('inputs.password'));
            $newTrashpicker->availability = false;

            $newTrashpicker->save();

            return response()->json(['success' => true, 'message' => 'Register trashpicker berhasil']);
        } else {
            return response()->json(['success' => false, 'message' => 'OTP salah']);
        }
    }

    public function updateLokasiPengguna(Request $request)
    {
        $pengguna = Pengguna::find(auth()->user()->id);
        $pengguna->lat = $request->lat;
        $pengguna->long = $request->long;
        $pengguna->save();
        return response()->json(['success' => true, 'message' => 'Update lokasi pengguna berhasil']);
    }

    public function updateLokasiTrashpicker(Request $request)
    {
        $trashpicker = Trashpicker::find(auth()->user()->id);
        $trashpicker->lat = $request->lat;
        $trashpicker->long = $request->long;
        $trashpicker->save();
        return response()->json(['success' => true, 'message' => 'Update lokasi trashpicker berhasil']);
    }
}
