<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{

    public function index(): View
    {
        return view('login.index');
    }

    public function auth(LoginRequest $request): RedirectResponse
    {
        if (Auth::attempt($request->validated())) {
            $request->session()->regenerate();

            if (isAdmin()) {
                return redirect()->route('dashboard');
            }

            return redirect()->route('profile.index');
        }

        return back()->withErrors([
            'email' => __('auth.login.email_or_password_is_incorrect')
        ])->onlyInput('email');
    }

}
