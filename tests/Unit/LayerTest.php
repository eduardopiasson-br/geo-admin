<?php

namespace Tests\Unit;

use App\Models\Layer;
use Tests\TestCase;

class LayerTest extends TestCase
{
    public function test_validate_geojson_with_valid_polygon(): void
    {
        $validGeoJSON = [
            'type' => 'Polygon',
            'coordinates' => [
                [
                    [-47.0, -15.0],
                    [-47.5, -15.0],
                    [-47.5, -15.5],
                    [-47.0, -15.5],
                    [-47.0, -15.0],
                ]
            ]
        ];

        $this->assertTrue(Layer::validateGeoJSON($validGeoJSON));
    }

    public function test_validate_geojson_with_invalid_structure(): void
    {
        $invalidGeoJSON = [
            'coordinates' => [[-47.0, -15.0]]
        ];

        $this->assertFalse(Layer::validateGeoJSON($invalidGeoJSON));
    }

    public function test_validate_geojson_with_invalid_type(): void
    {
        $invalidGeoJSON = [
            'type' => 'InvalidType',
            'coordinates' => [[-47.0, -15.0]]
        ];

        $this->assertFalse(Layer::validateGeoJSON($invalidGeoJSON));
    }

    public function test_fillable_fields(): void
    {
        $layer = new Layer();
        $fillable = $layer->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertNotContains('geometry', $fillable);
    }
}

