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
<<<<<<< HEAD

=======
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
        return view('admin.setting.index', compact('settings'));
    }

    public function update(Request $request)
    {
<<<<<<< HEAD
        $imageKeys = ['logo', 'favicon', 'sambutan_direktur_foto', 'pengaduan_barcode'];
=======
        $imageKeys = ['logo', 'favicon', 'sambutan_direktur_foto'];
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7

        foreach ($request->except(['_token', '_method']) as $key => $value) {
            if (in_array($key, $imageKeys)) {
                continue;
            }
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        foreach ($imageKeys as $key) {
            if ($request->hasFile($key)) {
                $old = SiteSetting::get($key);
<<<<<<< HEAD
                if ($old) {
                    Storage::disk('public')->delete($old);
                }
=======
                if ($old) Storage::disk('public')->delete($old);
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
                $path = $request->file($key)->store('rssite/images', 'public');
                SiteSetting::updateOrCreate(['key' => $key], ['value' => $path]);
            }
        }

        return redirect()->route('admin.setting.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
