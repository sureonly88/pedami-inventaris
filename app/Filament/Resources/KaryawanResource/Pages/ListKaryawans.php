<?php

namespace App\Filament\Resources\KaryawanResource\Pages;

use App\Exports\KaryawanExport;
use App\Filament\Resources\KaryawanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListKaryawans extends ListRecords
{
    protected static string $resource = KaryawanResource::class;

    protected static ?string $title = 'Karyawan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\ImportAction::make()
                ->importer(\App\Filament\Imports\KaryawanImporter::class)
                ->label('Import Karyawan Excel')
                ->color('success')
                ->icon('heroicon-o-arrow-up-tray'),
            Actions\Action::make('export_excel')
                ->label('Export Karyawan Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->action(fn () => Excel::download(
                    new KaryawanExport(),
                    'Data_Karyawan_' . now()->format('Y-m-d_His') . '.xlsx'
                )),
            Actions\CreateAction::make(),
        ];
    }
}
