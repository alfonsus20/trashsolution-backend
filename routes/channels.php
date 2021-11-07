<?php

use App\Models\Penjualan;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('penjualan-sampah.{id}', function ($user, $id) {
    return $user->id == Penjualan::find($id)->id_pengguna;
});

Broadcast::channel('penjualan-sampah-trashpicker.{id}', function ($user, $id) {
    return $user->id == Penjualan::find($id)->id_trashpicker;
});
