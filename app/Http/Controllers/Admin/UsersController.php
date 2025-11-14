<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\Customer;
use Gate;
use Hash;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::all();
        $roles = Role::all()->pluck('title', 'id');
        $customers = Customer::all()->pluck('CustomerName', 'id');

        return view('admin.users.index', compact('users', 'roles', 'customers'));
    }

    public function create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::all()->pluck('title', 'id');

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $authUser = $request->user();
        $currentCompany = $authUser->currentCompany();

        $userdata = $request->all();
        $userdata['IsSalesperson'] = $request->input('IsSalesperson');

        // Check if user is a sales person
        if(isset($request->IsSalesperson)) {
            $salesperson = "1";
        } else {
            $salesperson = "0";
        }
        $userdata['IsSalesperson'] = $salesperson;

        // Check if user is a customer
        if(isset($request->IsCustomer)) {
            $customer = "1";
        } else {
            $customer = "0";
        }
        $userdata['IsCustomer'] = $customer;

        //echo "<pre>"; print_r($userdata); die;
        $user = User::create($userdata);

        $user->roles()->sync($request->input('roles', []));

        $user->attachCompany($currentCompany);

        return redirect()->route('admin.users.index')->with('flash_message', 'User successfully added');
    }

    public function edit($id)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = User::with('roles')->findOrFail($id);

        return response()->json([
            'id' => $user->id,
            'PreferredName' => $user->PreferredName,
            'email' => $user->email,
            'PhoneNumber' => $user->PhoneNumber,
            'roles' => $user->roles->map(function($role) {
                return ['id' => $role->id, 'title' => $role->title];
            }),
            'IsSalesperson' => $user->IsSalesperson,
            'RepCode' => $user->RepCode,
            'IsCustomer' => $user->IsCustomer,
            'customer_id' => $user->customer_id,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'PreferredName' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'PhoneNumber' => 'nullable|string|max:20',
            'password' => 'nullable|min:8',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'IsSalesperson' => 'nullable|boolean',
            'RepCode' => 'nullable|string|max:50|unique:users,RepCode,' . $id,
            'IsCustomer' => 'nullable|boolean',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        // Update user fields
        $user->PreferredName = $validated['PreferredName'];
        $user->FullName = $validated['PreferredName'];
        $user->email = $validated['email'];
        $user->PhoneNumber = $validated['PhoneNumber'] ?? null;

        // Only update password if provided
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        // Update salesperson fields
        $user->IsSalesperson = $request->has('IsSalesperson') ? 1 : 0;
        $user->RepCode = $user->IsSalesperson ? $validated['RepCode'] : null;

        // Update customer fields
        $user->IsCustomer = $request->has('IsCustomer') ? 1 : 0;
        $user->customer_id = $user->IsCustomer ? $validated['customer_id'] : null;

        $user->save();

        // Sync roles
        $user->roles()->sync($validated['roles']);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }


    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->load('roles');

        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->delete();

        return back();
    }

    public function massDestroy(MassDestroyUserRequest $request)
    {
        User::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
