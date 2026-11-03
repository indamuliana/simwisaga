<?php

namespace App\Modules\AnalitikSpmb\Models;

use Illuminate\Database\Eloquent\Model;

class MasterWilayah extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $primaryKey = 'kode';

    protected $keyType = 'string';

    protected $fillable = [
        'kode',
        'nama',
        'level',
        'parent_kode',
    ];
}
