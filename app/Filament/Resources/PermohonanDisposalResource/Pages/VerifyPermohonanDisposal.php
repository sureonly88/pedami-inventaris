<?php

namespace App\Filament\Resources\PermohonanDisposalResource\Pages;

use App\Filament\Resources\PermohonanDisposalResource;
use Filament\Resources\Pages\Page;
use App\Models\PermohonanDisposal;
use Filament\Actions\Action;
use Carbon\Carbon;

class VerifyPermohonanDisposal extends Page
{
    protected static string $resource = PermohonanDisposalResource::class;

    protected static string $view = 'filament.resources.permohonan-disposal-resource.pages.verify-permohonan-disposal';

    public PermohonanDisposal $record;

    public function mount(PermohonanDisposal $record)
    {
        $this->record = $record;
    }

    protected function getHeaderActions(): array
    {
        $karyawan = auth()->user()->karyawan;

        return [
            Action::make('verif_manager')
                ->label('Verifikasi Manager')
                ->color('success')
                ->visible(fn () => $karyawan->jabatan === 'Manager' && !$this->record->verif_manager)
                ->action(fn () => $this->verifyManager()),

            Action::make('verif_ketua')
                ->label('Verifikasi Ketua')
                ->color('primary')
                ->visible(fn () =>
                    $karyawan->jabatan === 'Ketua' &&
                    $this->record->verif_manager &&
                    !$this->record->verif_ketua
                )
                ->action(fn () => $this->verifyKetua()),
        ];
    }

    protected function verifyManager()
    {
        $this->record->update([
            'verif_manager' => 1,
            'tgl_verif_manager' => Carbon::now(),
        ]);

        // Notification::make()
        //     ->title('Berhasil diverifikasi oleh Manager')
        //     ->success()
        //     ->send();
    }

    protected function verifyKetua()
    {
        $this->record->update([
            'verif_ketua' => 1,
            'tgl_verif_ketua' => Carbon::now(),
        ]);

        $this->record->asset()->update([
            'status_barang' => 'Disposal',
        ]);

        // Notification::make()
        //     ->title('Berhasil diverifikasi oleh Ketua')
        //     ->success()
        //     ->send();
    }
}
