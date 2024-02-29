<?php

namespace App\Services;

use App\Http\Requests\User\UpdatePasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotService
{
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $data = $request->validated();

        return Password::reset(
            $data,
            function ($user) use ($data) {
                $user->forceFill([
                    'password' => Hash::make($data['password']),
                    'remember_token' => Str::random(60)
                ])->save();
            }
        );
    }
}
