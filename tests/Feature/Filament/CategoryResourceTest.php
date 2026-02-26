<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\CategoryResource;
use App\Models\Admin;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CategoryResourceTest extends TestCase
{
    use DatabaseTransactions;

    protected Admin $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create user and Admin model
        $user = User::factory()->create([
            'user_type' => 'admin',
            'email_verified_at' => now(),
        ]);
        $this->adminUser = Admin::find($user->id);

        // 2. Seed permissions
        $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);

        // 3. Assign super_admin role for unrestricted Filament access
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'admin']);
        $this->adminUser->assignRole($superAdminRole);

        // 4. Authenticate
        $this->actingAs($this->adminUser, 'admin');
    }

    public function test_can_render_category_list_page()
    {
        $categories = Category::factory()->count(3)->create();

        Livewire::test(CategoryResource\Pages\ListCategories::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($categories)
            ->assertTableColumnExists('name')
            ->assertTableColumnExists('parentCategory.name');
    }

    public function test_can_render_category_create_page()
    {
        Livewire::test(CategoryResource\Pages\CreateCategory::class)
            ->assertSuccessful()
            ->assertFormExists()
            ->assertFormFieldExists('name')
            ->assertFormFieldExists('slug')
            ->assertFormFieldExists('parent_id');
    }

    public function test_can_create_a_category()
    {
        Livewire::test(CategoryResource\Pages\CreateCategory::class)
            ->fillForm([
                'name' => 'Electronics',
                'slug' => 'electronics',
                'position' => 0,
                'featured' => 1,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics',
            'slug' => 'electronics',
            'featured' => 1,
        ]);
    }

    public function test_category_slug_must_be_unique_on_create()
    {
        Category::factory()->create([
            'slug' => 'existing-slug',
        ]);

        Livewire::test(CategoryResource\Pages\CreateCategory::class)
            ->fillForm([
                'name' => 'Another Category',
                'slug' => 'existing-slug', // duplicate
            ])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique']);
    }

    public function test_can_edit_a_category()
    {
        $category = Category::factory()->create([
            'name' => 'Old Category Name',
            'slug' => 'old-slug',
        ]);

        Livewire::test(CategoryResource\Pages\EditCategory::class, [
            'record' => $category->getRouteKey(),
        ])
            ->assertSuccessful()
            ->fillForm([
                'name' => 'Updated Category Name',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category Name',
            'slug' => 'old-slug', // Slug usually unaffected, but can be updated.
        ]);
    }

    public function test_can_delete_a_category()
    {
        $category = Category::factory()->create();

        Livewire::test(CategoryResource\Pages\EditCategory::class, [
            'record' => $category->getRouteKey(),
        ])
            ->callAction(\Filament\Actions\DeleteAction::class);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_parent_category_select_works_and_can_be_assigned()
    {
        $parentCategory = Category::factory()->create(['name' => 'Parent Cat']);

        Livewire::test(CategoryResource\Pages\CreateCategory::class)
            ->fillForm([
                'name' => 'Child Cat',
                'slug' => 'child-cat',
                'parent_id' => $parentCategory->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'Child Cat',
            'parent_id' => $parentCategory->id,
        ]);
    }
}
