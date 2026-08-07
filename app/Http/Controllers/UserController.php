<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->orderBy('name')
            ->paginate(15);

        return view('pengaturan.user', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'peran' => ['required', 'string', Rule::in(['owner', 'admin', 'kasir', 'gudang', 'montir'])],
        ]);

        $validated['aktif'] = true;

        User::create($validated);

        return redirect()->route('pengaturan.user')->with('sukses', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'peran' => ['required', 'string', Rule::in(['owner', 'admin', 'kasir', 'gudang', 'montir'])],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('pengaturan.user')->with('sukses', 'User berhasil diperbarui.');
    }

    public function toggleAktif(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return back()->with('gagal', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['aktif' => ! $user->aktif]);

        $status = $user->aktif ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('pengaturan.user')->with('sukses', "User {$user->name} berhasil {$status}.");
    }
}
