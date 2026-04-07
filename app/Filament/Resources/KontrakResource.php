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

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Kontrak';

    protected static ?string $navigationGroup = 'Transaksi';

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
                    ->label('Tanggal Awal Kontrak')
                    ->reactive()
                    ->afterStateUpdated(function ($state, $get, $set) {
                        $tglAkhir = $get('tgl_akhir');
                        if ($state && $tglAkhir) {
                            $start = \Carbon\Carbon::parse($state);
                            $end = \Carbon\Carbon::parse($tglAkhir);
                            $diff = (int) $start->diffInMonths($end);
                            $set('masa_sewa', $diff);
                        }
                    }),
                Forms\Components\DatePicker::make('tgl_akhir')
                    ->required()
                    ->label('Tanggal Akhir Kontrak')
                    ->reactive()
                    ->afterStateUpdated(function ($state, $get, $set) {
                        $tglAwal = $get('tgl_awal');
                        if ($tglAwal && $state) {
                            $start = \Carbon\Carbon::parse($tglAwal);
                            $end = \Carbon\Carbon::parse($state);
                            $diff = (int) $start->diffInMonths($end);
                            $set('masa_sewa', $diff);
                        }
                    }),
                Forms\Components\TextInput::make('masa_sewa')
                    ->label('Masa Sewa')
                    ->suffix('Bulan')
                    ->readOnly()
                    ->dehydrated(false)
                    ->numeric()
                    ->formatStateUsing(fn ($state) => filled($state) ? (string) round((float) $state) : null)
                    ->placeholder('Otomatis terisi...')
                    ->default(function ($record) {
                        if ($record && $record->tgl_awal && $record->tgl_akhir) {
                            $start = \Carbon\Carbon::parse($record->tgl_awal);
                            $end = \Carbon\Carbon::parse($record->tgl_akhir);
                            return round($start->diffInMonths($end));
                        }
                        return null;
                    }),
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
            ->striped()
            ->defaultSort('tgl_akhir', 'desc')
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
                    ->label('No. Kontrak')
                    ->badge()
                    ->color('gray')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul Kontrak')
                    ->searchable()
                    ->wrap()
                    ->limit(40),
                Tables\Columns\TextColumn::make('tgl_awal')
                    ->label('Tgl Awal')
                    ->date('d/m/Y')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_akhir')
                    ->label('Tgl Akhir')
                    ->date('d/m/Y')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('masa_sewa')
                    ->label('Masa Sewa')
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->getStateUsing(function ($record) {
                        if ($record->tgl_awal && $record->tgl_akhir) {
                            $start = \Carbon\Carbon::parse($record->tgl_awal);
                            $end = \Carbon\Carbon::parse($record->tgl_akhir);
                            return round($start->diffInMonths($end)) . ' Bulan';
                        }
                        return '-';
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        $now = now()->startOfDay();
                        $start = \Carbon\Carbon::parse($record->tgl_awal)->startOfDay();
                        $end = \Carbon\Carbon::parse($record->tgl_akhir)->startOfDay();

                        if ($now->gt($end)) {
                            return 'EXPIRED';
                        }

                        if ($now->diffInDays($end, false) <= 30 && $now->diffInDays($end, false) >= 0) {
                            return 'SEGERA BERAKHIR';
                        }

                        if ($now->between($start, $end)) {
                            return 'AKTIF';
                        }

                        if ($now->lt($start)) {
                            return 'AKAN DATANG';
                        }

                        return 'UNKNOWN';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'AKTIF' => 'success',
                        'SEGERA BERAKHIR' => 'warning',
                        'EXPIRED' => 'danger',
                        'AKAN DATANG' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('file')
                    ->label('File')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'aktif' => 'AKTIF',
                        'expired' => 'EXPIRED',
                        'segera' => 'SEGERA BERAKHIR',
                        'coming' => 'AKAN DATANG',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!$data['value']) return $query;
                        
                        $now = now()->startOfDay()->toDateString();
                        $nearEnd = now()->startOfDay()->addDays(30)->toDateString();
                        
                        return match ($data['value']) {
                            'aktif' => $query->where('tgl_awal', '<=', $now)->where('tgl_akhir', '>=', $now),
                            'expired' => $query->where('tgl_akhir', '<', $now),
                            'segera' => $query->where('tgl_akhir', '>=', $now)->where('tgl_akhir', '<=', $nearEnd),
                            'coming' => $query->where('tgl_awal', '>', $now),
                        };
                    })
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
