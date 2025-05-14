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
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {

        // check user with fb id 


        // if exist login with the user object
        // if not create and login
        try {
            $facebookUser = Socialite::driver('facebook')->user();

            $user = User::where('fb_id', $facebookUser->getId())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $facebookUser->getName(),
                    'fb_id' => $facebookUser->getId(),
                    'password' => bcrypt(uniqid()), // not used, just to satisfy the DB column
                ]);
            }
            Auth::login($user);
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['msg' => 'Login with Facebook failed.']);
        }
    }
}
