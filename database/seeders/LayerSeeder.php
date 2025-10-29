<?php

namespace Database\Seeders;

use App\Models\Layer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LayerSeeder extends Seeder
{
    /**
     * Seed exemplo de camadas geográficas.
     */
    public function run(): void
    {
        // Limpar camadas existentes (opcional - comentar se não quiser limpar)
        // Layer::truncate();

        // Exemplo 1: Região do Distrito Federal (Brasil)
        $dfGeoJSON = [
            'type' => 'Polygon',
            'coordinates' => [
                [
                    [-47.95, -15.95],
                    [-47.60, -15.95],
                    [-47.60, -15.55],
                    [-47.95, -15.55],
                    [-47.95, -15.95]
                ]
            ]
        ];

        DB::statement(
            'INSERT INTO layers (name, geometry, created_at, updated_at) 
             VALUES (?, ST_GeomFromGeoJSON(?), NOW(), NOW())',
            ['Distrito Federal - Brasil', json_encode($dfGeoJSON)]
        );

        // Exemplo 2: Região de São Paulo (Brasil)
        $spGeoJSON = [
            'type' => 'Polygon',
            'coordinates' => [
                [
                    [-46.80, -23.70],
                    [-46.40, -23.70],
                    [-46.40, -23.40],
                    [-46.80, -23.40],
                    [-46.80, -23.70]
                ]
            ]
        ];

        DB::statement(
            'INSERT INTO layers (name, geometry, created_at, updated_at) 
             VALUES (?, ST_GeomFromGeoJSON(?), NOW(), NOW())',
            ['Região Metropolitana de São Paulo', json_encode($spGeoJSON)]
        );

        // Exemplo 3: Região do Rio de Janeiro (Brasil)
        $rjGeoJSON = [
            'type' => 'Polygon',
            'coordinates' => [
                [
                    [-43.40, -23.00],
                    [-43.10, -23.00],
                    [-43.10, -22.75],
                    [-43.40, -22.75],
                    [-43.40, -23.00]
                ]
            ]
        ];

        DB::statement(
            'INSERT INTO layers (name, geometry, created_at, updated_at) 
             VALUES (?, ST_GeomFromGeoJSON(?), NOW(), NOW())',
            ['Baía de Guanabara - Rio de Janeiro', json_encode($rjGeoJSON)]
        );

        // Exemplo 4: MultiPolygon - Região Nordeste do Brasil
        $neGeoJSON = [
            'type' => 'MultiPolygon',
            'coordinates' => [
                [
                    [
                        [-38.5, -8.5],
                        [-38.0, -8.5],
                        [-38.0, -8.0],
                        [-38.5, -8.0],
                        [-38.5, -8.5]
                    ]
                ],
                [
                    [
                        [-40.0, -12.5],
                        [-39.5, -12.5],
                        [-39.5, -12.0],
                        [-40.0, -12.0],
                        [-40.0, -12.5]
                    ]
                ]
            ]
        ];

        DB::statement(
            'INSERT INTO layers (name, geometry, created_at, updated_at) 
             VALUES (?, ST_GeomFromGeoJSON(?), NOW(), NOW())',
            ['Região Nordeste - Múltiplas Áreas', json_encode($neGeoJSON)]
        );

        // Exemplo 5: Região Sul do Brasil
        $sulGeoJSON = [
            'type' => 'Polygon',
            'coordinates' => [
                [
                    [-51.0, -30.0],
                    [-49.0, -30.0],
                    [-49.0, -27.0],
                    [-51.0, -27.0],
                    [-51.0, -30.0]
                ]
            ]
        ];

        DB::statement(
            'INSERT INTO layers (name, geometry, created_at, updated_at) 
             VALUES (?, ST_GeomFromGeoJSON(?), NOW(), NOW())',
            ['Região Sul do Brasil', json_encode($sulGeoJSON)]
        );

        // Exemplo 6: Mato Grosso do Sul - Região Norte (Campo Grande)
        $msNorteGeoJSON = [
            'type' => 'Polygon',
            'coordinates' => [
                [
                    [-54.8, -20.4],
                    [-54.2, -20.4],
                    [-54.2, -19.9],
                    [-54.8, -19.9],
                    [-54.8, -20.4]
                ]
            ]
        ];

        DB::statement(
            'INSERT INTO layers (name, geometry, created_at, updated_at) 
             VALUES (?, ST_GeomFromGeoJSON(?), NOW(), NOW())',
            ['Mato Grosso do Sul - Região Norte', json_encode($msNorteGeoJSON)]
        );

        // Exemplo 7: Mato Grosso do Sul - Região Central (sobrepondo parcialmente)
        $msCentroGeoJSON = [
            'type' => 'Polygon',
            'coordinates' => [
                [
                    [-55.0, -20.7],
                    [-54.3, -20.7],
                    [-54.3, -20.1],
                    [-55.0, -20.1],
                    [-55.0, -20.7]
                ]
            ]
        ];

        DB::statement(
            'INSERT INTO layers (name, geometry, created_at, updated_at) 
             VALUES (?, ST_GeomFromGeoJSON(?), NOW(), NOW())',
            ['Mato Grosso do Sul - Região Central (Sobreposição)', json_encode($msCentroGeoJSON)]
        );

        // Exemplo 8: Mato Grosso do Sul - Região Sul (Ponta Porã)
        $msSulGeoJSON = [
            'type' => 'Polygon',
            'coordinates' => [
                [
                    [-55.5, -22.5],
                    [-55.0, -22.5],
                    [-55.0, -22.0],
                    [-55.5, -22.0],
                    [-55.5, -22.5]
                ]
            ]
        ];

        DB::statement(
            'INSERT INTO layers (name, geometry, created_at, updated_at) 
             VALUES (?, ST_GeomFromGeoJSON(?), NOW(), NOW())',
            ['Mato Grosso do Sul - Região Sul', json_encode($msSulGeoJSON)]
        );

        // Exemplo 9: Mato Grosso do Sul - Área de Interseção (sobrepondo múltiplas regiões)
        $msIntersecaoGeoJSON = [
            'type' => 'Polygon',
            'coordinates' => [
                [
                    [-54.6, -20.5],
                    [-54.4, -20.5],
                    [-54.4, -20.3],
                    [-54.6, -20.3],
                    [-54.6, -20.5]
                ]
            ]
        ];

        DB::statement(
            'INSERT INTO layers (name, geometry, created_at, updated_at) 
             VALUES (?, ST_GeomFromGeoJSON(?), NOW(), NOW())',
            ['MS - Área de Interseção (Sobreposição)', json_encode($msIntersecaoGeoJSON)]
        );

        // Exemplo 10: Mato Grosso do Sul - Região Leste (Dourados)
        $msLesteGeoJSON = [
            'type' => 'Polygon',
            'coordinates' => [
                [
                    [-54.5, -22.2],
                    [-54.0, -22.2],
                    [-54.0, -21.7],
                    [-54.5, -21.7],
                    [-54.5, -22.2]
                ]
            ]
        ];

        DB::statement(
            'INSERT INTO layers (name, geometry, created_at, updated_at) 
             VALUES (?, ST_GeomFromGeoJSON(?), NOW(), NOW())',
            ['Mato Grosso do Sul - Região Leste', json_encode($msLesteGeoJSON)]
        );

        // Exemplo 11: Mato Grosso do Sul - Área Maior (englobando várias regiões)
        $msGrandeGeoJSON = [
            'type' => 'Polygon',
            'coordinates' => [
                [
                    [-56.0, -23.0],
                    [-54.0, -23.0],
                    [-54.0, -19.5],
                    [-56.0, -19.5],
                    [-56.0, -23.0]
                ]
            ]
        ];

        DB::statement(
            'INSERT INTO layers (name, geometry, created_at, updated_at) 
             VALUES (?, ST_GeomFromGeoJSON(?), NOW(), NOW())',
            ['Mato Grosso do Sul - Região Completa (Sobreposição Múltipla)', json_encode($msGrandeGeoJSON)]
        );

        $this->command->info('Seeders de camadas criados com sucesso!');
        $this->command->info('Total de camadas criadas: 11 (incluindo 6 de Mato Grosso do Sul com sobreposições)');
    }
}

