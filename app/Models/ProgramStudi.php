<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ProgramStudi extends Model
{
    use HasUuids;
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'program_studi';

    protected $fillable = [
        'fakultas_id',
        'nama',
        'kode',
        'jenjang',
    ];

    /**
     * Konfigurasi audit trail.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName): string => "{$eventName} program studi");
    }

    // --- Relasi ---

    /**
     * @return BelongsTo<Fakultas, $this>
     */
    public function fakultas(): BelongsTo
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id', 'id');
    }
}
