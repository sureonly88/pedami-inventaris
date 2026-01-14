<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KaryawanResource\Pages;
use App\Filament\Resources\KaryawanResource\RelationManagers;
use App\Models\Karyawan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subdivisi;
use App\Models\Divisi;
use Filament\Forms\Get;


class KaryawanResource extends Resource
{
    protected static ?string $model = Karyawan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Karyawan';

    protected static ?string $navigationGroup = 'Setup';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nik')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama_karyawan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('jabatan')
                    ->options([
                        'Ketua' => 'Ketua',
                        'Bendahara' => 'Bendahara',
                        'Sekretaris' => 'Sekretaris',
                        'Manager'=> 'Manager',
                        'Kepala Divisi' => 'Kepala Divisi',
                        'Koordinator' => 'Koordinator',
                        'Staff' => 'Staf',
                        'All Karyawan' => 'All Karyawan',
                    ])->required(),

                Forms\Components\Select::make('divisi_id')
                    ->label('Divisi')
                    ->options(Divisi::pluck('nama_divisi', 'id'))
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('subdivisi_id', null)),

                Forms\Components\Select::make('subdivisi_id')
                    ->label('Sub Divisi')
                    ->options(function (Get $get) {
                        $divisiId = $get('divisi_id');

                        if (!$divisiId) {
                            return [];
                        }

                        return Subdivisi::where('divisi_id', $divisiId)
                            ->pluck('nama_sub', 'id');
                    })
                    ->reactive()
                    ->required(),

                Forms\Components\Select::make('jkel')
                ->options([
                    'Laki-Laki' => 'Laki - Laki',
                    'Perempuan' => 'Perempuan',
                ])->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nik'),
                Tables\Columns\TextColumn::make('nama_karyawan')->searchable(),
                Tables\Columns\TextColumn::make('jabatan'),
                Tables\Columns\TextColumn::make('subdivisi.divisi.nama_divisi'),
                Tables\Columns\TextColumn::make('subdivisi.nama_sub'),
                Tables\Columns\TextColumn::make('jkel'),
            ])
            ->filters([
                //Tables\Filters\SelectFilter::make('divisi_id')
                //->relationship('divisi', 'nama_divisi')
                // Tables\Filters\SelectFilter::make('divisi_id')
                // ->relationship('divisi', 'subdivisi')
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
            'index' => Pages\ListKaryawans::route('/'),
            'create' => Pages\CreateKaryawan::route('/create'),
            'edit' => Pages\EditKaryawan::route('/{record}/edit'),
        ];
    }
}
