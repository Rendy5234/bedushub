<?php

namespace Tests\Feature;

use App\Filament\Imports\JurusanImporter;
use App\Models\Institusi;
use App\Models\Jurusan;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JurusanImporterTest extends TestCase
{
    use RefreshDatabase;

    /** @var Institusi */
    private $smkInstitusi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->smkInstitusi = Institusi::create([
            'nama' => 'SMK Negeri 1 Bedus',
            'jenis' => 'SMK',
            'kecamatan' => 'Bojonegoro',
            'alamat' => 'Jl. Raya Bedus No. 1, Bojonegoro',
            'is_active' => true,
        ]);
    }

    public function test_it_imports_new_jurusan_with_institusi_id_resolved_from_name(): void
    {
        JurusanImporter::test()
            ->import([
                'nama' => 'Teknik Komputer dan Jaringan',
                'kode' => 'TKJ',
                'institusi_id' => 'SMK Negeri 1 Bedus',
            ])
            ->assertImported();

        $this->assertDatabaseHas('jurusan', [
            'nama' => 'Teknik Komputer dan Jaringan',
            'kode' => 'TKJ',
            'institusi_id' => $this->smkInstitusi->id,
        ]);
    }

    public function test_it_imports_jurusan_without_kode(): void
    {
        JurusanImporter::test()
            ->import([
                'nama' => 'Teknik Mesin',
                'kode' => '',
                'institusi_id' => 'SMK Negeri 1 Bedus',
            ])
            ->assertImported();

        $this->assertDatabaseHas('jurusan', [
            'nama' => 'Teknik Mesin',
            'kode' => null,
        ]);
    }

    public function test_it_restores_soft_deleted_jurusan_with_the_same_name_and_institusi(): void
    {
        $jurusan = Jurusan::create([
            'nama' => 'Akuntansi',
            'kode' => 'AK',
            'institusi_id' => $this->smkInstitusi->id,
        ]);

        $jurusan->delete();

        JurusanImporter::test()
            ->import([
                'nama' => 'Akuntansi',
                'kode' => 'AKT',
                'institusi_id' => 'SMK Negeri 1 Bedus',
            ])
            ->assertImported();

        $jurusan->refresh();

        $this->assertFalse($jurusan->trashed());
        $this->assertSame('AKT', $jurusan->kode);
    }

    public function test_it_fails_when_institusi_is_not_found(): void
    {
        $result = JurusanImporter::test()
            ->import([
                'nama' => 'Teknik Komputer dan Jaringan',
                'kode' => 'TKJ',
                'institusi_id' => 'Institusi Tidak Ada',
            ])
            ->assertHasErrors(['institusi_id']);

        $this->assertSame(
            ['Institusi tidak ditemukan. Periksa nama institusi pada baris ini.'],
            $result->errors()->all(),
        );

        $this->assertDatabaseMissing('jurusan', [
            'nama' => 'Teknik Komputer dan Jaringan',
        ]);
    }

    public function test_it_normalizes_corrupted_row_with_comma_in_first_field(): void
    {
        // Baris rusak terbaca sebagai satu kolom di "nama".
        JurusanImporter::test()
            ->import([
                'nama' => '"Teknik Komputer dan Jaringan,TKJ,SMK Negeri 1 Bedus"',
                'kode' => '',
                'institusi_id' => '',
            ])
            ->assertImported();

        $this->assertDatabaseHas('jurusan', [
            'nama' => 'Teknik Komputer dan Jaringan',
            'kode' => 'TKJ',
            'institusi_id' => $this->smkInstitusi->id,
        ]);
    }

    public function test_template_csv_has_correct_header_and_no_bom(): void
    {
        $path = public_path('templates/template-jurusan.csv');

        $this->assertFileExists($path);

        $contents = (string) file_get_contents($path);

        $this->assertStringNotContainsString("\xEF\xBB\xBF", $contents);
        $this->assertStringStartsWith('nama,kode,institusi_id', $contents);

        $lines = array_values(array_filter(
            explode("\n", str_replace("\r\n", "\n", trim($contents))),
            fn (string $line): bool => $line !== '',
        ));

        $this->assertCount(3, $lines);

        foreach (array_slice($lines, 1) as $line) {
            $this->assertCount(3, str_getcsv($line));
        }

        $this->assertSame('Teknik Komputer dan Jaringan', str_getcsv($lines[1])[0]);
        $this->assertSame('SMK Negeri 1 Bedus', str_getcsv($lines[1])[2]);
    }

    public function test_validation_messages_are_short_and_do_not_mention_excel(): void
    {
        $importer = new JurusanImporter(new Import, [], []);
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
