<?php

namespace App\Filament\Painel\Widgets;

use App\Models\Layer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LayersCountWidget extends BaseWidget
{
    /**
     * Exibe estatísticas sobre as camadas cadastradas
     */
    protected function getStats(): array
    {
        $totalLayers = Layer::count();
        
        // Contar camadas com geometria usando SQL
        $layersWithGeometry = Layer::query()
            ->whereRaw('geometry IS NOT NULL')
            ->count();

        return [
            Stat::make('Total de Camadas', $totalLayers)
                ->description('Camadas cadastradas no sistema')
                ->descriptionIcon('heroicon-o-map')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5]), // Gráfico de exemplo
            
            Stat::make('Camadas com Geometria', $layersWithGeometry)
                ->description('Camadas com dados geoespaciais válidos')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('info')
                ->url(route('filament.painel.resources.layers.index')),
            
            Stat::make('Camadas sem Geometria', $totalLayers - $layersWithGeometry)
                ->description('Camadas pendentes de upload')
                ->descriptionIcon('heroicon-o-exclamation-circle')
                ->color('warning'),
        ];
    }
}

