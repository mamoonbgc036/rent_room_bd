<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

class SSOController extends Controller
{
    public function redirectToGhorerMenu()
    {
      $user = Auth::user();

    $data = [
        'phone' => $user->phone,
        'name' => $user->name,
        'password' => $user->password,
        'expires' => now()->addMinutes(20)->timestamp,
    ];

    // Generate signature manually
    $url = 'https://ghorermenu.com/sso-login';
    $query = http_build_query($data);
    $signature = hash_hmac('sha256', $url . '?' . $query, config('app.key'));

    $signedUrl = $url . '?' . $query . '&signature=' . $signature;

    return redirect()->away($signedUrl);
    }
}
