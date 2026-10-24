<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Skill extends Model
{
    use HasUuids;
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'skills';

    protected $fillable = [
        'nama',
        'kategori',
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
            ->setDescriptionForEvent(fn (string $eventName): string => "{$eventName} skill");
    }

    // --- Relasi ---

    public function userSkills(): HasMany
    {
        return $this->hasMany(UserSkill::class, 'skill_id', 'id');
    }

    public function lowonganSkills(): HasMany
    {
        return $this->hasMany(LowonganSkill::class, 'skill_id', 'id');
    }

    public function pelatihanSkills(): HasMany
    {
        return $this->hasMany(PelatihanSkill::class, 'skill_id', 'id');
    }
}
