<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Criar extensão PostGIS se ainda não existir
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');

        Schema::create('layers', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->timestamps();
        });

        // Adicionar coluna geometry usando SQL direto para PostGIS
        DB::statement('ALTER TABLE layers ADD COLUMN geometry geometry(Geometry, 4326)');
        
        // Criar índice espacial GIST para melhor performance em consultas espaciais
        DB::statement('CREATE INDEX layers_geometry_idx ON layers USING GIST (geometry)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layers');
    }
};
