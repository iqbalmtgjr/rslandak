<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::pluck('value', 'key');
        return view('admin.setting.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $imageKeys = ['logo', 'favicon', 'sambutan_direktur_foto', 'pengaduan_barcode'];

        foreach ($request->except(['_token', '_method']) as $key => $value) {
            if (in_array($key, $imageKeys)) {
                continue;
            }
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        foreach ($imageKeys as $key) {
            if ($request->hasFile($key)) {
                $old = SiteSetting::get($key);
                if ($old) Storage::disk('public')->delete($old);
                $path = $request->file($key)->store('rssite/images', 'public');
                SiteSetting::updateOrCreate(['key' => $key], ['value' => $path]);
            }
        }

        return redirect()->route('admin.setting.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
