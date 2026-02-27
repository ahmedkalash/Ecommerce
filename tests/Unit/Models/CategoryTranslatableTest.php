<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CategoryTranslatableTest extends TestCase
{
    use DatabaseTransactions;

    public function test_category_fields_are_translatable()
    {
        $category = Category::factory()->create([
            'name' => ['en' => 'Electronics', 'ar' => 'إلكترونيات'],
            'meta_title' => ['en' => 'Buy Electronics', 'ar' => 'شراء إلكترونيات'],
        ]);

        app()->setLocale('en');
        $this->assertEquals('Electronics', $category->name);
        $this->assertEquals('Buy Electronics', $category->meta_title);

        app()->setLocale('ar');
        $this->assertEquals('إلكترونيات', $category->name);
        $this->assertEquals('شراء إلكترونيات', $category->meta_title);
    }
}
