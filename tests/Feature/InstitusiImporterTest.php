<?php

namespace Tests\Feature;

use App\Enums\JenisInstitusi;
use App\Filament\Imports\InstitusiImporter;
use App\Models\Institusi;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstitusiImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_new_institusi_with_normalized_values(): void
    {
        InstitusiImporter::test()
            ->import([
                'nama' => 'SMK Negeri 1 Bedus',
                'jenis' => 'smk',
                'kecamatan' => 'Bojonegoro',
                'alamat' => 'Jl. Raya Bedus No. 1',
                'is_active' => 'aktif',
            ])
            ->assertImported();

        $this->assertDatabaseHas('institusi', [
            'nama' => 'SMK Negeri 1 Bedus',
            'jenis' => JenisInstitusi::Smk->value,
            'kecamatan' => 'Bojonegoro',
            'is_active' => true,
        ]);
    }

    public function test_it_imports_inactive_institusi(): void
    {
        InstitusiImporter::test()
            ->import([
                'nama' => 'SMA Negeri 1 Bedus',
                'jenis' => 'SMA',
                'kecamatan' => 'Bojonegoro',
                'alamat' => 'Jl. Pendidikan No. 5',
                'is_active' => 'nonaktif',
            ])
            ->assertImported();

        $this->assertDatabaseHas('institusi', [
            'nama' => 'SMA Negeri 1 Bedus',
            'is_active' => false,
        ]);
    }

    public function test_it_restores_soft_deleted_institusi_with_the_same_name(): void
    {
        $institusi = Institusi::create([
            'nama' => 'Universitas Bedus',
            'jenis' => JenisInstitusi::Pt,
            'kecamatan' => 'Bojonegoro',
            'alamat' => 'Alamat Lama',
            'is_active' => true,
        ]);

        $institusi->delete();

        InstitusiImporter::test()
            ->import([
                'nama' => 'Universitas Bedus',
                'jenis' => 'PT',
                'kecamatan' => 'Bojonegoro',
                'alamat' => 'Jl. Pendidikan No. 10',
                'is_active' => 'aktif',
            ])
            ->assertImported();

        $institusi->refresh();

        $this->assertFalse($institusi->trashed());
        $this->assertSame('Jl. Pendidikan No. 10', $institusi->alamat);
    }

    public function test_it_fails_when_jenis_is_invalid(): void
    {
        InstitusiImporter::test()
            ->import([
                'nama' => 'Akademi Tidak Dikenal',
                'jenis' => 'UNIVERSITAS',
                'kecamatan' => 'Bojonegoro',
                'alamat' => 'Jl. Tidak Ada',
                'is_active' => 'aktif',
            ])
            ->assertHasErrors(['jenis']);

        $this->assertDatabaseMissing('institusi', [
            'nama' => 'Akademi Tidak Dikenal',
        ]);
    }

    public function test_template_csv_is_comma_separated_without_bom(): void
    {
        $path = public_path('templates/template-institusi.csv');

        $this->assertFileExists($path);

        $contents = (string) file_get_contents($path);

        $this->assertStringNotContainsString("\xEF\xBB\xBF", $contents);
        $this->assertStringStartsWith('nama,jenis,kecamatan,alamat,status', $contents);

        $lines = array_values(array_filter(
            explode("\n", str_replace("\r\n", "\n", trim($contents))),
            fn (string $line): bool => $line !== '',
        ));

        $this->assertCount(3, $lines);

        foreach (array_slice($lines, 1) as $line) {
            $this->assertCount(5, str_getcsv($line));
        }

        $this->assertSame('SMK', str_getcsv($lines[1])[1]);
        $this->assertSame('PT', str_getcsv($lines[2])[1]);
    }

    public function test_validation_messages_are_short_and_do_not_mention_excel(): void
    {
        $importer = new InstitusiImporter(new Import, [], []);
        $messages = $importer->getValidationMessages();

        $this->assertSame(
            'Format CSV tidak sesuai. Pastikan dipisahkan dengan koma (,).',
            $messages['nama.required'],
        );
        $this->assertSame(
            'Kolom jenis tidak ditemukan. Periksa susunan CSV.',
            $messages['jenis.required'],
        );
        $this->assertSame(
            'Jenis tidak valid. Gunakan: SMK, SMA, atau PT.',
            $messages['jenis.in'],
        );

        foreach ($messages as $message) {
            $this->assertStringNotContainsString('Excel', $message);
        }
    }
}
