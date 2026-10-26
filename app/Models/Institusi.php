<?php

namespace App\Models;

use App\Enums\JenisInstitusi;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Institusi extends Model
{
    use HasUuids;
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'institusi';

    protected $fillable = [
        'nama',
        'jenis',
        'kecamatan',
        'alamat',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'jenis' => JenisInstitusi::class,
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
            ->setDescriptionForEvent(fn (string $eventName): string => "{$eventName} institusi");
    }

    // --- Relasi ---

    /**
     * @return HasMany<Fakultas, $this>
     */
    public function fakultas(): HasMany
    {
        return $this->hasMany(Fakultas::class, 'institusi_id', 'id');
    }

    /**
     * @return HasMany<Jurusan, $this>
     */
    public function jurusan(): HasMany
    {
        return $this->hasMany(Jurusan::class, 'institusi_id', 'id');
    }

    /**
     * Cek apakah institusi masih dipakai oleh Fakultas atau Jurusan.
     */
    public function hasFakultasOrJurusan(): bool
    {
        return $this->fakultas()->exists() || $this->jurusan()->exists();
    }
}
