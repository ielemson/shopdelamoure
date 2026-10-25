<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    private const PERMISSIONS = [
        'manage website settings',
        'manage delivery settings',
        'manage store',
        'manage admins',
    ];

    private const RESERVED_ROLES = [
        'admin',
        'customer',
        'super admin',
    ];

    public function index()
    {
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->with('permissions')
            ->orderBy('name')
            ->get();

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', self::PERMISSIONS)
            ->orderBy('name')
            ->get();

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRole($request);

        DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($data['permissions'] ?? []);
        });

        return back()->with('success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role)
    {
        $this->ensureEditable($role);

        $data = $this->validateRole($request, $role);

        DB::transaction(function () use ($role, $data) {
            $role->update([
                'name' => $data['name'],
            ]);

            $role->syncPermissions($data['permissions'] ?? []);
        });

        return back()->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $this->ensureEditable($role);

        if ($role->users()->exists()) {
            return back()->with(
                'error',
                'Reassign users before deleting this role.'
            );
        }

        $role->delete();

        return back()->with('success', 'Role deleted successfully.');
    }

    private function validateRole(Request $request, ?Role $role = null): array
    {
        $request->merge([
            'name' => trim((string) $request->input('name')),
        ]);

        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')
                    ->where('guard_name', 'web')
                    ->ignore($role?->id),
                function ($attribute, $value, $fail) {
                    if (in_array(
                        mb_strtolower($value),
                        self::RESERVED_ROLES,
                        true
                    )) {
                        $fail('This role name is reserved.');
                    }
                },
            ],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => [
                'required',
                'string',
                'distinct',
                Rule::in(self::PERMISSIONS),
                Rule::exists('permissions', 'name')
                    ->where('guard_name', 'web'),
            ],
        ]);
    }

    private function ensureEditable(Role $role): void
    {
        abort_unless($role->guard_name === 'web', 404);

        abort_if(
            in_array(
                mb_strtolower($role->name),
                self::RESERVED_ROLES,
                true
            ),
            403,
            'This system role cannot be changed here.'
        );
    }
}
