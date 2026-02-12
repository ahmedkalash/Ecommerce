<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\Admin;
use Spatie\Permission\Models\Role;
use Hash;
use Illuminate\Support\Facades\Log;

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
     */
    public function index()
    {
        $staffs = Admin::where('user_type', UserType::STAFF->value)->paginate(10);

        return view('backend.staff.staffs.index', compact('staffs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::orderBy('id', 'desc')->get();

        return view('backend.staff.staffs.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStaffRequest $request)
    {
        try {
            \DB::beginTransaction();

            $user = new Admin;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->mobile;
            $user->user_type = UserType::STAFF->value;
            $user->password = Hash::make($request->password);

            if ($user->save()) {
                // Assign role via Spatie Permission
                $role = Role::findOrFail($request->role_id);
                $user->assignRole($role->name);

                \DB::commit();
                flash(translate('Staff has been inserted successfully'))->success();

                return redirect()->route('staffs.index');
            }

            \DB::rollback();
            flash(translate('Something went wrong'))->error();

            return back();
        } catch (\Exception $e) {
            \DB::rollback();
            Log::error('Staff storage failed: '.$e->getMessage(), $e->getTrace());
            flash(translate('Something went wrong'))->error();

            return back();
        }
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
     * @param  string  $id  Encrypted String
     */
    public function edit($id)
    {
        $staff = Admin::findOrFail(decrypt($id));
        $roles = Role::orderBy('id', 'desc')->get();

        return view('backend.staff.staffs.edit', compact('staff', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     */
    public function update(UpdateStaffRequest $request, $id)
    {
        try {
            \DB::beginTransaction();

            $user = Admin::findOrFail($id);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->mobile;

            if (strlen($request->password) > 0) {
                $user->password = Hash::make($request->password);
            }

            if ($user->save()) {
                $role = Role::findOrFail($request->role_id);
                $user->syncRoles($role->name);

                \DB::commit();
                flash(translate('Staff has been updated successfully'))->success();

                return redirect()->route('staffs.index');
            }

            \DB::rollback();
            flash(translate('Something went wrong'))->error();

            return back();
        } catch (\Exception $e) {
            \DB::rollback();
            Log::error('Staff update failed: '.$e->getMessage());
            flash(translate('Something went wrong'))->error();

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        if (Admin::destroy($id)) {
            flash(translate('Staff has been deleted successfully'))->success();

            return redirect()->route('staffs.index');
        }

        flash(translate('Something went wrong'))->error();

        return back();
    }
}
