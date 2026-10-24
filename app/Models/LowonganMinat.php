<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LowonganMinat extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'lowongan_minat';

    protected $fillable = [
        'lowongan_id',
        'kategori_minat_id',
    ];

    public function lowongan(): BelongsTo
    {
        return $this->belongsTo(Lowongan::class, 'lowongan_id', 'id');
    }

    public function kategoriMinat(): BelongsTo
    {
        return $this->belongsTo(KategoriMinat::class, 'kategori_minat_id', 'id');
    }
}
