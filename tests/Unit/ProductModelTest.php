<?php

namespace Tests\Unit;

use App\Models\Product;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
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
            'id' => 999,
            'created_at' => '2023-01-01 00:00:00',
            'unit_price' => 100, // Explicit check for removed/unsaved field
        ];

        $product = new Product($data);

        $this->assertEquals('Test Product', $product->name);
        $this->assertNull($product->id);
        $this->assertNull($product->created_at);
        $this->assertNull($product->unit_price);
    }
}
