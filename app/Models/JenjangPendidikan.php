<?php

namespace App\Models;

use Database\Factories\JenjangPendidikanFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class JenjangPendidikan extends Model
{
    /** @use HasFactory<JenjangPendidikanFactory> */
    use HasFactory, HasUuids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'kode',
        'nama',
        'kelompok',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    /**
     * Relasi ke ProgramStudi
     */
    /**
     * @return HasMany<ProgramStudi, $this>
     */
    public function programStudi(): HasMany
    {
        return $this->hasMany(ProgramStudi::class, 'jenjang', 'kode');
    }

    /**
     * Mendapatkan semua jenjang aktif (untuk dropdown create)
     *
     * @return Collection<int, JenjangPendidikan>
     */
    public static function getActiveOptions(): Collection
    {
        return self::where('is_active', true)
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get();
    }

    /**
     * Mendapatkan opsi termasuk jenjang non-aktif yang sedang dipakai record tertentu
     *
     * @param  array<string>  $currentKodes  Kode jenjang yang sedang dipakai (misal dari record yang diedit)
     * @return Collection<int, JenjangPendidikan>
     */
    public static function getOptionsIncludingInactive(array $currentKodes = []): Collection
    {
        $query = self::query()
            ->orderBy('urutan')
            ->orderBy('nama');

        if (! empty($currentKodes)) {
            $query->where(function ($q) use ($currentKodes) {
                $q->where('is_active', true)
                    ->orWhereIn('kode', $currentKodes);
            });
        } else {
            $query->where('is_active', true);
        }

        return $query->get();
    }

    /**
     * Cek apakah jenjang ini dipakai oleh ProgramStudi
     */
    public function isUsedByProgramStudi(): bool
    {
        return $this->programStudi()->exists();
    }
}
