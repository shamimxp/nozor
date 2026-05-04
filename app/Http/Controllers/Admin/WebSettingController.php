<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebSetting;
use Illuminate\Http\Request;

class WebSettingController extends Controller
{
    /**
     * Show web settings edit form.
     */
    public function index()
    {
        $setting = WebSetting::first();
        if (!$setting) {
            $setting = WebSetting::create(['currency_symbol' => '৳']);
        }
        return view('admin.web-setting.index', compact('setting'));
    }

    /**
     * Update the web settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'slogan'           => 'nullable|string|max:500',
            'contact_number_1' => 'nullable|string|max:50',
            'contact_number_2' => 'nullable|string|max:50',
            'address'          => 'nullable|string|max:1000',
            'email'            => 'nullable|email|max:255',
            'facebook'         => 'nullable|url|max:500',
            'twitter'          => 'nullable|url|max:500',
            'instagram'        => 'nullable|url|max:500',
            'youtube'          => 'nullable|url|max:500',
            'linkedin'         => 'nullable|url|max:500',
            'whatsapp'         => 'nullable|string|max:500',
            'tiktok'           => 'nullable|url|max:500',
            'pinterest'        => 'nullable|url|max:500',
            'office_hour'      => 'nullable|string|max:255',
            'currency_symbol'  => 'nullable|string|max:10',
        ]);

        try {
            $setting = WebSetting::first();
            if (!$setting) {
                $setting = new WebSetting();
            }

            $setting->fill($request->only([
                'slogan', 'contact_number_1', 'contact_number_2', 'address', 'email',
                'facebook', 'twitter', 'instagram', 'youtube', 'linkedin',
                'whatsapp', 'tiktok', 'pinterest', 'office_hour', 'currency_symbol',
            ]));

            $setting->save();

            return response()->json(['success' => 'Web settings updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong. ' . $e->getMessage()], 500);
        }
    }
}
