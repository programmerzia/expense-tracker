<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::where('role', '!=', 'super_admin')->get();
        $onlineUsers = User::where('last_seen', '>=', Carbon::now()->subMinutes(5))
            ->where('role', '!=', 'super_admin')
            ->get();
        
        return view('admin.dashboard', compact('users', 'onlineUsers'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'active',
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'User created successfully');
    }

    public function toggleUserStatus(User $user)
    {
        if ($user->role === 'super_admin') {
            return back()->with('error', 'Cannot modify super admin status');
        }

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        $statusMessage = $user->status === 'active' ? 'activated' : 'deactivated';
        return back()->with('success', "User has been {$statusMessage} successfully");
    }
}
