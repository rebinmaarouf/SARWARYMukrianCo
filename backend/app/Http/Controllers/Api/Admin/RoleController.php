<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('guard_name', 'api')->get();
        
        return response()->json($roles->map(function($role) {
            $perms = DB::table('role_has_permissions')
                ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                ->where('role_has_permissions.role_id', $role->id)
                ->pluck('name')
                ->toArray();

            return [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $perms
            ];
        }));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'api']);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json($this->formatRole($role), 201);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        $role->update(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json($this->formatRole($role));
    }

    protected function formatRole($role)
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->toArray()
        ];
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Super Admin') {
            return response()->json(['message' => 'ناتوانیت ڕۆڵی سەرەکی بسڕیتەوە.'], 403);
        }

        try {
            // سڕینەوەی هەموو پەیوەندییەکان پێش سڕینەوەی خودی ڕۆڵەکە بۆ ڕێگری لە هەڵەی ٥٠٠
            DB::table('role_has_permissions')->where('role_id', $role->id)->delete();
            DB::table('model_has_roles')->where('role_id', $role->id)->delete();
            
            $role->delete();
            return response()->json(['message' => 'ڕۆڵەکە بە سەرکەوتوویی سڕایەوە.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'هەڵەیەک ڕوویدا: ' . $e->getMessage()], 500);
        }
    }

    public function getAllPermissions()
    {
        return response()->json(Permission::where('guard_name', 'api')->get());
    }
}
