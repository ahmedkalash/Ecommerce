<?php

namespace Tests\Feature\Scout;

use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Skip actual meilisearch network calls by pretending the driver is "null" for standard feature tests
        // unless explicitly testing the search engine integration.
        config(['scout.driver' => 'null']);
    }

    public function test_searchable_models_can_be_instantiated_without_crashing()
    {
        $product = Product::factory()->create([
            'name' => ['en' => 'Test Product'],
        ]);

        $this->assertNotNull($product->id);

        // Just verify the search method exists and doesn't throw exceptions from Scout trait
        $builder = Product::search('Test');

        $this->assertEquals('Test', $builder->query);
        $this->assertInstanceOf(Product::class, $builder->model);
    }
}
