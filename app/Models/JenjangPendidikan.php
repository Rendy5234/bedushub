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

    /**
     * Mendapatkan opsi jenis institusi berdasarkan status aktif jenjang pendidikan
     *
     * @param  string|null  $currentJenis  Jenis yang sedang dipilih (untuk edit data lama)
     * @return array<string, string> Array dengan key = kode jenis, value = label
     */
    public static function getInstitusiJenisOptions(?string $currentJenis = null): array
    {
        $options = [];

        // Cek SD
        $sd = self::where('kode', 'SD')->where('is_active', true)->first();
        if ($sd) {
            $options['SD'] = $sd->nama; // 'SD/MI'
        }

        // Cek SMP
        $smp = self::where('kode', 'SMP')->where('is_active', true)->first();
        if ($smp) {
            $options['SMP'] = $smp->nama; // 'SMP/MTs'
        }

        // Cek SMA
        $sma = self::where('kode', 'SMA')->where('is_active', true)->first();
        if ($sma) {
            $options['SMA'] = $sma->nama; // 'SMA/MA'
        }

        // Cek SMK
        $smk = self::where('kode', 'SMK')->where('is_active', true)->first();
        if ($smk) {
            $options['SMK'] = $smk->nama; // 'SMK/MAK'
        }

        // Cek PT (minimal ada satu jenjang tinggi yang aktif: D1, D2, D3, D4, S1, S2, S3)
        $ptActive = self::whereIn('kode', ['D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'])
            ->where('is_active', true)
            ->exists();
        if ($ptActive) {
            $options['PT'] = 'Perguruan Tinggi';
        }

        // Edge case: jika currentJenis ada tapi tidak di daftar aktif, tambahkan dengan label (Non-Aktif)
        if ($currentJenis && ! isset($options[$currentJenis])) {
            $jenjang = self::where('kode', $currentJenis)->first();
            if ($jenjang) {
                $options[$currentJenis] = $jenjang->nama.' (Non-Aktif)';
            } else {
                // Fallback jika kode tidak ditemukan di database
                $options[$currentJenis] = $currentJenis.' (Non-Aktif)';
            }
        }

        return $options;
    }

    /**
     * Mendapatkan opsi jenjang untuk Program Studi (hanya Perguruan Tinggi: D1-D4, S1-S3)
     *
     * @param  string|null  $currentKode  Kode jenjang yang sedang dipilih (untuk edit data lama)
     * @return array<string, string> Array dengan key = kode, value = label (nama)
     */
    public static function getProdiJenjangOptions(?string $currentKode = null): array
    {
        $kodeTinggi = ['D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'];

        $options = self::whereIn('kode', $kodeTinggi)
            ->where('is_active', true)
            ->orderBy('urutan')
            ->orderBy('nama')
            ->pluck('nama', 'kode')
            ->toArray();

        // Edge case: jika currentKode ada tapi tidak di daftar aktif (sudah dimatikan),
        // tambahkan dengan label (Non-Aktif)
        if ($currentKode && ! isset($options[$currentKode])) {
            $jenjang = self::where('kode', $currentKode)->first();
            if ($jenjang) {
                $options[$currentKode] = $jenjang->nama.' (Non-Aktif)';
            } else {
                // Fallback jika kode tidak ditemukan di database
                $options[$currentKode] = $currentKode.' (Non-Aktif)';
            }
        }

        return $options;
    }
}
