<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'department'])->orderBy('name')->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.form', $this->formData(new User()));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, true);

        User::create($data + ['password' => Hash::make($data['password'])]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', $this->formData($user));
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, false, $user);

        if (filled($data['password'] ?? null)) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'Anda tidak dapat menghapus akun sendiri.');

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }

    private function validated(Request $request, bool $creating, ?User $user = null): array
    {
        $rules = [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'role_id'      => ['required', 'exists:roles,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'status'       => ['required', 'in:active,inactive'],
            'password'     => $creating
                ? ['required', 'min:8']
                : ['nullable', 'min:8'],
        ];

        return $request->validate($rules, [
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
        ]);
    }

    private function formData(User $user): array
    {
        return [
            'user'        => $user,
            'roles'       => Role::orderBy('id')->get(),
            'departments' => Department::orderBy('name')->get(),
        ];
    }
}
