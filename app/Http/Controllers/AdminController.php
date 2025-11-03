<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function __construct()
    {
        // Middleware is already applied at route level in web.php
    }

    /**
     * Show admin dashboard.
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_groups' => Group::count(),
            'total_files' => \App\Models\File::count(),
            'admin_users' => User::where('role', 'admin')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    // User Management Methods
    public function users()
    {
        $users = User::with('group')->paginate(15);
        $groups = Group::all();
        return view('admin.users.index', compact('users', 'groups'));
    }

    public function createUser()
    {
        $groups = Group::all();
        return view('admin.users.create', compact('groups'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,usuario',
            'group_id' => 'nullable|exists:groups,id',
        ]);

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'group_id' => $request->group_id,
                'email_verified_at' => now(),
            ]);

            return redirect()->route('admin.users')->with('success', 'Usuario creado exitosamente. Puede acceder a las credenciales en la sección de Credenciales de Acceso.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,usuario',
            'group_id' => 'nullable|exists:groups,id',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'group_id' => $request->group_id,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'Usuario eliminado exitosamente.');
    }

    // Group Management Methods
    public function groups()
    {
        $groups = Group::withCount('users')->paginate(15);
        return view('admin.groups.index', compact('groups'));
    }

    public function storeGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:groups',
            'description' => 'nullable|string',
            'quota' => 'required|integer|min:0',
        ]);

        Group::create($request->all());
        return redirect()->route('admin.groups')->with('success', 'Grupo creado exitosamente.');
    }

    public function updateGroup(Request $request, Group $group)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('groups')->ignore($group->id)],
            'description' => 'nullable|string',
            'quota' => 'required|integer|min:0',
        ]);

        $group->update($request->all());
        return redirect()->route('admin.groups')->with('success', 'Grupo actualizado exitosamente.');
    }

    public function destroyGroup(Group $group)
    {
        if ($group->users()->count() > 0) {
            return redirect()->route('admin.groups')->with('error', 'No se puede eliminar un grupo que tiene usuarios asignados.');
        }

        $group->delete();
        return redirect()->route('admin.groups')->with('success', 'Grupo eliminado exitosamente.');
    }

    // Configuration Management Methods
    public function configurations()
    {
        $configurations = Configuration::all();
        return view('admin.configurations.index', compact('configurations'));
    }

    public function updateConfiguration(Request $request)
    {
        $request->validate([
            'cuota_global' => 'required|integer|min:1048576', // Min 1MB
            'extensiones_prohibidas' => 'required|string',
        ]);

        Configuration::setValue('cuota_global', (string) $request->cuota_global);
        Configuration::setValue('extensiones_prohibidas', $request->extensiones_prohibidas);

        return redirect()->route('admin.configurations')->with('success', 'Configuraciones actualizadas exitosamente.');
    }



    /**
     * Get admin statistics as JSON.
     */
    public function getStats()
    {
        $stats = [
            'total_users' => User::count(),
            'total_groups' => Group::count(),
            'total_files' => \App\Models\File::count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'regular_users' => User::where('role', 'usuario')->count(),
        ];

        return response()->json($stats);
    }
}
