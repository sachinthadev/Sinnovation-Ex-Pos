<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\EmployeeResource;
use App\Filament\Resources\ServiceResource;
use App\Models\Employee;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('add_employee')
                ->label('Add New Employee')
                ->color('success')
                ->form([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Select::make('labour_type')
                        ->label('Labour Type')
                        ->options([
                            'mechanic' => 'Mechanic',
                            'supporter' => 'Supporter',
                        ])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    Employee::create($data);
                }),
            Action::make('view_employees')
                ->label('View Employee List')
                ->color('gray')
                ->icon('heroicon-o-user-group')
                ->url(fn (): string => EmployeeResource::getUrl('index')),
            CreateAction::make('add_service')
                ->label('New Services')
                ->color('primary')
                ->form([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('price')
                        ->numeric()
                        ->default(0)
                        ->required(),
                    TextInput::make('commission_percentage')
                        ->label('Commission (%)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->step(0.01)
                        ->suffix('%')
                        ->default(0)
                        ->required(),
                ])
                ->mutateFormDataUsing(function (array $data): array {
                    $data['type'] = 'service';
                    return $data;
                }),
        ];
    }
}
