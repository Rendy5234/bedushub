<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PelatihanMinat extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'pelatihan_minat';

    protected $fillable = [
        'pelatihan_id',
        'kategori_minat_id',
    ];

    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(Pelatihan::class, 'pelatihan_id', 'id');
    }

    public function kategoriMinat(): BelongsTo
    {
        return $this->belongsTo(KategoriMinat::class, 'kategori_minat_id', 'id');
    }
}
