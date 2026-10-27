<?php

namespace Tests\Feature;

use App\Enums\JenisInstitusi;
use App\Models\Fakultas;
use App\Models\Institusi;
use App\Models\User;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InstitusiResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_open_institusi_list_page(): void
    {
        Role::create(['name' => 'super_admin', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $institusi = Institusi::create([
            'nama' => 'SMK Negeri 1 Bedus',
            'jenis' => JenisInstitusi::Smk,
            'kecamatan' => 'Bojonegoro',
            'alamat' => 'Jl. Raya Bedus No. 1',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/admin/institusi')
            ->assertOk()
            ->assertSee('SMK Negeri 1 Bedus');

        $this->get('/admin/institusi/create')->assertOk();
        $this->get("/admin/institusi/{$institusi->id}/edit")->assertOk();
    }

    public function test_institusi_with_fakultas_or_jurusan_is_protected_from_deletion(): void
    {
        $institusi = Institusi::create([
            'nama' => 'Universitas Bedus',
            'jenis' => JenisInstitusi::Pt,
            'kecamatan' => 'Bojonegoro',
            'alamat' => 'Jl. Pendidikan No. 10',
            'is_active' => true,
        ]);

        $this->assertFalse($institusi->hasFakultasOrJurusan());

        Fakultas::create([
            'institusi_id' => $institusi->id,
            'nama' => 'Fakultas Teknik',
        ]);

        $this->assertTrue($institusi->hasFakultasOrJurusan());
    }

    public function test_hasil_import_modal_renders_completed_timestamp_safely(): void
    {
        $import = new Import([
            'file_name' => 'template-institusi.csv',
            'total_rows' => 2,
            'successful_rows' => 1,
            'completed_at' => now(),
        ]);

        $html = view('filament.actions.hasil-import', [
            'import' => $import,
            'failedRows' => collect(),
            'successfulRows' => collect(),
        ])->render();

        $expectedDate = Carbon::createFromTimestamp((int) $import->completed_at)
            ->translatedFormat('d M Y H:i');

        $this->assertStringContainsString('Selesai', $html);
        $this->assertStringContainsString($expectedDate, $html);
    }
}
