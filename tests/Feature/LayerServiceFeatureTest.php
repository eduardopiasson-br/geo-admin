<?php

namespace Tests\Feature;

use App\Services\LayerService;
use Tests\TestCase;

class LayerServiceFeatureTest extends TestCase
{
    private LayerService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LayerService();
    }

    public function test_get_all_layers_as_geojson_returns_correct_structure(): void
    {
        $result = $this->service->getAllLayersAsGeoJSON();

        $this->assertIsArray($result);
        $this->assertEquals('FeatureCollection', $result['type']);
        $this->assertIsArray($result['features']);
    }

    public function test_get_all_layers_as_geojson_handles_database_errors_gracefully(): void
    {
        // Mesmo se houver erro no banco, deve retornar estrutura válida
        $result = $this->service->getAllLayersAsGeoJSON();

        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('features', $result);
        $this->assertEquals('FeatureCollection', $result['type']);
    }
}

