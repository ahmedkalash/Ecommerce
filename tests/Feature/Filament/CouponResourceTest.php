<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\CouponResource;
use App\Models\Admin;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class CouponResourceTest extends TestCase
{
    use DatabaseTransactions;

    protected Admin $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('en');

        // Ensure we have an admin user with the correct type/permissions to view the Filament panel
        // This project uses 'admin' user_type for backend access and requires email verification.
        $user = User::factory()->create([
            'user_type' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->adminUser = Admin::find($user->id);
    }

    public function test_can_render_coupon_list_page()
    {
        $coupons = Coupon::factory()->count(3)->create();

        $this->actingAs($this->adminUser, 'admin');

        Livewire::test(CouponResource\Pages\ListCoupons::class)
            ->assertCanSeeTableRecords($coupons)
            ->assertTableColumnExists('label')
            ->assertTableColumnExists('code')
            ->assertTableColumnExists('type')
            ->assertTableColumnExists('discount');
    }

    public function test_can_render_coupon_create_page()
    {
        $this->actingAs($this->adminUser, 'admin');

        Livewire::test(CouponResource\Pages\CreateCoupon::class)
            ->assertFormExists()
            ->assertFormFieldExists('label')
            ->assertFormFieldExists('code')
            ->assertFormFieldExists('type')
            ->assertFormFieldExists('product_ids');
    }

    public function test_can_render_coupon_edit_page()
    {
        $this->actingAs($this->adminUser, 'admin');

        $coupon = Coupon::factory()->create();

        Livewire::test(CouponResource\Pages\EditCoupon::class, [
            'record' => $coupon->getRouteKey(),
        ])
            ->assertFormExists()
            ->assertFormSet([
                'label' => $coupon->label,
                'code' => $coupon->code,
            ]);
    }

    public function test_can_validate_invalid_coupon_data_on_create()
    {
        $this->actingAs($this->adminUser, 'admin');

        Livewire::test(CouponResource\Pages\CreateCoupon::class)
            ->fillForm([
                'label' => '', // required
                'code' => '', // required
                'discount' => -5, // minimum 0
                'start_date' => now()->addDays(5)->toDateString(),
                'end_date' => now()->toDateString(), // after start_date
            ])
            ->call('create')
            ->assertHasFormErrors([
                'label' => 'required',
                'code' => 'required',
                'discount' => 'min',
                'end_date' => 'after',
            ]);
    }

    public function test_can_create_coupon()
    {
        $this->actingAs($this->adminUser, 'admin');

        Livewire::test(CouponResource\Pages\CreateCoupon::class)
            ->fillForm([
                'type' => \App\Enums\Coupons\CouponTypes::CART_BASED->value,
                'label' => 'New Year Sale',
                'code' => 'NEWYEAR26',
                'discount' => 15.00,
                'discount_type' => \App\Enums\Coupons\CouponDiscountTypes::PERCENTAGE->value,
                'start_date' => now()->startOfDay()->toDateTimeString(),
                'end_date' => now()->addDays(30)->endOfDay()->toDateTimeString(),
                'usage_limit' => 50,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $coupon = \App\Models\Coupon::where('code', 'NEWYEAR26')->first();
        $this->assertNotNull($coupon);
        $this->assertEquals('New Year Sale', $coupon->label);
    }

    public function test_can_update_coupon()
    {
        $this->actingAs($this->adminUser, 'admin');

        $coupon = Coupon::factory()->create([
            'label' => 'Old Label',
        ]);

        Livewire::test(CouponResource\Pages\EditCoupon::class, [
            'record' => $coupon->getRouteKey(),
        ])
            ->fillForm([
                'label' => 'Updated Label',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $coupon->refresh();
        $this->assertEquals('Updated Label', $coupon->label);
    }

    public function test_can_delete_coupon()
    {
        $this->actingAs($this->adminUser, 'admin');

        $coupon = Coupon::factory()->create();

        Livewire::test(CouponResource\Pages\EditCoupon::class, [
            'record' => $coupon->getRouteKey(),
        ])
            ->callAction(\Filament\Actions\DeleteAction::class);

        $this->assertDatabaseMissing('coupons', [
            'id' => $coupon->id,
        ]);
    }

    public function test_can_create_product_based_coupon()
    {
        $this->actingAs($this->adminUser, 'admin');

        $products = \App\Models\Product::factory()->count(2)->create();

        Livewire::test(CouponResource\Pages\CreateCoupon::class)
            ->fillForm([
                'type' => \App\Enums\Coupons\CouponTypes::PRODUCT_BASED->value,
                'label' => 'Product Sale',
                'code' => 'PROD26',
                'discount' => 10.00,
                'discount_type' => \App\Enums\Coupons\CouponDiscountTypes::FIXED->value,
                'start_date' => now()->startOfDay()->toDateTimeString(),
                'end_date' => now()->addDays(30)->endOfDay()->toDateTimeString(),
                'usage_limit' => 10,
                'product_ids' => $products->pluck('id')->toArray(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('coupons', [
            'code' => 'PROD26',
            'type' => \App\Enums\Coupons\CouponTypes::PRODUCT_BASED->value,
        ]);

        $coupon = \App\Models\Coupon::where('code', 'PROD26')->first();
        $this->assertEquals($products->pluck('id')->toArray(), $coupon->product_ids);
    }

    public function test_can_hydrate_product_ids_on_edit()
    {
        $this->actingAs($this->adminUser, 'admin');

        $products = \App\Models\Product::factory()->count(2)->create();

        $coupon = Coupon::factory()->create([
            'type' => \App\Enums\Coupons\CouponTypes::PRODUCT_BASED->value,
            'product_ids' => $products->pluck('id')->toArray(),
            'code' => 'HYDRATE123',
        ]);

        Livewire::test(CouponResource\Pages\EditCoupon::class, [
            'record' => $coupon->getRouteKey(),
        ])
            ->assertFormExists()
            ->assertFormSet([
                'product_ids' => $products->pluck('id')->toArray(),
            ]);
    }
}
