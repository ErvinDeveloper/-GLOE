<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\UpdateRequest;
use App\Repository\SettingRepository;
use App\Services\SettingService;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    use Response;

    public function __construct(
        private readonly SettingRepository $settingRepository,
        private readonly SettingService    $settingService
    )
    {
    }

    public function index(): View
    {
        $settings = $this->settingRepository->all();
        return view('admin.setting.index', compact('settings'));
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        $this->settingService->update($request);
        return $this->responseSuccess([
            'message' => 'Settings updated'
        ]);
    }
}
