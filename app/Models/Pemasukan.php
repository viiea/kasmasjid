<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    use HasFactory;
    protected $table = 'pemasukans';
    
    protected $fillable = [
        'upload',
        'tanggal_pemasukan',
        'jumlah_pemasukan',
        'id_jenis',
        'id_donatur'
    ];


    public function donatur()
    {
        return $this->belongsTo(Donatur::class, 'id_donatur');
    }

    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'id_jenis');
    }
}
