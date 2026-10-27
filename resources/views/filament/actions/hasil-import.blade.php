<style>
    .custom-modal-text { color: #1f2937; }
    .dark .custom-modal-text { color: #f3f4f6 !important; }
</style>
<div class="custom-modal-text" style="padding: 20px 8px; font-size: 14px; line-height: 1.5;">
    @if(! $import || ! $import->completed_at)
        <p style="font-size: 14px; color: #9ca3af;">
            Belum ada riwayat import institusi.
        </p>
    @else
        @php
            $completedDate = $import->completed_at
                ? \Illuminate\Support\Carbon::parse($import->completed_at)->translatedFormat('d M Y H:i')
                : '-';
        @endphp

        <div style="display: flex; gap: 20px; margin-bottom: 24px; flex-wrap: wrap;">
            <div>
                <div style="font-size: 11px; color: #9ca3af; margin-bottom: 4px;">Waktu Selesai</div>
                <div style="font-size: 15px; font-weight: 600;">{{ $completedDate }}</div>
            </div>
            <div>
                <div style="font-size: 11px; color: #9ca3af; margin-bottom: 4px;">Total Baris</div>
                <div style="font-size: 15px; font-weight: 600;">
                    {{ number_format((float) $import->total_rows, 0, ',', '.') }}
                </div>
            </div>
            <div>
                <div style="font-size: 11px; color: #9ca3af; margin-bottom: 4px;">Berhasil</div>
                <div style="font-size: 15px; font-weight: 600;">
                    {{ number_format((float) $import->successful_rows, 0, ',', '.') }}
                </div>
            </div>
            <div>
                <div style="font-size: 11px; color: #9ca3af; margin-bottom: 4px;">Gagal</div>
                <div style="font-size: 15px; font-weight: 600;">
                    {{ number_format((float) ($import->total_rows - $import->successful_rows), 0, ',', '.') }}
                </div>
            </div>
        </div>

        @if($failedRows && $failedRows->isNotEmpty())
            <div style="margin-top: 16px;">
                <h3 style="font-size: 15px; font-weight: 600; margin-bottom: 12px;">Data Gagal</h3>

                {{-- Scroll ke samping di layar kecil (mobile) --}}
                <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                    <table style="width: 100%; min-width: 600px; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr>
                                <th style="padding: 10px 12px; border-bottom: 1px solid rgba(107, 114, 128, 0.35); text-align: left; font-weight: 600; width: 40%;">Data Asli</th>
                                <th style="padding: 10px 12px; border-bottom: 1px solid rgba(107, 114, 128, 0.35); text-align: left; font-weight: 600; width: 60%;">Pesan Error</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($failedRows as $row)
                                <tr>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid rgba(107, 114, 128, 0.35); text-align: left; vertical-align: top; word-break: break-all;">
                                        {{ array_values($row->data ?? [])[0] ?? '-' }}
                                    </td>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid rgba(107, 114, 128, 0.35); text-align: left; vertical-align: top;">
                                        <ul style="margin: 0; font-size: 13px; line-height: 1.6;">
                                            <li>{{ $row->validation_error ?? 'Validasi gagal.' }}</li>
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Navigasi halaman (server-side, tanpa Alpine.js).
                     Menggunakan LengthAwarePaginator standar Laravel: onFirstPage(),
                     hasMorePages(), previousPageUrl()/nextPageUrl(). --}}
                @php
                    $navStyle = 'display: inline-block; padding: 6px 14px; border: 1px solid rgba(107, 114, 128, 0.45); border-radius: 6px; font-size: 13px; font-weight: 500; text-decoration: none; color: inherit;';
                    $navDisabledStyle = $navStyle.' opacity: .45; cursor: not-allowed;';
                @endphp

                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-top: 16px; flex-wrap: wrap;">
                    @if($failedRows->onFirstPage())
                        <span style="{{ $navDisabledStyle }}" aria-disabled="true">Sebelumnya</span>
                    @else
                        <a
                            href="{{ $failedRows->previousPageUrl() }}"
                            wire:click.prevent="previousHasilImportPage"
                            style="{{ $navStyle }}">Sebelumnya</a>
                    @endif

                    <span style="font-size: 12px; color: #9ca3af;">
                        Halaman {{ $failedRows->currentPage() }} dari {{ $failedRows->lastPage() }}
                        &middot;
                        Menampilkan {{ $failedRows->firstItem() ?? 0 }}-{{ $failedRows->lastItem() ?? 0 }}
                        dari {{ $failedRows->total() }} baris
                    </span>

                    @if($failedRows->hasMorePages())
                        <a
                            href="{{ $failedRows->nextPageUrl() }}"
                            wire:click.prevent="nextHasilImportPage"
                            style="{{ $navStyle }}">Berikutnya</a>
                    @else
                        <span style="{{ $navDisabledStyle }}" aria-disabled="true">Berikutnya</span>
                    @endif
                </div>
            </div>
        @else
            <p style="font-size: 14px; color: #9ca3af;">
                Tidak ada data gagal.
            </p>
        @endif
    @endif
</div>
