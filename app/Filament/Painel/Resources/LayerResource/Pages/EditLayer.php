<?php

namespace App\Filament\Painel\Resources\LayerResource\Pages;

use App\Filament\Painel\Resources\LayerResource;
use App\Models\Layer;
use App\Services\LayerService;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditLayer extends EditRecord
{
    protected static string $resource = LayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $geojsonFile = $data['geojson_file'] ?? null;
        $geometryData = $data['geometry_data'] ?? null;

        if (!$geojsonFile && !$geometryData) {
            unset($data['geojson_file'], $data['geometry_data']);
            return $data;
        }

        unset($data['geojson_file'], $data['geometry_data']);

        // Obter a geometria do GeoJSON
        $layerService = app(LayerService::class);
        $geometry = null;

        if ($geometryData) {
            $geometry = json_decode($geometryData, true);
        } elseif ($geojsonFile) {
            $path = Storage::disk('local')->path($geojsonFile);
            $content = file_get_contents($path);
            $geoJSON = json_decode($content, true);

            $geometry = $layerService->extractGeometry($geoJSON);

            // Limpar arquivo temporário
            Storage::disk('local')->delete($geojsonFile);
        }

        if ($geometry && Layer::validateGeoJSON($geometry)) {
            $data['geometry'] = $geometry;
        }

        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        /** @var Layer $record */
        $record->name = $data['name'];

        // Se há nova geometria, atualizar
        if (isset($data['geometry'])) {
            $layerService = app(LayerService::class);
            $layerService->updateGeometry($record, $data['geometry']);
        } else {
            // Apenas atualizar o nome
            $record->save();
        }

        $record->refresh();

        return $record;
    }
}

