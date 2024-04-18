<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubdivisiResource\Pages;
use App\Filament\Resources\SubdivisiResource\RelationManagers;
use App\Models\Subdivisi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Set;

class SubdivisiResource extends Resource
{
    protected static ?string $model = Subdivisi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationLabel = 'Subdivisi';

    protected static ?string $navigationGroup = 'Setup';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode_sub')
                    ->required()
                    ->readOnly(true)
                    ->default(function (?Subdivisi $subdivisi): string {
                        $last_sub = $subdivisi::orderBy('kode_sub', 'desc')->first();
                        $next_num = (int) substr($last_sub->kode_sub, 2, 3) + 1;
                        $next_sub = 'KS' . str_repeat('0', 3 - strlen($next_num)) . $next_num;
                        return $next_sub;
                    })
                    ->maxLength(255),
                Forms\Components\Select::make('divisi_id')
                    ->relationship(name: 'divisi', titleAttribute: 'nama_divisi'),

                Forms\Components\TextInput::make('nama_sub')
                    ->required()
                    ->maxLength(255)
                    ->columns(2)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_sub'),
                Tables\Columns\TextColumn::make('divisi.nama_divisi')->searchable(),
                Tables\Columns\TextColumn::make('nama_sub')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListSubdivisis::route('/'),
            'create' => Pages\CreateSubdivisi::route('/create'),
            'edit' => Pages\EditSubdivisi::route('/{record}/edit'),
        ];
    }
}
