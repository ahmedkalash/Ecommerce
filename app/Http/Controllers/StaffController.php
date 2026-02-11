<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Staff;
use App\Models\User;
use Hash;

class StaffController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_all_staffs'])->only('index');
        $this->middleware(['permission:add_staff'])->only('create');
        $this->middleware(['permission:edit_staff'])->only('edit');
        $this->middleware(['permission:delete_staff'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $staffs = Staff::paginate(10);

        return view('backend.staff.staffs.index', compact('staffs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::where('id', '!=', 1)->orderBy('id', 'desc')->get();

        return view('backend.staff.staffs.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(\App\Http\Requests\StoreStaffRequest $request)
    {
        try {
            \DB::beginTransaction();

            $user = new \App\Models\Admin; // Use Admin model so roles are assigned to App\Models\Admin
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->mobile;
            $user->user_type = 'staff';
            $user->password = Hash::make($request->password);

            if ($user->save()) {
                $staff = new Staff;
                $staff->user_id = $user->id;
                // role_id column is removed from staff table

                // Assign role via Spatie Permission
                $role = Role::findOrFail($request->role_id);
                $user->assignRole($role->name);

                if ($staff->save()) {
                    \DB::commit();
                    flash(translate('Staff has been inserted successfully'))->success();

                    return redirect()->route('staffs.index');
                }
            }

            \DB::rollback();
            flash(translate('Something went wrong'))->error();

            return back();
        } catch (\Exception $e) {
            \DB::rollback();
            flash(translate('Something went wrong: ').$e->getMessage())->error();

            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $staff = Staff::findOrFail(decrypt($id));
        $roles = $roles = Role::where('id', '!=', 1)->orderBy('id', 'desc')->get();

        return view('backend.staff.staffs.edit', compact('staff', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(\App\Http\Requests\UpdateStaffRequest $request, $id)
    {
        try {
            \DB::beginTransaction();

            $staff = Staff::findOrFail($id);
            // We need to retrieve the user as an Admin model instance to ensure role syncing works for App\Models\Admin
            $user = \App\Models\Admin::find($staff->user->id);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->mobile;

            if (strlen($request->password) > 0) {
                $user->password = Hash::make($request->password);
            }

            if ($user->save()) {
                // role_id column is removed from staff table
                // $staff->role_id = $request->role_id;

                if ($staff->save()) {
                    $role = Role::findOrFail($request->role_id);
                    $user->syncRoles($role->name);

                    \DB::commit();
                    flash(translate('Staff has been updated successfully'))->success();

                    return redirect()->route('staffs.index');
                }
            }

            \DB::rollback();
            flash(translate('Something went wrong'))->error();

            return back();
        } catch (\Exception $e) {
            \DB::rollback();
            flash(translate('Something went wrong: ').$e->getMessage())->error();

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Use Admin model to destroy to be consistent, though for deletion User::destroy works the same on the DB level
        \App\Models\Admin::destroy(Staff::findOrFail($id)->user->id);
        if (Staff::destroy($id)) {
            flash(translate('Staff has been deleted successfully'))->success();

            return redirect()->route('staffs.index');
        }

        flash(translate('Something went wrong'))->error();

        return back();
    }
}
