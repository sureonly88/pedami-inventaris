<?php

namespace App\Exports;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KaryawanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected int $rowNumber = 0;

    protected array $selectedFields;

    protected function getFormattedExportTimestamp(): string
    {
        return now()
            ->timezone(config('app.timezone'))
            ->locale('id')
            ->translatedFormat('d F Y H:i');
    }

    public function __construct(
        protected ?Builder $query = null,
        protected ?string $title = null,
        protected ?string $subtitle = null,
        ?array $selectedFields = null,
    ) {
        $availableFields = array_keys(static::getFieldOptions());

        $this->selectedFields = collect($selectedFields ?: $availableFields)
            ->filter(fn (string $field) => in_array($field, $availableFields, true))
            ->values()
            ->all();
    }

    public static function getFieldOptions(): array
    {
        return [
            'nik' => 'NIK',
            'nama_karyawan' => 'Nama Karyawan',
            'no_ktp' => 'No KTP',
            'no_hp' => 'No HP',
            'no_rekening' => 'No Rekening',
            'no_bpjs_ketenagakerjaan' => 'No BPJS Ketenagakerjaan',
            'no_bpjs_kesehatan' => 'No BPJS Kesehatan',
            'pendidikan_terakhir' => 'Pendidikan Terakhir',
            'alamat' => 'Alamat',
            'agama' => 'Agama',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'umur' => 'Umur',
            'tanggal_masuk_kerja' => 'Tanggal Masuk Kerja',
            'masa_kerja' => 'Masa Kerja',
            'nama_bank' => 'Nama Bank',
            'kontak_darurat' => 'Kontak Darurat',
            'status_karyawan' => 'Status Karyawan',
            'jabatan' => 'Jabatan',
            'subdivisi' => 'Subdivisi',
            'divisi' => 'Divisi',
            'jkel' => 'Jenis Kelamin',
        ];
    }

    public function collection()
    {
        $this->rowNumber = 0;

        return ($this->query ?? Karyawan::query())
            ->with(['subdivisi.divisi'])
            ->orderBy('nama_karyawan')
            ->get();
    }

    public function headings(): array
    {
        return array_merge(
            ['No'],
            array_values(array_intersect_key(static::getFieldOptions(), array_flip($this->selectedFields)))
        );
    }

    public function map($row): array
    {
        $availableValues = [
            'nik' => $row->nik,
            'nama_karyawan' => $row->nama_karyawan,
            'no_ktp' => $row->no_ktp,
            'no_hp' => $row->no_hp,
            'no_rekening' => $row->no_rekening,
            'no_bpjs_ketenagakerjaan' => $row->no_bpjs_ketenagakerjaan,
            'no_bpjs_kesehatan' => $row->no_bpjs_kesehatan,
            'pendidikan_terakhir' => $row->pendidikan_terakhir,
            'alamat' => $row->alamat,
            'agama' => $row->agama,
            'tempat_lahir' => $row->tempat_lahir,
            'tanggal_lahir' => optional($row->tanggal_lahir)->translatedFormat('d F Y'),
            'umur' => $row->umur,
            'tanggal_masuk_kerja' => optional($row->tanggal_masuk_kerja)->translatedFormat('d F Y'),
            'masa_kerja' => $row->masa_kerja,
            'nama_bank' => $row->nama_bank,
            'kontak_darurat' => $row->kontak_darurat,
            'status_karyawan' => $row->status_karyawan,
            'jabatan' => $this->normalizeJabatan($row->jabatan),
            'subdivisi' => $row->subdivisi?->nama_sub,
            'divisi' => $row->subdivisi?->divisi?->nama_divisi,
            'jkel' => $this->normalizeGender($row->jkel),
        ];

        return array_merge(
            [++$this->rowNumber],
            collect($this->selectedFields)->map(fn (string $field) => $availableValues[$field] ?? null)->all()
        );
    }

    protected function normalizeJabatan(?string $jabatan): ?string
    {
        return match ($jabatan) {
            'Staff' => 'Staf',
            default => $jabatan,
        };
    }

    protected function normalizeGender(?string $gender): ?string
    {
        return match ($gender) {
            'Laki-Laki' => 'Laki - Laki',
            default => $gender,
        };
    }

    public function styles(Worksheet $sheet)
    {
        return [
            5 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->insertNewRowBefore(1, 4);
                $sheet->setCellValue('A1', $this->title ?: 'DATA KARYAWAN');
                $sheet->setCellValue('A2', 'KOPERASI KONSUMEN PEDAMI');
                $sheet->setCellValue('A3', $this->subtitle ?: 'Tanggal Export: ' . $this->getFormattedExportTimestamp());

                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->mergeCells('A2:' . $highestColumn . '2');
                $sheet->mergeCells('A3:' . $highestColumn . '3');

                $sheet->getStyle('A1:' . $highestColumn . '3')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
                $sheet->getStyle('A2')->getFont()->setSize(12)->setBold(true);

                $sheet->getStyle('A5:' . $highestColumn . '5')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFE5E7EB'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle('A5:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $sheet->freezePane('A6');
                $sheet->getStyle('A5:' . $highestColumn . $highestRow)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

                if (($alamatColumn = $this->getColumnLetterForField('alamat')) !== null) {
                    $sheet->getStyle($alamatColumn . '6:' . $alamatColumn . $highestRow)->getAlignment()->setWrapText(true);
                }
            },
        ];
    }

    protected function getColumnLetterForField(string $field): ?string
    {
        $fieldIndex = array_search($field, $this->selectedFields, true);

        if ($fieldIndex === false) {
            return null;
        }

        return $this->columnNumberToLetter($fieldIndex + 2);
    }

    protected function columnNumberToLetter(int $columnNumber): string
    {
        $columnLetter = '';

        while ($columnNumber > 0) {
            $modulo = ($columnNumber - 1) % 26;
            $columnLetter = chr(65 + $modulo) . $columnLetter;
            $columnNumber = intdiv($columnNumber - $modulo, 26);
        }

        return $columnLetter;
    }
}