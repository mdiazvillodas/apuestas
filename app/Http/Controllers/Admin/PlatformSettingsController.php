<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlatformSettingsController extends Controller
{
    private const AUTO_REGISTER_COINS_KEY = 'auto_register_coins_enabled';

    public function edit(): View
    {
        return view('admin.settings.edit', [
            'autoRegisterCoinsEnabled' => AppSetting::getBool(self::AUTO_REGISTER_COINS_KEY),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        AppSetting::putBool(
            self::AUTO_REGISTER_COINS_KEY,
            $request->boolean('auto_register_coins_enabled')
        );

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Configuración actualizada.');
    }
}