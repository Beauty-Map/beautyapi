<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRuleRequest;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Setting::query()->firstOrCreate();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSettingsRequest $request)
    {
        $setting = Setting::query()->firstOrCreate();
        return $setting->update($request->only([
            'own',
            'first',
            'second',
            'third',
            'forth',
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateRule(UpdateRuleRequest $request)
    {
        $setting = Setting::query()->firstOrCreate();
        return $setting->update($request->only([
            'rule',
        ]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        //
    }
}
