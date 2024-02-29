<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repository\LevelRepository;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(LevelRepository $levelRepository): View
    {
        return view('admin.dashboard', $levelRepository->dashboardGetViewStatistics());
    }
}
