<?php

namespace App\Services;

use App\Models\Layer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LayerService
{
    /**
     * Cria uma nova camada a partir de GeoJSON.
     */
    public function createFromGeoJSON(string $name, array $geoJSON): Layer
    {
        $geoJSONString = json_encode($geoJSON);

        $result = DB::selectOne(
            'INSERT INTO layers (name, geometry, created_at, updated_at) 
             VALUES (?, ST_GeomFromGeoJSON(?), NOW(), NOW()) 
             RETURNING id',
            [$name, $geoJSONString]
        );

        return Layer::findOrFail($result->id);
    }

    /**
     * Atualiza a geometria de uma camada existente.
     */
    public function updateGeometry(Layer $layer, array $geoJSON): void
    {
        $geoJSONString = json_encode($geoJSON);

        DB::statement(
            'UPDATE layers SET geometry = ST_GeomFromGeoJSON(?), updated_at = NOW() WHERE id = ?',
            [$geoJSONString, $layer->id]
        );

        $layer->refresh();
    }

    /**
     * Extrai geometria de um array GeoJSON.
     */
    public function extractGeometry(array $geoJSON): ?array
    {
        if (!isset($geoJSON['type'])) {
            return null;
        }

        // Se for um Feature ou FeatureCollection, extrair a geometria
        if ($geoJSON['type'] === 'Feature') {
            return $geoJSON['geometry'] ?? null;
        }

        if ($geoJSON['type'] === 'FeatureCollection' && isset($geoJSON['features'][0])) {
            return $geoJSON['features'][0]['geometry'] ?? null;
        }

        // Se for uma geometria direta
        if (in_array($geoJSON['type'], ['Point', 'LineString', 'Polygon', 'MultiPoint', 'MultiLineString', 'MultiPolygon', 'GeometryCollection'])) {
            return $geoJSON;
        }

        return null;
    }

    /**
     * Converte todas as camadas para GeoJSON FeatureCollection.
     */
    public function getAllLayersAsGeoJSON(): array
    {
        try {
            $layers = Layer::all();
        } catch (\Exception $e) {
            Log::warning("Erro ao buscar camadas: " . $e->getMessage());
            return [
                'type' => 'FeatureCollection',
                'features' => [],
            ];
        }

        $features = [];

        foreach ($layers as $layer) {
            try {
                $feature = $layer->toGeoJSON();
                // Só adiciona se tiver geometria válida
                if (!empty($feature['geometry']) && $feature['geometry'] !== null) {
                    $features[] = $feature;
                }
            } catch (\Exception $e) {
                // Ignora erros individuais e continua com outras camadas
                Log::warning("Erro ao converter camada ID {$layer->id} para GeoJSON: " . $e->getMessage());
            }
        }

        return [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }
}

