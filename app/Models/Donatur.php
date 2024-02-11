<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donatur extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'donaturs';
    
    protected $fillable = [
        'nama','alamat','no_telephone','upload'
    ];

    public function pengeluaran(){
        return $this->hasMany(Pengeluaran::class);
    }

    public function pemasukan(){
        return $this->hasMany(Pemasukan::class);
    }
}
