<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use Filament\Pages\Page;
use App\Models\Setting;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class Settings extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.settings';

    public array $settings = [];

    public function mount(): void
    { 
        $this->settings = Setting::pluck('value', 'key')->toArray();
        $this->settings['currency_symbol'] = $this->settings['currency_symbol'] ?? '$';
    }

    public function save(): void
    {
        foreach ($this->settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        session()->flash('success', 'Settings updated successfully!');
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addUser')
                ->label('Add New User')
                ->button()
                ->modalHeading('Add New User')
                ->form([
                    TextInput::make('name')
                        ->label('Name')
                        ->required(),
                    TextInput::make('username')
                        ->label('Username')
                        ->required()
                        ->unique(User::class, 'username'),
                    TextInput::make('password')
                        ->label('Password')
                        ->required()
                        ->password()
                        ->minLength(8),
                    Radio::make('role')
                        ->label('Role')
                        ->options([
                            'admin' => 'Admin',
                            'employee' => 'Employee',
                        ])
                        ->default('employee')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $this->createUser($data);
                }),
        ];
    }

    protected function createUser(array $data): void
    {
        User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'password' => $data['password'],
            'role' => $data['role'],
        ]);

        Notification::make()
            ->title('User created successfully')
            ->success()
            ->send();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('settings.site_name')
                ->label('Site Name')
                ->required(),
            TextInput::make('settings.site_email')
                ->label('Email')
                ->email(),
            Textarea::make('settings.site_description')
                ->label('Description'),
            TextInput::make('settings.currency_symbol')
                ->default('$')
                ->label('Currency symbol'),
        ]);
    }
}
