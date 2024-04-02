<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RuanganResource\Pages;
use App\Filament\Resources\RuanganResource\RelationManagers;
use App\Models\Ruangan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RuanganResource extends Resource
{
    protected static ?string $model = Ruangan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Menu Ruangan';

    protected static ?string $navigationGroup = 'Setup';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ruangan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('penanggung_jawab')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('jabatan')
                    ->options([
                        'Pengurus' => 'Pengurus',
                        'Manager' => 'Manager',
                        'Kepala Divisi' => 'Kepala Divisi',
                        'Koordinator' => 'Koordinator',
                        'Staf' => 'Staf',
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ruangan'),
                Tables\Columns\TextColumn::make('penanggung_jawab')->searchable(),
                Tables\Columns\TextColumn::make('jabatan'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListRuangans::route('/'),
            'create' => Pages\CreateRuangan::route('/create'),
            'edit' => Pages\EditRuangan::route('/{record}/edit'),
        ];
    }
}
