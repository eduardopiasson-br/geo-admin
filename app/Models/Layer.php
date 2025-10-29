<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Layer extends Model
{
    protected $fillable = [
        'name',
        // geometry não é fillable porque é gerenciado via SQL nativo PostGIS
    ];

    /**
     * Converter geometria PostGIS para GeoJSON usando SQL nativo
     * Retorna um Feature GeoJSON completo com propriedades
     */
    public function toGeoJSON(): array
    {
        try {
            $result = DB::selectOne(
                "SELECT ST_AsGeoJSON(geometry) as geojson FROM layers WHERE id = ?",
                [$this->id]
            );

            if (!$result || !$result->geojson) {
                // Se não há geometria, retornar estrutura vazia
                return [
                    'type' => 'Feature',
                    'id' => $this->id,
                    'properties' => [
                        'id' => $this->id,
                        'name' => $this->name ?? '',
                        'created_at' => $this->created_at?->toIso8601String(),
                        'updated_at' => $this->updated_at?->toIso8601String(),
                    ],
                    'geometry' => null,
                ];
            }

            $geometry = json_decode($result->geojson, true);

            return [
                'type' => 'Feature',
                'id' => $this->id,
                'properties' => [
                    'id' => $this->id,
                    'name' => $this->name ?? '',
                    'created_at' => $this->created_at?->toIso8601String(),
                    'updated_at' => $this->updated_at?->toIso8601String(),
                ],
                'geometry' => $geometry,
            ];
        } catch (\Exception $e) {
            // Em caso de erro (ex: PostGIS não disponível), retornar estrutura sem geometria
            return [
                'type' => 'Feature',
                'id' => $this->id ?? null,
                'properties' => [
                    'id' => $this->id ?? null,
                    'name' => $this->name ?? '',
                    'created_at' => $this->created_at?->toIso8601String(),
                    'updated_at' => $this->updated_at?->toIso8601String(),
                ],
                'geometry' => null,
            ];
        }
    }


    /**
     * Validar se o GeoJSON contém uma geometria válida
     */
    public static function validateGeoJSON(array $geoJSON): bool
    {
        // Verificar estrutura básica do GeoJSON
        if (!isset($geoJSON['type'])) {
            return false;
        }

        $validTypes = ['Point', 'LineString', 'Polygon', 'MultiPoint', 'MultiLineString', 'MultiPolygon', 'GeometryCollection'];
        
        if (!in_array($geoJSON['type'], $validTypes)) {
            return false;
        }

        // Verificar se tem coordenadas (exceto GeometryCollection)
        if ($geoJSON['type'] !== 'GeometryCollection' && !isset($geoJSON['coordinates'])) {
            return false;
        }

        return true;
    }
}

