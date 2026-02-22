<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class Role_userController extends Controller
{
    // Menampilkan daftar user
    public function index()
    {
        $users = User::all();
        return view('role_users.index', compact('users'));
    }

    // Menampilkan form untuk assign role ke user
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all(); // Ambil semua role untuk ditampilkan di form
        return view('role_users.edit', compact('user', 'roles'));
    }

    // Menyimpan role yang di-assign ke user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validasi input
        $request->validate([
            'roles' => 'required|array',
        ]);

        // Sync roles (cabut semua role lama dan tambahkan role baru)
        $user->syncRoles($request->roles);

        return redirect()->route('role_users.index')->with('success', 'Roles updated successfully.');
    }
}
