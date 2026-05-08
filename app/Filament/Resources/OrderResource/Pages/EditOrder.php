<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Filament\Actions\Action;
use Illuminate\Support\HtmlString;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected string $view = 'filament.pages.edit-order';


    protected function getHeaderActions(): array
    {
        return [
            Action::make('addService')
                ->label('Add Service')
                ->icon('heroicon-o-plus')
                ->color('secondary')
                ->extraAttributes(fn () => [
                    'x-on:click' => new HtmlString("window.livewire && window.livewire.emit('openCustomServiceRow')"),
                ]),
            Action::make('print')
                ->label('Print')
                ->livewireClickHandlerEnabled(false)
                ->extraAttributes(fn(Order $record) => [
                    'class' => 'md:flex hidden',
                    'x-on:click' => new HtmlString("printJS({ printable:'" . url('print/'.$record->id) . "', type: 'pdf' })")
                ])
                ->icon('heroicon-o-printer')
                ->color('success'),
            Action::make('preview')
                ->label('Preview')
                ->url(fn(Order $record) => url('/print/' . $record->id))
                ->openUrlInNewTab()
                ->icon('heroicon-o-document-text')
                ->color('success'),
            DeleteAction::make()->visible(fn () => auth()->user()->role === 'admin')
        ];
    }
}
