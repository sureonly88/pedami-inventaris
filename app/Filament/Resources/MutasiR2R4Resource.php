<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MutasiR2R4Resource\Pages;
use App\Filament\Resources\MutasiR2R4Resource\RelationManagers;
use App\Models\MutasiR2R4;
use App\Models\data_r2r4;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class MutasiR2R4Resource extends Resource
{
    protected static ?string $model = MutasiR2R4::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Mutasi Kendaraan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('data_r2r4_id')
                    ->label('Kendaraan')
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search) {
                        return data_r2r4::where('kode_brg', 'like', "%{$search}%")
                            ->orWhere('nm_brg', 'like', "%{$search}%")
                            ->orWhere('plat', 'like', "%{$search}%")
                            ->limit(20)
                            ->get()
                            ->mapWithKeys(function ($item) {
                                return [$item->id => $item->plat . ' - ' . $item->nm_brg];
                            });
                    })
                    ->getOptionLabelUsing(function ($value) {
                        $vehicle = data_r2r4::find($value);
                        return $vehicle ? $vehicle->plat . ' - ' . $vehicle->nm_brg : null;
                    })
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        $vehicle = data_r2r4::find($state);
                        if ($vehicle) {
                            $set('pemegang_awal', $vehicle->pemegang);
                            $set('departemen_awal', $vehicle->departemen);
                        }
                    })
                    ->required()
                    ->columnSpanFull(),

                Section::make('Sebelum Mutasi')
                    ->schema([
                        Forms\Components\TextInput::make('pemegang_awal')
                            ->label('Pemegang Awal')
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('departemen_awal')
                            ->label('Departemen Awal')
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(2),

                Section::make('Sesudah Mutasi')
                    ->schema([
                        Forms\Components\TextInput::make('pemegang_tujuan')
                            ->label('Pemegang Baru')
                            ->required(),
                        Forms\Components\TextInput::make('departemen_tujuan')
                            ->label('Departemen Baru')
                            ->required(),
                    ])->columns(2),

                Forms\Components\DatePicker::make('tgl_mutasi')
                    ->label('Tanggal Mutasi')
                    ->native(false)
                    ->required(),
                Forms\Components\TextInput::make('deskripsi')
                    ->label('Deskripsi / Keterangan')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tgl_mutasi')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_r2r4.plat')
                    ->label('Kendaraan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pemegang_awal')
                    ->label('Pemegang Awal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pemegang_tujuan')
                    ->label('Pemegang Baru')
                    ->searchable(),
                Tables\Columns\TextColumn::make('departemen_tujuan')
                    ->label('Departemen Baru'),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Add your filters here if needed
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMutasiR2R4S::route('/'),
            'create' => Pages\CreateMutasiR2R4::route('/create'),
            // 'edit' => Pages\EditMutasiR2R4::route('/{record}/edit'),
        ];
    }
}
