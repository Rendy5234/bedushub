@php
    /** @var \Filament\Actions\Imports\Models\Import|null $import */
    /** @var \Illuminate\Support\Collection<int, mixed> $failedRows */
    /** @var \Illuminate\Support\Collection<int, array{nama: string, jenis: string, kecamatan: string}> $successfulRows */
@endphp

@if(! $import || ! $import->completed_at)
    <div style="padding: 16px; text-align: center;">
        <p style="font-size: 14px; color: #6b7280;">
            Belum ada riwayat import institusi.
        </p>
    </div>
@else
    {{-- === Statistik Ringkas === --}}
    @php
        $completedDate = $import->completed_at
            ? \Illuminate\Support\Carbon::parse($import->completed_at)->translatedFormat('d M Y H:i')
            : '-';
    @endphp
    <div style="display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap;">
        <div>
            <div style="font-size: 11px; color: #6b7280; margin-bottom: 4px;">Waktu Selesai</div>
            <div style="font-size: 15px; font-weight: 600; color: #111827;">{{ $completedDate }}</div>
        </div>
        <div>
            <div style="font-size: 11px; color: #6b7280; margin-bottom: 4px;">Total Baris</div>
            <div style="font-size: 15px; font-weight: 600; color: #111827;">
                {{ number_format((float) $import->total_rows, 0, ',', '.') }}
            </div>
        </div>
        <div>
            <div style="font-size: 11px; color: #6b7280; margin-bottom: 4px;">Berhasil</div>
            <div style="font-size: 15px; font-weight: 600; color: #059e37;">
                {{ number_format((float) $import->successful_rows, 0, ',', '.') }}
            </div>
        </div>
        <div>
            <div style="font-size: 11px; color: #6b7280; margin-bottom: 4px;">Gagal</div>
            <div style="font-size: 15px; font-weight: 600; color: #dc2626;">
                {{ number_format((float) ($import->total_rows - $import->successful_rows), 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- === Data Berhasil Diproses === --}}
    <div style="margin-top: 24px;">
        <h3 style="font-size: 15px; font-weight: 600; color: #111827; margin-bottom: 12px;">
            Data Berhasil Diproses
        </h3>

        @if($successfulRows->isNotEmpty())
            <div style="max-height: 300px; overflow-y: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr>
                            <th style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; text-align: left; font-weight: 600; color: #374151; background-color: #f9fafb;">Nama</th>
                            <th style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; text-align: left; font-weight: 600; color: #374151; background-color: #f9fafb;">Jenis</th>
                            <th style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; text-align: left; font-weight: 600; color: #374151; background-color: #f9fafb;">Kecamatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($successfulRows as $row)
                            <tr>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; text-align: left; color: #111827;">{{ $row['nama'] }}</td>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; text-align: left; color: #111827;">{{ $row['jenis'] }}</td>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; text-align: left; color: #111827;">{{ $row['kecamatan'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="font-size: 14px; color: #6b7280;">
                Belum ada data berhasil.
            </p>
        @endif
    </div>

    {{-- === Data Gagal === --}}
    <div style="margin-top: 24px;">
        @if($failedRows->isNotEmpty())
            <h3 style="font-size: 15px; font-weight: 600; color: #111827; margin-bottom: 12px;">Data Gagal</h3>
            <div style="max-height: 300px; overflow-y: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr>
                            <th style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; text-align: left; font-weight: 600; color: #374151; background-color: #f9fafb;">Data Asli</th>
                            <th style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; text-align: left; font-weight: 600; color: #374151; background-color: #f9fafb;">Pesan Error</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($failedRows as $failedRow)
                            <tr>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; text-align: left; color: #111827; vertical-align: top;">
                                    @php
                                        $data = is_array($failedRow->data)
                                            ? $failedRow->data
                                            : json_decode((string) $failedRow->data, true);
                                    @endphp
                                    <pre style="white-space: pre-wrap; font-size: 13px; color: #111827; margin: 0;">{{ json_encode($data, JSON_PRETTY_PRINT) }}</pre>
                                </td>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; text-align: left; color: #111827; vertical-align: top;">
                                    <p style="font-size: 14px; color: #111827; margin: 0;">{{ $failedRow->validation_error ?? 'Validasi gagal.' }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="font-size: 14px; color: #6b7280;">
                Tidak ada data gagal.
            </p>
        @endif
    </div>
@endif
