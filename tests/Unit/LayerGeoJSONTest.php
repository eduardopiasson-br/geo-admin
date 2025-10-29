<?php

namespace Tests\Unit;

use App\Models\Layer;
use Tests\TestCase;

class LayerGeoJSONTest extends TestCase
{
    public function test_validate_geojson_with_multi_polygon(): void
    {
        $validGeoJSON = [
            'type' => 'MultiPolygon',
            'coordinates' => [
                [[[-47.0, -15.0], [-47.5, -15.0], [-47.5, -15.5], [-47.0, -15.5], [-47.0, -15.0]]],
                [[[-48.0, -16.0], [-48.5, -16.0], [-48.5, -16.5], [-48.0, -16.5], [-48.0, -16.0]]]
            ]
        ];

        $this->assertTrue(Layer::validateGeoJSON($validGeoJSON));
    }

    public function test_validate_geojson_with_point(): void
    {
        $validGeoJSON = [
            'type' => 'Point',
            'coordinates' => [-47.0, -15.0]
        ];

        $this->assertTrue(Layer::validateGeoJSON($validGeoJSON));
    }

    public function test_validate_geojson_with_line_string(): void
    {
        $validGeoJSON = [
            'type' => 'LineString',
            'coordinates' => [[-47.0, -15.0], [-47.5, -15.5]]
        ];

        $this->assertTrue(Layer::validateGeoJSON($validGeoJSON));
    }

    public function test_validate_geojson_with_geometry_collection(): void
    {
        $validGeoJSON = [
            'type' => 'GeometryCollection',
            'geometries' => [
                [
                    'type' => 'Point',
                    'coordinates' => [-47.0, -15.0]
                ]
            ]
        ];

        $this->assertTrue(Layer::validateGeoJSON($validGeoJSON));
    }

    public function test_validate_geojson_with_missing_coordinates(): void
    {
        $invalidGeoJSON = [
            'type' => 'Polygon'
            // Sem coordinates
        ];

        $this->assertFalse(Layer::validateGeoJSON($invalidGeoJSON));
    }

    public function test_validate_geojson_with_multi_point(): void
    {
        $validGeoJSON = [
            'type' => 'MultiPoint',
            'coordinates' => [[-47.0, -15.0], [-48.0, -16.0]]
        ];

        $this->assertTrue(Layer::validateGeoJSON($validGeoJSON));
    }

    public function test_validate_geojson_with_multi_line_string(): void
    {
        $validGeoJSON = [
            'type' => 'MultiLineString',
            'coordinates' => [
                [[-47.0, -15.0], [-47.5, -15.5]],
                [[-48.0, -16.0], [-48.5, -16.5]]
            ]
        ];

        $this->assertTrue(Layer::validateGeoJSON($validGeoJSON));
    }
}

