<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KontrakResource\Pages;
use App\Filament\Resources\KontrakResource\RelationManagers;
use App\Models\data_r2r4;
use App\Models\Kontrak;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ViewColumn;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;

class KontrakResource extends Resource
{
    protected static ?string $model = Kontrak::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Kontrak';

    protected static ?string $navigationGroup = 'Setup';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('no_kontrak')
                    //->unique(column: 'no_kontrak')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->maxLength(100),
                Forms\Components\DatePicker::make('tgl_awal')
                    ->required()
                    ->label('Tanggal Awal Kontrak'),
                Forms\Components\DatePicker::make('tgl_akhir')
                    ->required()
                    ->label('Tanggal Akhir Kontrak'),
                FileUpload::make('file')
                    ->disk('minio')
                    ->visibility('public')
                    ->downloadable(),
                Repeater::make('kontrakDetails')
                    ->relationship()
                    ->schema([
                        // Select::make('data_r2r4_id')
                        //     ->label('Kendaraan')    
                        //     ->options(data_r2r4::all()->pluck('plat', 'id')->all())
                        // ->searchable()

                        Select::make('data_r2r4_id')
                            ->label('Kendaraan') 
                            ->searchable()
                            //->options(data_r2r4::all()->pluck('plat', 'id')->all())
                            ->getSearchResultsUsing(fn (string $search): array => data_r2r4::where('plat', 'like', "%{$search}%")->limit(50)->pluck('plat', 'id')->toArray())
                            ->getOptionLabelUsing(fn ($value): ?string => data_r2r4::find($value)?->plat),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('no_kontrak')
                    ->searchable(),
                Tables\Columns\TextColumn::make('judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_awal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_akhir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('file')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListKontraks::route('/'),
            'create' => Pages\CreateKontrak::route('/create'),
            'edit' => Pages\EditKontrak::route('/{record}/edit'),
        ];
    }
}
