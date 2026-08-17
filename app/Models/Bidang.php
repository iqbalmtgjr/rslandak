<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    protected $table = 'rssite_bidangs';

    protected $fillable = ['nama', 'urutan'];

    public function strukturs()
    {
        return $this->hasMany(StrukturOrganisasi::class, 'bidang_id');
    }
}
