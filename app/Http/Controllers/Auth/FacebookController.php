<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends Controller
{
    public function redirectToFacebook()
    {
        if (str_contains(url()->previous(), 'package')) {
            session()->put('previous_url', url()->previous());
        }
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();

            $user = User::where('fb_id', $facebookUser->getId())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $facebookUser->getName(),
                    'fb_id' => $facebookUser->getId(),
                    'password' => bcrypt(uniqid()), // not used, just to satisfy the DB column
                ]);
            }
            Auth::login($user);
            if (session()->has('previous_url')) {
                $redirectUrl = session('previous_url');
                session()->forget('previous_url');
                return redirect($redirectUrl);
            }
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            \Log::error('Facebook login error: ' . $e->getMessage());
            return redirect('/login')->withErrors(['msg' => 'Login with Facebook failed.']);
        }
    }
}
