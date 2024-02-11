<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    use HasFactory;
    protected $table = 'jenis';
    protected $fillable = [
        'status', 'nama'
    ];

    public function pengeluaran(){
        return $this->hasMany(Pengeluaran::class);
    }

    public function pemasukan(){
        return $this->hasMany(Pemasukan::class);
    }
    
    
}
