<?php

namespace App\Http\Controllers;

// use App\Models\Role;
use Illuminate\Http\Request;
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
    public function index()
    {
        $roles = Role::paginate(10);

        return view('backend.staff.staff_roles.index', compact('roles'));

        // $roles = Role::paginate(10);
        // return view('backend.staff.staff_roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.staff.staff_roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->permissions);
        $role = Role::create(['name' => $request->name]);
        $role->givePermissionTo($request->permissions);

        flash(translate('New Role has been added successfully'))->success();

        return redirect()->route('roles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);

        return view('backend.staff.staff_roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->syncPermissions($request->permissions);
        $role->save();

        flash(translate('Role has been updated successfully'))->success();

        return back();
        // return redirect()->route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        if (env('DEMO_MODE') == 'On') {
            flash(translate('Data can not change in demo mode.'))->info();

            return back();
        }

        Role::destroy($id);
        flash(translate('Role has been deleted successfully'))->success();

        return redirect()->route('roles.index');
    }

    public function add_permission(Request $request)
    {
        $permission = Permission::create(['name' => $request->name, 'section' => $request->parent]);

        return redirect()->route('roles.index');
    }

    public function create_admin_permissions()
    {
    }
}
