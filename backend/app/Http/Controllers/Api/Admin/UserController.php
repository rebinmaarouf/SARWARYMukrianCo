<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index()
    {
        $query = User::with(['roles', 'permissions']);
        
        // Hide Master User from anyone else
        if (auth()->user()->email !== 'rebin.maaruf@gmail.com') {
            $query->where('email', '!=', 'rebin.maaruf@gmail.com');
        }
        
        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'roles' => 'required|array'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        }

        return response()->json($user->load('roles', 'permissions'), 201);
    }

    public function update(Request $request, User $user)
    {
        // Prevent editing Master User by anyone else
        if ($user->email === 'rebin.maaruf@gmail.com' && auth()->user()->email !== 'rebin.maaruf@gmail.com') {
            return response()->json(['message' => 'ئەم یوزەرە پارێزراوە و ناتوانی دەستکاری بکەیت.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'array',
            'permissions' => 'array'
        ]);

        $user->update($request->only('name', 'email'));

        if ($request->has('password') && !empty($request->password)) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        }

        return response()->json($user->load('roles', 'permissions'));
    }

    public function destroy(User $user)
    {
        if ($user->email === 'rebin.maaruf@gmail.com') {
            return response()->json(['message' => 'ئەم یوزەرە ماستەرە و هەرگیز ناسڕێتەوە.'], 403);
        }

        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'ناتوانی ئەکاونتی خۆت بسڕیتەوە.'], 403);
        }
        
        $user->delete();
        return response()->json(['message' => 'بەکارهێنەر بە سەرکەوتوویی سڕایەوە.']);
    }

    public function roles()
    {
        return response()->json(Role::all());
    }

    public function permissions()
    {
        return response()->json(Permission::all());
    }
}
