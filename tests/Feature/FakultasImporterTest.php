<?php

namespace Tests\Feature;

use App\Filament\Imports\FakultasImporter;
use App\Models\Fakultas;
use App\Models\Institusi;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FakultasImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_new_fakultas_with_institusi_id_resolved_from_name(): void
    {
        Institusi::create([
            'nama' => 'Universitas Bedus',
            'jenis' => 'PT',
            'kecamatan' => 'Bojonegoro',
            'alamat' => 'Jl. Pendidikan No. 10',
            'is_active' => true,
        ]);

        FakultasImporter::test()
            ->import([
                'nama' => 'Fakultas Teknik',
                'institusi_id' => 'Universitas Bedus',
            ])
            ->assertImported();

        $institusi = Institusi::where('nama', 'Universitas Bedus')->first();

        $this->assertDatabaseHas('fakultas', [
            'nama' => 'Fakultas Teknik',
            'institusi_id' => $institusi->id,
        ]);
    }

    public function test_it_restores_soft_deleted_fakultas_with_the_same_name_and_institusi(): void
    {
        $institusi = Institusi::create([
            'nama' => 'Universitas Bedus',
            'jenis' => 'PT',
            'kecamatan' => 'Bojonegoro',
            'alamat' => 'Jl. Pendidikan No. 10',
            'is_active' => true,
        ]);

        $fakultas = Fakultas::create([
            'nama' => 'Fakultas Ekonomi',
            'institusi_id' => $institusi->id,
        ]);

        $fakultas->delete();

        FakultasImporter::test()
            ->import([
                'nama' => 'Fakultas Ekonomi',
                'institusi_id' => 'Universitas Bedus',
            ])
            ->assertImported();

        $fakultas->refresh();

        $this->assertFalse($fakultas->trashed());
    }

    public function test_it_fails_when_institusi_is_not_found(): void
    {
        $result = FakultasImporter::test()
            ->import([
                'nama' => 'Fakultas Teknik',
                'institusi_id' => 'Institusi Tidak Ada',
            ])
            ->assertHasErrors(['institusi_id']);

        $this->assertSame(
            ['Institusi tidak ditemukan. Periksa nama institusi pada baris ini.'],
            $result->errors()->all(),
        );

        $this->assertDatabaseMissing('fakultas', [
            'nama' => 'Fakultas Teknik',
        ]);
    }

    public function test_it_normalizes_corrupted_row_with_comma_in_first_field(): void
    {
        Institusi::create([
            'nama' => 'Universitas Bedus',
            'jenis' => 'PT',
            'kecamatan' => 'Bojonegoro',
            'alamat' => 'Jl. Pendidikan No. 10',
            'is_active' => true,
        ]);

        // Baris rusak: "Fakultas Teknik,Universitas Bedus" terbungkus kutip
        // sehingga terbaca sebagai satu kolom di "nama".
        FakultasImporter::test()
            ->import([
                'nama' => '"Fakultas Teknik,Universitas Bedus"',
                'institusi_id' => '',
            ])
            ->assertImported();

        $institusi = Institusi::where('nama', 'Universitas Bedus')->first();

        $this->assertDatabaseHas('fakultas', [
            'nama' => 'Fakultas Teknik',
            'institusi_id' => $institusi->id,
        ]);
    }

    public function test_template_csv_has_correct_header_and_no_bom(): void
    {
        $path = public_path('templates/template-fakultas.csv');

        $this->assertFileExists($path);

        $contents = (string) file_get_contents($path);

        $this->assertStringNotContainsString("\xEF\xBB\xBF", $contents);
        $this->assertStringStartsWith('nama,institusi_id', $contents);

        $lines = array_values(array_filter(
            explode("\n", str_replace("\r\n", "\n", trim($contents))),
            fn (string $line): bool => $line !== '',
        ));

        $this->assertCount(3, $lines);

        foreach (array_slice($lines, 1) as $line) {
            $this->assertCount(2, str_getcsv($line));
        }

        $this->assertSame('Fakultas Teknik', str_getcsv($lines[1])[0]);
        $this->assertSame('Universitas Bedus', str_getcsv($lines[1])[1]);
    }

    public function test_validation_messages_are_short_and_do_not_mention_excel(): void
    {
        $importer = new FakultasImporter(new Import, [], []);
        $messages = $importer->getValidationMessages();

        $this->assertSame(
            'Format CSV tidak sesuai. Pastikan dipisahkan dengan koma (,).',
            $messages['nama.required'],
        );
        $this->assertSame(
            'Institusi tidak ditemukan. Periksa nama institusi pada baris ini.',
            $messages['institusi_id.required'],
        );

        foreach ($messages as $message) {
            $this->assertStringNotContainsString('Excel', $message);
        }
    }
}
