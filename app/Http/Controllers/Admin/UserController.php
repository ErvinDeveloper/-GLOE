<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreRequest;
use App\Http\Requests\Admin\User\UpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()->orderBy('id', 'desc')->paginate(config('main.per_page'));
        return view('admin.user.index', compact('users'));
    }

    public function edit(User $user): View
    {
        return view('admin.user.edit', compact('user'));
    }

    public function update(User $user, UpdateRequest  $request): RedirectResponse
    {
        $data = $request->validated();
        if (is_null($data['password'])) {
            unset($data['password']);
        }
        $user->update($data);
        return back()->withSuccess('Информация о пользователе обновлена');
    }

    public function create(): View
    {
        return view('admin.user.create');
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        User::query()->create($data);
        return redirect()->route('admin.user.index')->withSuccess('Пользователь добавлен');
    }

    public function delete(User $user)
    {
        $user->delete();
        return redirect()->route('admin.user.index')->withWarning('Пользователь удален');
    }
}
