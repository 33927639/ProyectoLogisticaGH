<?php

namespace App\Filament\Resources\TblRoleResource\Pages;

use App\Filament\Resources\TblRoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTblRoles extends ListRecords
{
    protected static string $resource = TblRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
