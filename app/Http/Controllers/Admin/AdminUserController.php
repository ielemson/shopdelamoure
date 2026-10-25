<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    private const SETTINGS_PERMISSIONS = [
        'manage website settings',
        'manage delivery settings',
    ];

    private function availableRoles()
    {
        return Role::query()
            ->where('guard_name', 'web')
            ->whereNotIn('name', ['Admin', 'Customer', 'Super Admin'])
            ->whereHas('permissions')
            ->whereDoesntHave('permissions', function ($query) {
                $query->whereNotIn('name', self::SETTINGS_PERMISSIONS);
            })
            ->orderBy('name')
            ->get();
    }

    public function index()
    {
        $admins = User::role('Admin')
            ->with('roles')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admins.form', [
            'admin' => null,
            'roles' => $this->availableRoles(),
            'selectedRoles' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateAdmin($request);

        DB::transaction(function () use ($data) {
            $admin = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            $admin->assignRole('Admin');
            $admin->assignRole($data['roles']);
        });

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Administrator created successfully.');
    }

    public function edit(User $admin)
    {
        $this->ensureEditable($admin);

        return view('admin.admins.form', [
            'admin' => $admin,
            'roles' => $this->availableRoles(),
            'selectedRoles' => $admin->getRoleNames()->all(),
        ]);
    }

    public function update(Request $request, User $admin)
    {
        $this->ensureEditable($admin);

        $data = $this->validateAdmin($request, $admin);

        DB::transaction(function () use ($admin, $data) {
            $admin->name = $data['name'];
            $admin->email = $data['email'];

            if (! empty($data['password'])) {
                $admin->password = $data['password'];
            }

            if ($admin->isDirty('email')) {
                $admin->email_verified_at = null;
            }

            $admin->save();

            // Access is controlled entirely through the selected roles.
            $admin->syncPermissions([]);
            $admin->syncRoles(array_merge(['Admin'], $data['roles']));
        });

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Administrator updated successfully.');
    }

    private function validateAdmin(
        Request $request,
        ?User $admin = null
    ): array {
        $allowedRoles = $this->availableRoles()->pluck('name')->all();

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($admin?->id),
            ],
            'password' => [
                $admin ? 'nullable' : 'required',
                'string',
                'min:12',
                'confirmed',
            ],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => [
                'required',
                'string',
                'distinct',
                Rule::in($allowedRoles),
            ],
        ]);
    }

    private function ensureEditable(User $admin): void
    {
        abort_unless($admin->hasRole('Admin'), 404);

        abort_if(
            $admin->id === auth()->id()
                || $admin->can('manage admins')
                || $admin->can('manage store'),
            403,
            'This administrator cannot be changed from this page.'
        );

        $allowedRoles = $this->availableRoles()
            ->pluck('name')
            ->push('Admin')
            ->all();

        abort_if(
            $admin->roles()
                ->where(function ($query) use ($allowedRoles) {
                    $query->where('guard_name', '!=', 'web')
                        ->orWhereNotIn('name', $allowedRoles);
                })
                ->exists(),
            403,
            'This account has roles outside settings administration.'
        );
    }
}
