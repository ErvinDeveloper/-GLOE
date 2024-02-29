<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ForgotPasswordRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Services\ForgotService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function create(): View
    {
        return view('forgot.create');
    }

    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink($request->validated());

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()->route('forgot.create')->withSuccess(__('auth.forgot.link_has_been_sent'));
        }

        return redirect()->route('forgot.create')->withError(__('auth.forgot.user_with_this_email_is_not_found'));
    }

    public function resetPassword(Request $request): View
    {
        return view('forgot.reset_password', compact('request'));
    }

    public function updatePassword(
        UpdatePasswordRequest $request,
        ForgotService         $forgotService
    ): RedirectResponse
    {
        $status = $forgotService->updatePassword($request);

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->withSuccess(__('auth.forgot.password_updated'));
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => trans($status)
        ]);
    }
}
