<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordEmail;
use App\Models\Order;
use App\Models\OrderItem;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login() {
        return view('front.account.login');
    }

    public function authenticate(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->passes()) {
            $user = User::where('email', $request->email)->first();

            if ($user && $user->status == 0) {
                return redirect()->route('account.login')
                    ->withInput($request->only('email'))
                    ->with('error', 'Your account has been blocked');
            } else {
                if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {

                    if (session()->has('url.intended')) {
                        return redirect(session()->get('url.intended'));
                    }

                    return redirect()->route('account.profile');
                } else {
//                session()->flash('error', 'Either email/password is incorrect.');
                    return redirect()->route('account.login')
                        ->withInput($request->only('email'))
                        ->with('error', 'Either email/password is incorrect');
                }
            }



        } else {
            return redirect()->route('account.login')
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }
    }

    public function register() {
        return view('front.account.register');
    }

    public function processRegister(Request $request) {

        $validator = Validator::make($request->all(), [
           'name' => 'required|min:3',
           'email' => 'required|email|unique:users',
           'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->passes()) {

            $user = new User;

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash('success', 'You have been registered successfully');

            return response()->json([
                'status' => true,
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function profile() {
        $user = Auth::user();
        return view('front.account.profile', compact('user'));
        // return view('front.account.profile');
    }
    // public function updateProfile(Request $request)
    // {
    // $user = Auth::user();

    // // Validate inputs
    // $request->validate([
    //     'name' => 'required|string|min:3',
    //     'email' => 'required|email',
    //     'phone' => 'nullable|string',
    //     'address' => 'nullable|string',
    // ]);

    // // Update user info
    // $user->name = $request->name;
    // $user->email = $request->email;
    // $user->phone = $request->phone;
    // $user->address = $request->address;

    // // Save the updated user
    // $user->save();

    // return redirect()->route('account.profile')->with('success', 'Profile updated successfully');
    // }


    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('account.login')->with('success', 'You have been logged out');
    }

    public function showChangePasswordForm(){
        return view('front.account.change-password');
    }
    public function changePassword(Request $request) {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        $user = User::find(Auth::id());

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Old password is incorrect']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();
        return redirect()->route('account.profile')->with('success', 'Password changed successfully');
    }

    //google login
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    public function handleGoogleCallback()
    {
    $googleUser = Socialite::driver('google')->stateless()->user();
    $user = User::where('email', $googleUser->getEmail())->first();

    if (!$user) {
        $user = User::create([
            'email' => $googleUser->getEmail(),
            'name' => $googleUser->getName(),
            'password' => Hash::make(Str::random(16)),
            'role' => 1,
        ]);
    }
    Auth::login($user);

    return redirect()->route('account.profile');
}

//facebook login
public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
{
    $facebookUser = Socialite::driver('facebook')->stateless()->user();
    $user = User::where('email', $facebookUser->getEmail())->first();

    if (!$user) {
        $user = User::create([
            'email' => $facebookUser->getEmail(),
            'name' => $facebookUser->getName(),
            'password' => Hash::make(Str::random(16)),
            'role' => 1,
        ]);
    }

    Auth::login($user);

    return redirect()->route('account.profile');
}

    public function myorders() {
        $data = [];
        $user = Auth::user();

        $orders =  Order::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        $data['orders'] = $orders;

        return view('front.account.order', $data);
    }

    public function orderDetail($id) {
        $data = [];
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)->where('id', $id)->first();
        $data['order'] = $order;

        $items = OrderItem::where('order_id', $order->id)->get();
        $data['orderItems'] = $items;

        $orderItemsCount = OrderItem::where('order_id', $order->id)->count();
        $data['orderItemsCount'] = $orderItemsCount;

        return view('front.account.order-detail', $data);
    }

    public function forgotPassword() {
        return view('front.account.forgot-password');
    }

    public function processForgotPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);
        if($validator->fails()){
            return redirect()->route('front.forgotPassword')->withInput()->withErrors($validator);
        }
        $token = Str::random(60);
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now(),
        ]);
        $user = User::where('email', $request->email)->first();
        $formData = [
            'token' => $token,
            'user' => $user,
            'mailSubject' => 'You have requested to reset your password'
        ];

        Mail::to($request->email)->send(new ResetPasswordEmail($formData));
        return redirect()->route('front.forgotPassword')->with('success', 'We have sent you a password reset link to your email address.');
    }

    public function resetPassword($token) {
        $tokenExists = DB::table('password_reset_tokens')->where('token', $token)->first();
        if ($tokenExists == null) {
            return redirect()->route('front.forgotPassword')->with('error', 'Invalid or expired token.');
        }

        return view('front.account.reset-password', [
            'token' => $token,
        ]);
    }

    public function processResetPassword(Request $request) {
        $token = $request->token;
        $tokenObj = DB::table('password_reset_tokens')->where('token', $token)->first();
        if ($tokenObj == null) {
            return redirect()->route('front.forgotPassword')->with('error', 'Invalid request');
        }
        $user = User::where('email', $tokenObj->email)->first();
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        if($validator->fails()){
            return redirect()->route('front.resetPassword', $token)->withErrors($validator);
        }

        User::where('id', $user->id)->update([
            'password' => Hash::make($request->new_password),
        ]);
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        return redirect()->route('account.login')->with('success', 'Password has been reset successfully. You can now login with your new password.');
    }
}
