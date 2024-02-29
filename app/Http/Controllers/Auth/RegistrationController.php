<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegistrationRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function index(): View
    {
        return view('registration.index');
    }

    public function store(RegistrationRequest $request): RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('profile.index');
        }

        $data = $request->validated();
        $user = User::query()->create($data);

        if ($user) {
            Auth::login($user);
            return redirect()->route('profile.index');
        }

        return redirect()->route('login')->withErrors([
            'formError' => 'Произошла ошибка при создании пользователя'
        ]);
    }
}
