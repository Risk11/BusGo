<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function penumpang()
    {
        return $this->belongsTo(Penumpang::class, 'id');
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'id');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id');
    }
}
