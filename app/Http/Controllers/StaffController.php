<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    public function index(): View
    {
        $staffs = Admin::where('user_type', UserType::STAFF->value)->paginate(10);

        return view('backend.staff.staffs.index', compact('staffs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $roles = Role::orderBy('id', 'desc')->get();

        return view('backend.staff.staffs.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStaffRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

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

                DB::commit();
                flash(translate('Staff has been inserted successfully'))->success();

                return redirect()->route('staffs.index');
            }

            DB::rollBack();
            flash(translate('Something went wrong'))->error();

            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Staff storage failed: '.$e->getMessage(), $e->getTrace());
            flash(translate('Something went wrong'))->error();

            return back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): never
    {
        throw new NotFoundHttpException;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $staff = Admin::findOrFail($id);
        $roles = Role::orderBy('id', 'desc')->get();

        return view('backend.staff.staffs.edit', compact('staff', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStaffRequest $request, int $id): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $user = Admin::findOrFail($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->mobile;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            if ($user->save()) {
                $role = Role::findOrFail($request->role_id);
                $user->syncRoles($role->name);

                DB::commit();
                flash(translate('Staff has been updated successfully'))->success();

                return redirect()->route('staffs.index');
            }

            DB::rollBack();
            flash(translate('Something went wrong'))->error();

            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Staff update failed: '.$e->getMessage(), $e->getTrace());
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
            $user = Admin::findOrFail($id);
            $user->delete();

            flash(translate('Staff has been deleted successfully'))->success();

            return back();
        } catch (\Exception $e) {
            Log::error('Staff deletion failed: '.$e->getMessage(), $e->getTrace());
            flash(translate('Something went wrong'))->error();

            return back();
        }
    }
}
