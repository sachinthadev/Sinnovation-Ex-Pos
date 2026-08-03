<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Ledger extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Ledger';

    protected string $view = 'filament.pages.ledger';
}
