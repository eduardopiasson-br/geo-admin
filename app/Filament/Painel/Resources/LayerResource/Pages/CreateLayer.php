<?php

namespace App\Filament\Painel\Resources\LayerResource\Pages;

use App\Filament\Painel\Resources\LayerResource;
use App\Models\Layer;
use App\Services\LayerService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateLayer extends CreateRecord
{
    protected static string $resource = LayerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $geojsonFile = $data['geojson_file'] ?? null;
        $geometryData = $data['geometry_data'] ?? null;

        unset($data['geojson_file'], $data['geometry_data']);

        // Obter a geometria do GeoJSON
        $geometry = null;
        if ($geometryData) {
            $geometry = json_decode($geometryData, true);
        } elseif ($geojsonFile) {
            $path = Storage::disk('local')->path($geojsonFile);
            $content = file_get_contents($path);
            $geoJSON = json_decode($content, true);

            $layerService = app(LayerService::class);
            $geometry = $layerService->extractGeometry($geoJSON);

            // Limpar arquivo temporário
            Storage::disk('local')->delete($geojsonFile);
        }

        if (!$geometry || !Layer::validateGeoJSON($geometry)) {
            throw new \Exception('Geometria GeoJSON inválida.');
        }

        $data['geometry'] = $geometry;

        return $data;
    }

    protected function handleRecordCreation(array $data): Layer
    {
        $layerService = app(LayerService::class);

        return $layerService->createFromGeoJSON(
            $data['name'],
            $data['geometry']
        );
    }
}

