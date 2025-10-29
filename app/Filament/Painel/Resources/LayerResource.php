<?php

namespace App\Filament\Painel\Resources;

use App\Filament\Painel\Resources\LayerResource\Pages;
use App\Models\Layer;
use App\Services\LayerService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class LayerResource extends Resource
{
    protected static ?string $model = Layer::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Camadas';

    protected static ?string $modelLabel = 'Camada';

    protected static ?string $pluralModelLabel = 'Camadas';

    protected static ?string $navigationGroup = null;

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome da Camada')
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('geojson_file')
                    ->label('Arquivo GeoJSON')
                    ->acceptedFileTypes(['application/json', 'application/geo+json'])
                    ->disk('local')
                    ->directory('geojson-uploads')
                    ->visibility('private')
                    ->maxSize(10240) // 10MB
                    ->required(fn ($record) => $record === null)
                    ->deletable(false)
                    ->downloadable(false)
                    ->columnSpanFull()
                    ->helperText('Faça upload de um arquivo GeoJSON válido contendo uma geometria (Polygon, MultiPolygon, etc.)')
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if (!$state) {
                            return;
                        }

                        try {
                            $path = Storage::disk('local')->path($state);
                            $content = file_get_contents($path);
                            $geoJSON = json_decode($content, true);

                            if (json_last_error() !== JSON_ERROR_NONE) {
                                throw ValidationException::withMessages([
                                    'geojson_file' => 'O arquivo não é um JSON válido.',
                                ]);
                            }

                            // Extrair geometria usando LayerService
                            $layerService = app(LayerService::class);
                            $geometry = $layerService->extractGeometry($geoJSON);

                            if (!$geometry || !Layer::validateGeoJSON($geometry)) {
                                throw ValidationException::withMessages([
                                    'geojson_file' => 'O GeoJSON não contém uma geometria válida.',
                                ]);
                            }

                            $set('geometry_data', json_encode($geometry));
                        } catch (ValidationException $e) {
                            throw $e;
                        } catch (\Exception $e) {
                            throw ValidationException::withMessages([
                                'geojson_file' => 'Erro ao processar o arquivo: ' . $e->getMessage(),
                            ]);
                        }
                    }),

                Forms\Components\Hidden::make('geometry_data'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('has_geometry')
                    ->label('Tem Geometria')
                    ->boolean()
                    ->getStateUsing(fn (Layer $record): bool => $record->geometry !== null)
                    ->default(false),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLayers::route('/'),
            'create' => Pages\CreateLayer::route('/create'),
            'edit' => Pages\EditLayer::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
}

