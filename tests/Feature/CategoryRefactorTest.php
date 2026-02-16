<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CategoryRefactorTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test product category relationship via pivot table.
     */
    public function test_product_belongs_to_multiple_categories()
    {
        // 1. Create Categories
        $cat1 = Category::create([
            'parent_id' => null,
            'slug' => 'cat-1',
            'digital' => 0, // Assuming required field based on Service
        ]); // Use minimal fields or factory if available?
        // Let's assume minimal fields work or use raw create.

        $cat2 = Category::create([
            'parent_id' => $cat1->id,
            'slug' => 'cat-2',
            'digital' => 0,
        ]);

        // 2. Create Product
        // Product creation requires numerous fields based on ProductService/Validation?
        // We'll create minimal viable product directly using Model to bypass complex service validation for this unit test.
        $product = Product::forceCreate([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'user_id' => 1, // Assuming user 1 exists or use factory
            'unit_price' => 100,
            'published' => 1,
            'current_stock' => 10,
            // 'category_id' => $cat1->id, // Should NOT be used
        ]);

        // 3. Attach Categories
        $product->categories()->attach([$cat1->id, $cat2->id]);

        // 4. Assertions
        $this->assertCount(2, $product->categories);
        $this->assertTrue($product->categories->contains($cat1));
        $this->assertTrue($product->categories->contains($cat2));

        // 5. Reverse assertion
        $this->assertTrue($cat1->products->contains($product));

        // 6. Test Adjacency
        $this->assertTrue($cat2->parent_id == $cat1->id);
        // Verify recursive relationship usage (staudenmeir logic) if configured correctly
        // $this->assertTrue($cat1->isAncestorOf($cat2)); // Function from trait
    }

    /**
     * Test sync method works on pivot.
     */
    public function test_product_category_sync()
    {
        $cat1 = Category::create(['slug' => 'sync-1', 'digital' => 0]);
        $cat2 = Category::create(['slug' => 'sync-2', 'digital' => 0]);

        $product = Product::forceCreate([
            'name' => 'Sync Product',
            'slug' => 'sync-product',
            'user_id' => 1,
            'unit_price' => 50,
        ]);

        $product->categories()->sync([$cat1->id]);
        $this->assertCount(1, $product->refresh()->categories);
        $this->assertTrue($product->categories->contains($cat1));

        $product->categories()->sync([$cat2->id]);
        $product->refresh();
        $this->assertCount(1, $product->categories);
        $this->assertTrue($product->categories->contains($cat2));
        $this->assertFalse($product->categories->contains($cat1));
    }
}
