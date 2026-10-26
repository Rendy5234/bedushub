<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Jurusan extends Model
{
    use HasUuids;
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'jurusan';

    protected $fillable = [
        'institusi_id',
        'nama',
        'kode',
    ];

    /**
     * Konfigurasi audit trail.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName): string => "{$eventName} jurusan");
    }

    // --- Relasi ---

    /**
     * @return BelongsTo<Institusi, $this>
     */
    public function institusi(): BelongsTo
    {
        return $this->belongsTo(Institusi::class, 'institusi_id', 'id');
    }
}
