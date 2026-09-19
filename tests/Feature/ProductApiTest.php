<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Passport::actingAs(User::factory()->create(), [
            'products:read',
            'products:create',
            'products:update',
            'products:delete',
        ]);
    }

    public function test_products_can_be_listed(): void
    {
        Product::factory(3)->create();

        $this->getJson('/api/products')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_product_can_be_created(): void
    {
        $payload = [
            'name' => 'Monitor 24 pulgadas',
            'description' => 'Monitor Full HD.',
            'price' => 1299.99,
            'stock' => 8,
            'active' => true,
        ];

        $this->postJson('/api/products', $payload)
            ->assertCreated()
            ->assertJsonPath('data.name', $payload['name'])
            ->assertJsonPath('data.stock', $payload['stock']);

        $this->assertDatabaseHas('products', ['name' => $payload['name']]);
    }

    public function test_product_creation_validates_required_fields(): void
    {
        $this->postJson('/api/products', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'price', 'stock']);
    }

    public function test_product_can_be_updated_and_deleted(): void
    {
        $product = Product::factory()->create();

        $this->patchJson("/api/products/{$product->id}", ['stock' => 0])
            ->assertOk()
            ->assertJsonPath('data.stock', 0);

        $this->deleteJson("/api/products/{$product->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_missing_product_returns_not_found(): void
    {
        $this->getJson('/api/products/999999')->assertNotFound();
    }
}
