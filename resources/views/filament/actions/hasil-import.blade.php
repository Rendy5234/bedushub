@php
    /** @var \Filament\Actions\Imports\Models\Import|null $import */
    /** @var \Illuminate\Support\Collection<int, \Filament\Actions\Imports\Models\FailedImportRow>|null $failedRows */

    // Kolom completed_at pada model Import memakai cast "timestamp", sehingga bertipe int (Unix timestamp), bukan Carbon.
    $completedAt = $import?->completed_at !== null
        ? \Illuminate\Support\Carbon::createFromTimestamp((int) $import->completed_at)
        : null;
@endphp

<div style="display: flex; flex-direction: column; gap: 1.25rem;">
    @if ($import === null)
        <p style="font-size: 0.875rem; line-height: 1.5; color: #6b7280;">
            Belum ada riwayat import institusi. Silakan gunakan tombol <strong>Import Institusi</strong> untuk
            mengunggah berkas CSV terlebih dahulu.
        </p>
    @else
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
            <div style="display: flex; flex-direction: column; gap: 0.125rem; min-width: 0;">
                <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280;">
                    Berkas
                </span>
                <span style="font-size: 0.875rem; font-weight: 600; color: #111827; word-break: break-all;">
                    {{ $import->file_name }}
                </span>
            </div>

            @if ($import->completed_at === null)
                <span style="flex-shrink: 0; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background-color: #fef3c7; color: #92400e;">
                    Sedang diproses
                </span>
            @else
                <span style="flex-shrink: 0; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background-color: #d1fae5; color: #065f46;">
                    Selesai
                </span>
            @endif
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
            <tbody>
                <tr>
                    <td style="padding: 0.5rem 0; color: #6b7280; width: 55%;">Waktu Selesai</td>
                    <td style="padding: 0.5rem 0; text-align: right; font-weight: 600; color: #111827;">
                        {{ $completedAt?->translatedFormat('d M Y H:i') ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; color: #6b7280; border-top: 1px solid #e5e7eb;">Total Baris</td>
                    <td style="padding: 0.5rem 0; text-align: right; font-weight: 600; color: #111827; border-top: 1px solid #e5e7eb;">
                        {{ number_format($import->total_rows, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; color: #6b7280; border-top: 1px solid #e5e7eb;">Berhasil</td>
                    <td style="padding: 0.5rem 0; text-align: right; font-weight: 600; color: #047857; border-top: 1px solid #e5e7eb;">
                        {{ number_format($import->successful_rows, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; color: #6b7280; border-top: 1px solid #e5e7eb;">Gagal</td>
                    <td style="padding: 0.5rem 0; text-align: right; font-weight: 600; color: {{ $import->getFailedRowsCount() > 0 ? '#b91c1c' : '#111827' }}; border-top: 1px solid #e5e7eb;">
                        {{ number_format($import->getFailedRowsCount(), 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        @if ($failedRows !== null && $failedRows->isNotEmpty())
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <span style="font-size: 0.875rem; font-weight: 600; color: #111827;">
                    Baris Gagal (maksimal 10 terakhir)
                </span>

                <div style="max-height: 16rem; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 0.5rem;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.75rem;">
                        <thead>
                            <tr style="background-color: #f9fafb; text-align: left;">
                                <th style="padding: 0.5rem; color: #6b7280; font-weight: 600; width: 45%;">Data</th>
                                <th style="padding: 0.5rem; color: #6b7280; font-weight: 600;">Kesalahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($failedRows as $failedRow)
                                <tr>
                                    <td style="padding: 0.5rem; border-top: 1px solid #e5e7eb; color: #374151; word-break: break-word;">
                                        {{ json_encode($failedRow->data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}
                                    </td>
                                    <td style="padding: 0.5rem; border-top: 1px solid #e5e7eb; color: #b91c1c; word-break: break-word;">
                                        {{ $failedRow->validation_error ?? 'Tidak diketahui' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif
</div>
