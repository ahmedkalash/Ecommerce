<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddPermissionRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_staff_roles'])->only('index');
        $this->middleware(['permission:add_staff_role'])->only('create');
        $this->middleware(['permission:edit_staff_role'])->only('edit');
        $this->middleware(['permission:delete_staff_role'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $roles = Role::paginate(10);

        return view('backend.staff.staff_roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('backend.staff.staff_roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $role = Role::create(['name' => $request->name]);
            $role->givePermissionTo($request->permissions);

            DB::commit();
            flash(translate('New Role has been added successfully'))->success();

            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role creation failed: '.$e->getMessage(), $e->getTrace());
            flash(translate('Something went wrong'))->error();

            return back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): never
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $role = Role::findOrFail($id);

        return view('backend.staff.staff_roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, int $id): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $role = Role::findOrFail($id);
            $role->name = $request->name;
            $role->syncPermissions($request->permissions);
            $role->save();

            DB::commit();
            flash(translate('Role has been updated successfully'))->success();

            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role update failed: '.$e->getMessage(), $e->getTrace());
            flash(translate('Something went wrong'))->error();

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();

            flash(translate('Role has been deleted successfully'))->success();

            return back();
        } catch (\Exception $e) {
            Log::error('Role deletion failed: '.$e->getMessage(), $e->getTrace());
            flash(translate('Something went wrong'))->error();

            return back();
        }
    }

    /**
     * Add a new permission to the database.
     */
    public function add_permission(AddPermissionRequest $request): RedirectResponse
    {
        try {
            Permission::create([
                'name' => $request->name,
                'group' => $request->group,
                'guard_name' => 'admin',
            ]);

            return back();
        } catch (\Exception $e) {
            Log::error('Permission creation failed: '.$e->getMessage(), $e->getTrace());
            flash(translate('Something went wrong'))->error();

            return back();
        }
    }
}
