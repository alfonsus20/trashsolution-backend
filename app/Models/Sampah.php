<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sampah extends Model
{
    use HasFactory;

    protected $table = 'sampah';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'nama',
        'jenis',
        'ukuran',
        'harga',
        'gambar'
    ];

    public function penjualanSampah()
    {
        return $this->belongsToMany(PenjualanSampah::class);
    }
}
