<?php

namespace App\Services;

use App\Http\Requests\Admin\Setting\UpdateRequest;
use App\Models\Setting;
use App\Repository\SettingRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

readonly class SettingService
{
    public function __construct(private SettingRepository $settingRepository)
    {
    }

    public function update(UpdateRequest $request): Model|Builder|bool|int
    {
        $data = $request->validated();

        $settings = $this->settingRepository->all();
        if (empty($settings)) {
            return Setting::query()->create($data);
        } else {
            return $settings->update($data);
        }
    }
}
