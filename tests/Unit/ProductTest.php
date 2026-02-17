<?php

namespace Tests\Unit;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $product = new Product;

        $expectedFillable = [
            'name',
            'added_by',
            'user_id',
            'brand_id',
            'description',
            'published',
            'approved',
            'shipping_type',
            'shipping_cost',
            'est_shipping_days',
            'meta_title',
            'meta_description',
            'slug',
            'rating',
            'barcode',
            'digital',
            'file_name',
            'file_path',
            'external_link',
            'external_link_btn',
            'wholesale_product',
            'frequently_bought_selection_type',
            'has_warranty',
            'warranty_id',
            'warranty_note_id',
            'extra_attributes',
        ];

        $this->assertEqualsCanonicalizing($expectedFillable, $product->getFillable());
    }

    /** @test */
    public function it_protects_against_mass_assignment_of_non_fillable_attributes()
    {
        $data = [
            'name' => 'Test Product',
            'slug' => 'test-product',
            'id' => 999, // Should be guarded
            'created_at' => '2023-01-01 00:00:00', // Should be guarded
        ];

        $product = new Product($data);

        $this->assertEquals('Test Product', $product->name);
        $this->assertNull($product->id);
        $this->assertNull($product->created_at);
    }

    /** @test */
    public function it_has_stocks_relationship()
    {
        $product = Product::factory()->create();
        $stock = ProductStock::factory()->create(['product_id' => $product->id]);

        $this->assertTrue($product->stocks->contains($stock));
        $this->assertInstanceOf(ProductStock::class, $product->stocks->first());
    }

    /** @test */
    public function it_has_brand_relationship()
    {
        $brand = Brand::factory()->create();
        $product = Product::factory()->create(['brand_id' => $brand->id]);

        $this->assertInstanceOf(Brand::class, $product->brand);
        $this->assertEquals($brand->id, $product->brand->id);
    }

    /** @test */
    public function it_has_user_relationship()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $product->user);
        $this->assertEquals($user->id, $product->user->id);
    }

    /** @test */
    public function it_has_categories_relationship()
    {
        $product = Product::factory()->create();
        $category = Category::factory()->create();

        $product->categories()->attach($category);

        $this->assertTrue($product->categories->contains($category));
        $this->assertInstanceOf(Category::class, $product->categories->first());
    }
}
