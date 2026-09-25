<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SettingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings.view|settings.edit', only: ['index']),
            new Middleware('permission:settings.edit', only: ['update']),
        ];
    }

    public function index()
    {
        // Fetch all settings and key them by their 'key' field for easy access in the view
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        $oldRate = null;
        if (isset($data['points_conversion_rate'])) {
            $oldRateSetting = Setting::where('key', 'points_conversion_rate')->first();
            $oldRate = $oldRateSetting ? (float)$oldRateSetting->value : 1.0;
        }

        // Loop through the submitted data and update or create settings
        foreach ($data as $key => $value) {
            // Determine group based on prefix or known keys
            $group = 'general';
            if (str_starts_with($key, 'points_') || str_starts_with($key, 'reward_')) {
                $group = 'points';
            } elseif (str_starts_with($key, 'social_')) {
                $group = 'social';
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group]
            );
        }

        // Check if points conversion rate changed
        if (isset($data['points_conversion_rate'])) {
            $newRate = (float)$data['points_conversion_rate'];
            if ($oldRate > 0 && $newRate > 0 && $oldRate !== $newRate) {
                // Recalculate points for all rewards based on their base fiat value
                $rewards = \App\Models\Reward::all();
                foreach ($rewards as $reward) {
                    $baseFiatValue = $reward->points_required * $oldRate;
                    $reward->points_required = (int) round($baseFiatValue / $newRate);
                    $reward->save();
                }
            }
        }

        return redirect()->route('admin.settings.index')->with('success', 'System settings updated successfully.');
    }
}
