<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermohonanDisposalResource\Pages;
use App\Filament\Resources\PermohonanDisposalResource\RelationManagers;
use App\Models\PermohonanDisposal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Asset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ImageEntry;

class PermohonanDisposalResource extends Resource
{
    protected static ?string $model = PermohonanDisposal::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('asset_id')
                    ->label('Asset')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->options(fn () =>
                        Asset::query()
                            ->get()
                            ->mapWithKeys(fn ($asset) => [
                                $asset->id => $asset->kode_asset . ' - ' . $asset->nama_asset,
                            ])
                    )
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $asset = Asset::find($state);

                        if ($asset) {
                            $set('nama_asset', $asset->nama_asset);
                            $set('hrg_beli', $asset->hrg_beli);
                            $set('gambar_asset', [$asset->gambar]);
                        }
                    }),
                Section::make('Informasi Asset')
                    ->schema([
                        TextInput::make('nama_asset')
                            ->label('Nama Asset')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('hrg_beli')
                            ->label('Harga Beli')
                            ->disabled()
                            ->formatStateUsing(fn ($state) =>
                                $state ? 'Rp ' . number_format($state, 0, ',', '.') : null
                            )
                            ->dehydrated(false),

                        FileUpload::make('gambar_asset')
                            ->label('Gambar Asset')
                            ->image()
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->visible(fn ($get) => filled($get('asset_id'))),

                Forms\Components\DatePicker::make('tgl_pengajuan')
                    ->required(),
                
                Forms\Components\TextInput::make('keterangan')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_pengajuan')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dibuat_oleh')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('verif_manager')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('verif_ketua')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListPermohonanDisposals::route('/'),
            'create' => Pages\CreatePermohonanDisposal::route('/create'),
            //'edit' => Pages\EditPermohonanDisposal::route('/{record}/edit'),
        ];
    }
}
