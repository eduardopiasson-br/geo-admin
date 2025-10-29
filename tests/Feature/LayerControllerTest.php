<?php

namespace Tests\Feature;

use Tests\TestCase;

class LayerControllerTest extends TestCase
{
    public function test_api_layers_endpoint_returns_feature_collection(): void
    {
        $response = $this->get('/api/layers');

        // Verifica que retorna status 200
        $response->assertStatus(200);
        
        // Verifica que é JSON
        $response->assertJsonStructure([
            'type',
            'features',
        ]);
        
        // Verifica que tem a estrutura correta de FeatureCollection
        $data = $response->json();
        $this->assertEquals('FeatureCollection', $data['type']);
        $this->assertIsArray($data['features']);
    }

    public function test_api_layers_endpoint_returns_empty_when_no_layers(): void
    {
        $response = $this->get('/api/layers');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertEquals('FeatureCollection', $data['type']);
        $this->assertIsArray($data['features']);
        // Quando não há camadas, features deve ser array vazio
        $this->assertEmpty($data['features']);
    }

    public function test_api_layers_endpoint_returns_valid_json_structure(): void
    {
        $response = $this->get('/api/layers');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        
        $data = $response->json();
        $this->assertArrayHasKey('type', $data);
        $this->assertArrayHasKey('features', $data);
        $this->assertEquals('FeatureCollection', $data['type']);
    }
}

