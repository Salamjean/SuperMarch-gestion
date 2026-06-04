<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $settings = Setting::firstOrCreate([], [
            'store_name' => 'SUPERMARCHÉ PRO',
            'phone' => '+225 07 00 00 00 00',
            'address' => 'Abidjan, Cocody Riviera Palmeraie',
            'email' => 'contact@supermarchepro.com',
            'invoice_footer' => 'Merci pour votre confiance ! Les marchandises vendues ne sont ni reprises ni échangées, sauf accord de la direction.',
        ]);

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'invoice_footer' => ['nullable', 'string'],
            'invoice_format' => ['required', 'string', 'in:ticket,a4'],
        ]);

        $settings = Setting::first();
        if (!$settings) {
            $settings = new Setting();
        }
        $settings->fill($validated);
        $settings->save();

        return redirect()->route('admin.settings.edit')
            ->with('success', 'Les paramètres de la boutique ont été mis à jour avec succès.');
    }
}
