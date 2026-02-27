<?php

namespace Tests\Feature\Admin\Catalog;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['user_type' => 'admin']);
    }

    /** @test */
    public function admin_can_list_categories()
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin, 'admin')
            ->get(CategoryResource::getUrl('index'))
            ->assertSuccessful()
            ->assertSee($category->name);
    }

    /** @test */
    public function admin_can_create_a_category()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(CategoryResource\Pages\CreateCategory::class)
            ->fillForm([
                'name' => 'New Filament Category',
                'slug' => 'new-filament-category',
                'parent_id' => null,
                'featured' => true,
                'seo' => [
                    'meta_title' => 'SEO Title', // Assuming structure matches form
                    'meta_description' => 'SEO Desc',
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'New Filament Category',
            'slug' => 'new-filament-category',
            'featured' => 1,
        ]);
    }

    /** @test */
    public function admin_can_edit_a_category()
    {
        $category = Category::factory()->create();

        Livewire::actingAs($this->admin, 'admin')
            ->test(CategoryResource\Pages\EditCategory::class, [
                'record' => $category->getRouteKey(),
            ])
            ->fillForm([
                'name' => 'Updated Filament Name',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Filament Name',
        ]);
    }

    /** @test */
    public function admin_can_delete_a_category()
    {
        $category = Category::factory()->create();

        Livewire::actingAs($this->admin, 'admin')
            ->test(CategoryResource\Pages\ListCategories::class)
            ->callTableAction('delete', $category);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /** @test */
    public function admin_can_bulk_delete_categories()
    {
        $categories = Category::factory()->count(3)->create();

        Livewire::actingAs($this->admin, 'admin')
            ->test(CategoryResource\Pages\ListCategories::class)
            ->callTableBulkAction('delete', $categories);

        foreach ($categories as $category) {
            $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        }
    }

    /** @test */
    public function validation_fails_if_name_is_missing()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(CategoryResource\Pages\CreateCategory::class)
            ->fillForm([
                'name' => '',
                'slug' => 'slug-ok',
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    }

    /** @test */
    public function validation_fails_if_slug_is_not_unique()
    {
        $existing = Category::factory()->create(['slug' => 'exists']);

        Livewire::actingAs($this->admin, 'admin')
            ->test(CategoryResource\Pages\CreateCategory::class)
            ->fillForm([
                'name' => 'Another One',
                'slug' => 'exists',
            ])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique']);
    }
}
