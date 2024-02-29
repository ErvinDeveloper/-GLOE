<?php

namespace App\Http\Controllers\Auth\SocialNetwork;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\SocialNetwork\GoogleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    private Provider $driver;

    public function __construct()
    {
        $this->driver = Socialite::driver('google');
    }

    public function redirectToGoogle(): RedirectResponse
    {
        return $this->driver->redirect();
    }

    public function callbackFromGoogle(): RedirectResponse
    {
        $user = Socialite::driver('google')->user();

        $checkUser = User::query()->where('email', $user->email)->first();
        if (empty($checkUser)) {
            User::query()->create([
                'name' => $user->name,
                'email' => $user->email,
                'password' => Str::password(40)
            ]);
        }

        Auth::login($checkUser);

        return redirect()->route('home');
    }

}
