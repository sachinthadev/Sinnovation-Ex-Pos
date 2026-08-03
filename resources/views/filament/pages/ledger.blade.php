<x-filament-panels::page>
    <div class="flex gap-4">
        <x-filament::button tag="a" href="{{ url('/admin/income') }}" size="lg" color="success">
            Income
        </x-filament::button>

        <x-filament::button size="lg" color="danger">
            Expense
        </x-filament::button>

        <x-filament::button size="lg" color="warning">
            Salary
        </x-filament::button>
    </div>
</x-filament-panels::page>
