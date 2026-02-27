<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductTranslatableTest extends TestCase
{
    use DatabaseTransactions;

    public function test_product_name_is_stored_as_json_and_retrieved_by_locale()
    {
        $product = Product::factory()->create([
            'name' => ['en' => 'English Name', 'ar' => 'Arabic Name'],
        ]);

        // Assert it retrieves the correct string based on active locale
        app()->setLocale('en');
        $this->assertEquals('English Name', $product->name);

        app()->setLocale('ar');
        $this->assertEquals('Arabic Name', $product->name);

        // Assert raw JSON storage in the database
        $rawAttributes = $product->getAttributes();
        $this->assertJsonStringEqualsJsonString(
            '{"en":"English Name","ar":"Arabic Name"}',
            $rawAttributes['name']
        );
    }

    public function test_to_searchable_array_expands_locales()
    {

        $product = Product::factory()->create([
            'name' => ['en' => 'English Apple', 'ar' => 'تفاحة'],
            'description' => ['en' => 'Tasty', 'ar' => 'لذيذ'],
        ]);

        $searchableArray = $product->toSearchableArray();

        $this->assertArrayHasKey('id', $searchableArray);

        // Assert English fields
        $this->assertEquals('English Apple', $searchableArray['name_en']);
        $this->assertEquals('Tasty', $searchableArray['description_en']);

        // Assert Arabic fields
        $this->assertEquals('تفاحة', $searchableArray['name_ar']);
        $this->assertEquals('لذيذ', $searchableArray['description_ar']);
    }
}
