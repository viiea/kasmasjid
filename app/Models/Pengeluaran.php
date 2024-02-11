<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;
    protected $table = 'pengeluarans';

    protected $fillable = [
        'upload','tanggal_pengeluaran','jumlah_pengeluaran',
        'id_jenis'
    ];

    public function jenis()
    {
        return $this->belongsTo(Jenis::class,'id_jenis');
    }

}
