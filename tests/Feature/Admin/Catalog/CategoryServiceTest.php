<?php

namespace Tests\Feature\Admin\Catalog;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryServiceTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected CategoryService $categoryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryService = app(CategoryService::class);
    }

    /** @test */
    public function it_can_store_a_category_with_all_fields()
    {
        $data = [
            'name' => 'Test Category',
            'slug' => 'test-category-slug',
            'parent_id' => null,
            'commision_rate' => 10,
            'featured' => 1,
            'top' => 1,
            'digital' => 0,
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Desc',
        ];

        $category = $this->categoryService->store($data);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Test Category',
            'slug' => 'test-category-slug',
            'commision_rate' => 10,
            'featured' => 1,
        ]);
    }

    /** @test */
    public function it_auto_generates_slug_and_meta_title_if_missing()
    {
        $data = [
            'name' => 'Auto Gen Category',
            // slug missing
            // meta_title missing
        ];

        $category = $this->categoryService->store($data);

        $this->assertEquals('auto-gen-category', $category->slug);
        $this->assertEquals('Auto Gen Category', $category->meta_title);
    }

    /** @test */
    public function it_normalizes_parent_id_zero_to_null()
    {
        $data = [
            'name' => 'Child Category',
            'parent_id' => 0,
        ];

        $category = $this->categoryService->store($data);

        $this->assertNull($category->parent_id);
    }

    /** @test */
    public function it_can_update_a_category()
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $data = [
            'name' => 'Updated Name',
            'slug' => 'updated-name',
        ];

        $updatedCategory = $this->categoryService->update($data, $category);

        $this->assertEquals('Updated Name', $updatedCategory->name);
        $this->assertEquals('updated-name', $updatedCategory->slug);
    }

    /** @test */
    public function it_can_delete_a_category()
    {
        $category = Category::factory()->create();

        $this->categoryService->delete($category);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        // If soft deletes are enabled, use assertSoftDeleted
    }

    /** @test */
    public function it_can_bulk_delete_categories()
    {
        $categories = Category::factory()->count(3)->create();
        $ids = $categories->pluck('id');

        $this->categoryService->bulkDelete($categories);

        foreach ($ids as $id) {
            $this->assertDatabaseMissing('categories', ['id' => $id]);
        }
    }
}
