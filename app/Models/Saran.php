<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saran extends Model
{
    protected $table = 'rssite_saran';

    protected $fillable = ['tipe', 'pesan'];
}
