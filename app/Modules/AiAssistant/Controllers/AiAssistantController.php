<?php

namespace App\Modules\AiAssistant\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AiAssistant\Models\AiSetting;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    public function chatView()
    {
        return view('aiassistant::chat');
    }

    public function settingsView()
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->can('sistem.sekolah.edit'), 403);
        $setting = AiSetting::first() ?? new AiSetting();
        return view('aiassistant::settings', compact('setting'));
    }

    public function settingsUpdate(Request $request)
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->can('sistem.sekolah.edit'), 403);
        $request->validate([
            'name' => 'required|string|max:255',
            'style' => 'nullable|string',
            'base_prompt' => 'nullable|string',
        ]);

        $setting = AiSetting::first();
        if (!$setting) {
            $setting = new AiSetting();
        }

        $setting->name = $request->input('name');
        $setting->style = $request->input('style');
        $setting->base_prompt = $request->input('base_prompt');
        $setting->save();

        return redirect()->route('ai-assistant.settings')->with('success', 'Pengaturan Persona AI berhasil disimpan.');
    }
}
