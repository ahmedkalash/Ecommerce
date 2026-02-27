<?php

namespace Tests\Unit\Models;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RoleTranslatableTest extends TestCase
{
    use DatabaseTransactions;

    public function test_role_name_is_fixed_and_label_is_translatable()
    {
        $role = Role::create([
            'name' => 'super_admin', // Used by Spatie Permission internally
            'guard_name' => 'admin',
            'label' => ['en' => 'Super Administrator', 'ar' => 'مدير النظام العام'],
        ]);

        // `name` remains string for guards
        $this->assertEquals('super_admin', $role->name);

        // `display_name` accessor respects locale via `label` JSON
        app()->setLocale('en');
        $this->assertEquals('Super Administrator', $role->display_name);

        app()->setLocale('ar');
        $this->assertEquals('مدير النظام العام', $role->display_name);
    }

    public function test_permission_display_name_falls_back_to_name()
    {
        $permission = Permission::create([
            'name' => 'edit_products',
            'guard_name' => 'admin',
            // no label JSON provided
        ]);

        // Should return the raw name 'edit_products' because label is empty
        $this->assertEquals('edit_products', $permission->display_name);
    }
}
