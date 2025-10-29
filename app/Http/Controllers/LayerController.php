<?php

namespace App\Http\Controllers;

use App\Services\LayerService;
use Illuminate\Http\JsonResponse;

class LayerController extends Controller
{
    public function __construct(
        private readonly LayerService $layerService
    ) {
    }

    /**
     * Retorna todas as camadas em formato GeoJSON FeatureCollection
     */
    public function index(): JsonResponse
    {
        $geoJSON = $this->layerService->getAllLayersAsGeoJSON();

        return response()->json(
            $geoJSON,
            200,
            [],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}

