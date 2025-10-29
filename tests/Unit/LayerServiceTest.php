<?php

namespace Tests\Unit;

use App\Services\LayerService;
use Tests\TestCase;

class LayerServiceTest extends TestCase
{
    private LayerService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LayerService();
    }

    public function test_extract_geometry_from_feature(): void
    {
        $geoJSON = [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Polygon',
                'coordinates' => [[[-47.0, -15.0], [-47.5, -15.0], [-47.5, -15.5], [-47.0, -15.5], [-47.0, -15.0]]]
            ]
        ];

        $geometry = $this->service->extractGeometry($geoJSON);

        $this->assertNotNull($geometry);
        $this->assertEquals('Polygon', $geometry['type']);
    }

    public function test_extract_geometry_from_feature_collection(): void
    {
        $geoJSON = [
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [-47.0, -15.0]
                    ]
                ]
            ]
        ];

        $geometry = $this->service->extractGeometry($geoJSON);

        $this->assertNotNull($geometry);
        $this->assertEquals('Point', $geometry['type']);
    }

    public function test_extract_geometry_from_direct_geometry(): void
    {
        $geoJSON = [
            'type' => 'Polygon',
            'coordinates' => [[[-47.0, -15.0], [-47.5, -15.0], [-47.5, -15.5], [-47.0, -15.5], [-47.0, -15.0]]]
        ];

        $geometry = $this->service->extractGeometry($geoJSON);

        $this->assertNotNull($geometry);
        $this->assertEquals($geoJSON, $geometry);
    }

    public function test_extract_geometry_returns_null_for_invalid_structure(): void
    {
        $geoJSON = ['invalid' => 'data'];

        $geometry = $this->service->extractGeometry($geoJSON);

        $this->assertNull($geometry);
    }

    public function test_get_all_layers_as_geojson_returns_empty_when_no_layers(): void
    {
        $result = $this->service->getAllLayersAsGeoJSON();

        $this->assertEquals('FeatureCollection', $result['type']);
        $this->assertIsArray($result['features']);
        $this->assertEmpty($result['features']);
    }
}

