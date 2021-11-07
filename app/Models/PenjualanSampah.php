<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanSampah extends Model
{
    use HasFactory;
    protected $table = 'penjualan_sampah';

    public function penjualan()
    {
        return $this->belongsToMany(Penjualan::class, 'id_penjualan');
    }

    public function sampah()
    {
        return $this->hasMany(Sampah::class, 'id_sampah');
    }
}
