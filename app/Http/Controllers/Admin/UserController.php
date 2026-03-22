<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return inertia('admin/user/index', [
            'users' => User::where('isAdmin', false)
                ->latest()
                ->paginate(15),
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin) {
            return redirect()->back()->with('error', 'Cannot delete admin users.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
