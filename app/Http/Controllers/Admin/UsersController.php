<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
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

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::all()->pluck('title', 'id');

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $user = $request->all();
        $user['IsSalesperson'] = $request->input('IsSalesperson');
        User::create($user);
        //$user = User::create($request->all());

        $user->roles()->sync($request->input('roles', []));
        

        return redirect()->route('admin.users.index');
    }

    public function edit(User $user)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::all()->pluck('title','id');

        $user->load('roles');

        return view('admin.users.edit', compact('roles', 'user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        //$user->update($request->all());
        $input = User::where('id', $user->id)->first();
        $input->FullName      = $request->input('FullName');
        $input->PreferredName = $request->input('PreferredName');
        $input->email         = $request->input('email');
        if(!empty($request->input('password'))){
            $input->password  = Hash::make($request->input('password'));
        }
        $input->RepCode       = $request->input('RepCode');
        $input->PhoneNumber   = $request->input('PhoneNumber');
        $input->IsCustomer    = $request->filled('IsCustomer');
        $input->IsSalesperson = $request->filled('IsSalesperson');
        $input->save();

        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('admin.users.index');
        //echo "<pre>"; print_r($input); die;
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
