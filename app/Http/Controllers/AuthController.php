<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{Auth, Hash, Mail};
use App\Mail\OtpMail;

class AuthController extends Controller
{
    public function loginGet()
    {
        $title = "Login";

        return view('/auth/login', compact("title"));
    }

    public function loginPost(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email:dns',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $message = "Login success";

            myFlasherBuilder(message: $message, success: true);
            return redirect('/home');
        }

        $message = "Wrong credential";

        myFlasherBuilder(message: $message, failed: true);
        return back();
    }

    public function registrationGet()
    {
        $title = "Registration";

        return view('/auth/register', compact("title"));
    }

    public function registrationPost(Request $request)
    {
        $validatedData = $request->validate([
            'fullname' => 'required|max:255',
            'username' => 'required|max:15',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'password' => 'required|confirmed|min:4',
            'phone' => 'required',
            'gender' => 'required',
            'address' => 'required',
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['image'] = env("IMAGE_PROFILE");
        $validatedData = array_merge($validatedData, [
            "coupon" => 0,
            "point" => 0,
            'remember_token' => Str::random(30),
            'role_id' => 2 // value 2 for customer role
        ]);

        try {
            User::create($validatedData);

            // Generate OTP and send to user's email
            $otp = rand(100000, 999999);
            $request->session()->put('otp', $otp);
            $request->session()->put('otp_email', $request->email);
            Mail::to($request->email)->send(new OtpMail($otp));

            return redirect('/auth/verify-otp');
        } catch (\Illuminate\Database\QueryException $exception) {
            return abort(500);
        }
    }

    public function verifyOtpGet()
    {
        $title = "Verify OTP";

        return view('/auth/verify-otp', compact("title"));
    }

    public function verifyOtpPost(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric'
        ]);

        if ($request->otp == session('otp')) {
            $request->session()->forget('otp');
            $request->session()->forget('otp_email');
            $message = "Registration success";

            myFlasherBuilder(message: $message, success: true);
            return redirect('/auth/login');
        }

        $message = "Invalid OTP";

        myFlasherBuilder(message: $message, failed: true);
        return back();
    }

    public function logoutPost()
    {
        try {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            $message = "Session ended, you logout <strong>successfully</strong>";

            myFlasherBuilder(message: $message, success: true);

            return redirect('/auth');
        } catch (\Illuminate\Database\QueryException $exception) {
            return abort(500);
        }
    }
}
