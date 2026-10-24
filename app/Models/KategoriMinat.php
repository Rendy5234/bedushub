<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class KategoriMinat extends Model
{
    use HasUuids;
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'kategori_minat';

    protected $fillable = [
        'nama',
        'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Konfigurasi audit trail.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName): string => "{$eventName} kategori minat");
    }

    // --- Relasi ---

    public function userMinat(): HasMany
    {
        return $this->hasMany(UserMinat::class, 'kategori_minat_id', 'id');
    }

    public function lowonganMinat(): HasMany
    {
        return $this->hasMany(LowonganMinat::class, 'kategori_minat_id', 'id');
    }

    public function pelatihanMinat(): HasMany
    {
        return $this->hasMany(PelatihanMinat::class, 'kategori_minat_id', 'id');
    }
}
