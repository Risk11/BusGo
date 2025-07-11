<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function bus()
    {
        return $this->belongsTo(Bus::class, 'id');
    }

    public function kursis()
    {
        return $this->hasMany(Kursi::class, 'id');
    }

    public function tikets()
    {
        return $this->hasMany(Tiket::class, 'id');
    }
}
