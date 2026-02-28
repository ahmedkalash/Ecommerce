<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\ProductResource;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductResourceTest extends TestCase
{
    use DatabaseTransactions;

    protected Admin $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('en');

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

    public function test_can_render_product_list_page()
    {
        Product::query()->delete();

        $products = Product::factory()->count(3)->create()->each(function (Product $product) {
            $product->categories()->attach(Category::factory()->create());
            $product->brand()->associate(Brand::factory()->create())->save();
            $product->stocks()->create([
                'variant' => 'Default',
                'price' => 100,
                'qty' => 10,
                'min_qty' => 1,
            ]);
        });

        $livewire = Livewire::test(ProductResource\Pages\ListProducts::class);

        $livewire->assertSuccessful()
            ->assertCanSeeTableRecords($products)
            ->assertTableColumnExists('thumbnail')
            ->assertTableColumnExists('name')
            ->assertTableColumnExists('categories.name')
            ->assertTableColumnExists('min_price')
            ->assertTableColumnExists('max_price')
            ->assertTableColumnExists('total_qty')
            ->assertTableColumnExists('published');
    }

    public function test_can_render_product_create_page()
    {
        Livewire::test(ProductResource\Pages\CreateProduct::class)
            ->assertSuccessful()
            ->assertFormExists()
            ->assertFormFieldExists('name')
            ->assertFormFieldExists('slug')
            ->assertFormFieldExists('categories')
            ->assertFormFieldExists('brand_id')
            ->assertFormFieldExists('stocks');
    }

    public function test_can_create_a_product_with_stock_variations()
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();

        $livewire = Livewire::test(ProductResource\Pages\CreateProduct::class);
        $defaultStockUuid = array_key_first($livewire->instance()->data['stocks'] ?? []);

        // If there's no default item for some reason, we fallback to a random string
        $uuid1 = $defaultStockUuid ?: 'variant_1';
        $uuid2 = 'variant_2';

        $livewire->fillForm([
            'name' => 'New Product',
            'slug' => 'new-product',
            'description' => '<p>Amazing new product desc</p>',
            'published' => true,
            'categories' => [$category->id], // Filament select tree / multiple select expects array of IDs
            'brand_id' => $brand->id,
            'shipping_type' => 'flat_rate',
            'stocks' => [
                $uuid1 => [
                    'variant' => 'Default',
                    'price' => 150.00,
                    'qty' => 50,
                    'min_qty' => 1,
                    'cash_on_delivery' => true,
                    'todays_deal' => false,
                ],
                $uuid2 => [
                    'variant' => 'Large-Blue',
                    'price' => 170.00,
                    'qty' => 30,
                    'min_qty' => 1,
                    'cash_on_delivery' => true,
                    'todays_deal' => true,
                ],
            ],
        ])
            ->call('create')
            ->assertHasNoFormErrors();

        $product = Product::where('slug', 'new-product')->first();
        $this->assertNotNull($product);
        $this->assertEquals('New Product', $product->name);
        $this->assertEquals($brand->id, $product->brand_id);

        $this->assertCount(2, $product->stocks);
        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'variant' => 'Default',
            'price' => 150.00,
            'qty' => 50,
        ]);

        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'variant' => 'Large-Blue',
            'price' => 170.00,
            'qty' => 30,
        ]);

        $this->assertTrue($product->categories->contains($category->id));
    }

    public function test_can_render_product_edit_page()
    {
        $product = Product::factory()->create();
        $category = Category::factory()->create();
        $product->categories()->attach($category);
        $product->stocks()->create([
            'variant' => 'Single Variation',
            'price' => 99.99,
            'qty' => 10,
            'min_qty' => 1,
        ]);

        Livewire::test(ProductResource\Pages\EditProduct::class, [
            'record' => $product->getRouteKey(),
        ])
            ->assertSuccessful()
            ->assertFormExists();
    }
}
