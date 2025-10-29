<?php

namespace Tests\Feature;

use Tests\TestCase;

class MapPageTest extends TestCase
{
    public function test_map_page_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('map');
    }

    public function test_map_page_contains_required_elements(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('arcgis-map', false);
        $response->assertSee('Carregando mapa e camadas', false);
        $response->assertSee('js.arcgis.com/4.34', false);
        $response->assertSee('map-components', false);
    }

    public function test_map_page_has_api_endpoint_reference(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Verifica se o JavaScript contém referência à API
        $content = $response->getContent();
        $this->assertStringContainsString('/api/layers', $content);
    }

    public function test_map_page_includes_error_handling(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $content = $response->getContent();
        
        // Verifica se há tratamento de erro no JavaScript
        $this->assertStringContainsString('errorMessage', $content);
        $this->assertStringContainsString('showError', $content);
        $this->assertStringContainsString('catch', $content);
    }

    public function test_map_page_includes_loading_overlay(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $content = $response->getContent();
        
        // Verifica se há overlay de loading
        $this->assertStringContainsString('loadingOverlay', $content);
        $this->assertStringContainsString('hideLoading', $content);
    }

    public function test_map_page_has_timeout_safety(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $content = $response->getContent();
        
        // Verifica se há timeout de segurança
        $this->assertStringContainsString('setTimeout', $content);
        $this->assertStringContainsString('10000', $content); // 10 segundos
    }
}

