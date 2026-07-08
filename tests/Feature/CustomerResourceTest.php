<?php

namespace Tests\Feature;

use App\Filament\Resources\CustomerResource;
use Filament\Schemas\Schema;
use Tests\TestCase;

class CustomerResourceTest extends TestCase
{
    public function test_vehicle_model_field_is_required_in_the_form_schema(): void
    {
        $schema = CustomerResource::form(Schema::make());
        $components = $schema->getComponents();
        $vehicleModel = collect($components)->first(fn ($component) => $component->getName() === 'vehicle_model');

        $this->assertNotNull($vehicleModel);
        $this->assertTrue($vehicleModel->isRequired());
    }
}
