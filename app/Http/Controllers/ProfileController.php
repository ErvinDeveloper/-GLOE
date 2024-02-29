<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateLevelRequest;
use App\Models\Level;
use App\Models\Test;
use App\Repository\UserRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{

    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function index(): View
    {
        $user = Auth::user()->load('level');

        $levels = Level::all();
        $test = Test::query()->first();
        $question = (empty($test) ? null : $test->questions()->first());

        return view('profile.index', compact('user', 'levels', 'test', 'question'));
    }

    public function levelReset(): RedirectResponse
    {
        $this->userRepository->resetLevel();
        return redirect()->back();
    }

    public function levelUpdate(UpdateLevelRequest $request): RedirectResponse
    {
        $this->userRepository->setLevel($request);
        return redirect()->back();
    }

    public function favorites(): View
    {
        $posts = $this->userRepository->favorites();
        return view('profile.favorites', compact('posts'));
    }
}
