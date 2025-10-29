<?php

namespace App\Filament\Painel\Resources\LayerResource\Pages;

use App\Filament\Painel\Resources\LayerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLayers extends ListRecords
{
    protected static string $resource = LayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

