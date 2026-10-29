<?php

namespace Tests\Feature;

use App\Filament\Imports\ProgramStudiImporter;
use App\Models\Fakultas;
use App\Models\Institusi;
use App\Models\ProgramStudi;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramStudiImporterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Siapkan data institusi + fakultas untuk kebutuhan relasi program studi.
     */
    private function createFakultas(string $namaFakultas = 'Fakultas Teknik'): Fakultas
    {
        $institusi = Institusi::create([
            'nama' => 'Universitas Bedus',
            'jenis' => 'PT',
            'kecamatan' => 'Bojonegoro',
            'alamat' => 'Jl. Pendidikan No. 10',
            'is_active' => true,
        ]);

        return Fakultas::create([
            'nama' => $namaFakultas,
            'institusi_id' => $institusi->id,
        ]);
    }

    public function test_it_imports_new_program_studi_with_fakultas_id_resolved_from_name(): void
    {
        $fakultas = $this->createFakultas();

        ProgramStudiImporter::test()
            ->import([
                'nama' => 'Teknik Informatika',
                'kode' => 'IF001',
                'jenjang' => 'S1',
                'fakultas_id' => 'Fakultas Teknik',
            ])
            ->assertImported();

        $this->assertDatabaseHas('program_studi', [
            'nama' => 'Teknik Informatika',
            'kode' => 'IF001',
            'jenjang' => 'S1',
            'fakultas_id' => $fakultas->id,
        ]);
    }

    public function test_it_imports_program_studi_without_kode(): void
    {
        $this->createFakultas();

        ProgramStudiImporter::test()
            ->import([
                'nama' => 'Teknik Sipil',
                'kode' => '',
                'jenjang' => 'S1',
                'fakultas_id' => 'Fakultas Teknik',
            ])
            ->assertImported();

        $this->assertDatabaseHas('program_studi', [
            'nama' => 'Teknik Sipil',
            'kode' => null,
        ]);
    }

    public function test_it_restores_soft_deleted_program_studi_with_the_same_name_and_fakultas(): void
    {
        $fakultas = $this->createFakultas();

        $programStudi = ProgramStudi::create([
            'nama' => 'Akuntansi',
            'kode' => 'AK001',
            'jenjang' => 'S1',
            'fakultas_id' => $fakultas->id,
        ]);

        $programStudi->delete();

        ProgramStudiImporter::test()
            ->import([
                'nama' => 'Akuntansi',
                'kode' => 'AK002',
                'jenjang' => 'S1',
                'fakultas_id' => 'Fakultas Teknik',
            ])
            ->assertImported();

        $programStudi->refresh();

        $this->assertFalse($programStudi->trashed());
        $this->assertSame('AK002', $programStudi->kode);
    }

    public function test_it_fails_when_fakultas_is_not_found(): void
    {
        $result = ProgramStudiImporter::test()
            ->import([
                'nama' => 'Teknik Informatika',
                'kode' => 'IF001',
                'jenjang' => 'S1',
                'fakultas_id' => 'Fakultas Tidak Ada',
            ])
            ->assertHasErrors(['fakultas_id']);

        $this->assertSame(
            ['Fakultas tidak ditemukan. Periksa nama fakultas pada baris ini.'],
            $result->errors()->all(),
        );

        $this->assertDatabaseMissing('program_studi', [
            'nama' => 'Teknik Informatika',
        ]);
    }

    public function test_it_fails_when_jenjang_is_invalid(): void
    {
        $this->createFakultas();

        ProgramStudiImporter::test()
            ->import([
                'nama' => 'Teknik Informatika',
                'kode' => 'IF001',
                'jenjang' => 'D4',
                'fakultas_id' => 'Fakultas Teknik',
            ])
            ->assertHasErrors(['jenjang']);

        $this->assertDatabaseMissing('program_studi', [
            'nama' => 'Teknik Informatika',
        ]);
    }

    public function test_it_normalizes_corrupted_row_with_comma_in_first_field(): void
    {
        $fakultas = $this->createFakultas();

        // Baris rusak terbaca sebagai satu kolom di "nama".
        ProgramStudiImporter::test()
            ->import([
                'nama' => '"Teknik Informatika,IF001,S1,Fakultas Teknik"',
                'kode' => '',
                'jenjang' => '',
                'fakultas_id' => '',
            ])
            ->assertImported();

        $this->assertDatabaseHas('program_studi', [
            'nama' => 'Teknik Informatika',
            'kode' => 'IF001',
            'jenjang' => 'S1',
            'fakultas_id' => $fakultas->id,
        ]);
    }

    public function test_template_csv_has_correct_header_and_no_bom(): void
    {
        $path = public_path('templates/template-prodi.csv');

        $this->assertFileExists($path);

        $contents = (string) file_get_contents($path);

        $this->assertStringNotContainsString("\xEF\xBB\xBF", $contents);
        $this->assertStringStartsWith('nama,kode,jenjang,fakultas_id', $contents);

        $lines = array_values(array_filter(
            explode("\n", str_replace("\r\n", "\n", trim($contents))),
            fn (string $line): bool => $line !== '',
        ));

        $this->assertCount(3, $lines);

        foreach (array_slice($lines, 1) as $line) {
            $this->assertCount(4, str_getcsv($line));
        }

        $this->assertSame('Teknik Informatika', str_getcsv($lines[1])[0]);
        $this->assertSame('S1', str_getcsv($lines[1])[2]);
        $this->assertSame('Fakultas Teknik', str_getcsv($lines[1])[3]);
    }

    public function test_validation_messages_are_short_and_do_not_mention_excel(): void
    {
        $importer = new ProgramStudiImporter(new Import, [], []);
        $messages = $importer->getValidationMessages();

        $this->assertSame(
            'Format CSV tidak sesuai. Pastikan dipisahkan dengan koma (,).',
            $messages['nama.required'],
        );
        $this->assertSame(
            'Fakultas tidak ditemukan. Periksa nama fakultas pada baris ini.',
            $messages['fakultas_id.required'],
        );
        $this->assertSame(
            'Jenjang tidak valid. Gunakan: D3, S1, S2, atau S3.',
            $messages['jenjang.in'],
        );

        foreach ($messages as $message) {
            $this->assertStringNotContainsString('Excel', $message);
        }
    }
}
