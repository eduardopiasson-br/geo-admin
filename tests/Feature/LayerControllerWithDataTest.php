<?php

namespace Tests\Feature;

use Tests\TestCase;

class LayerControllerWithDataTest extends TestCase
{
    public function test_api_layers_returns_valid_json_structure_with_data(): void
    {
        $response = $this->getJson('/api/layers');

        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertIsArray($data);
        $this->assertArrayHasKey('type', $data);
        $this->assertArrayHasKey('features', $data);
        $this->assertEquals('FeatureCollection', $data['type']);
        $this->assertIsArray($data['features']);
        
        // Se houver features, verificar estrutura
        if (!empty($data['features'])) {
            $firstFeature = $data['features'][0];
            $this->assertArrayHasKey('type', $firstFeature);
            $this->assertArrayHasKey('properties', $firstFeature);
            $this->assertArrayHasKey('geometry', $firstFeature);
            $this->assertEquals('Feature', $firstFeature['type']);
        }
    }

    public function test_api_layers_response_is_always_valid_geojson(): void
    {
        $response = $this->getJson('/api/layers');

        $response->assertStatus(200);
        $data = $response->json();
        
        // Validar estrutura mínima do GeoJSON FeatureCollection
        $this->assertEquals('FeatureCollection', $data['type']);
        $this->assertIsArray($data['features']);
        
        // Todas as features devem ter estrutura válida se existirem
        foreach ($data['features'] as $feature) {
            $this->assertArrayHasKey('type', $feature);
            $this->assertArrayHasKey('properties', $feature);
            if (isset($feature['geometry'])) {
                $this->assertIsArray($feature['geometry']);
            }
        }
    }
}

