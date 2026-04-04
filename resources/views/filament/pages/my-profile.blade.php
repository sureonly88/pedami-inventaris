<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 text-right">
            <x-filament::button type="submit" color="primary">
                Simpan Profil
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
