

<x-filament::input.wrapper >

    <x-filament::input.select wire:model="{{ $getStatePath() }}">
        <option value="0">Pilih Kontrak</option>
        @foreach ($kontraks as $id => $kontrak)
            <option value="{{ $id }}">{{ $kontrak }}</option>
        @endforeach
    </x-filament::input.select>
    <hr/>
    
    <x-filament::input
        type="text"
        readOnly="true"
        wire:model="{{ $getStatePath() }}"
    />

</x-filament::input.wrapper>



