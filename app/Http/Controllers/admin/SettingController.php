<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class SettingController extends Controller
{
    public function showChangePasswordForm()
    {
        return view('admin.change-password');
    }
    
    public function changePassword(Request $request) {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);
    
        $admin = User::find(Auth::guard('admin')->id());
    
        if (!Hash::check($request->old_password, $admin->password)) {
            return back()->withErrors(['old_password' => 'Old password is incorrect']);
        }
    
        $admin->password = Hash::make($request->new_password);
        $admin->save();
        return redirect()->route('admin.dashboard')->with('success', 'Password changed successfully');
    }
}
