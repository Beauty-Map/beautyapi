<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SliderUpdateRequest;
use App\Http\Resources\MainSliderResource;
use App\Models\MainSlider;
use Illuminate\Http\Request;

class MainSliderController extends Controller
{
    public function index()
    {
        return new MainSliderResource(MainSlider::query()->first());
    }

    public function update(SliderUpdateRequest $request, MainSlider $slider)
    {
        return $slider->update($request->only([
            'image',
            'main_title',
            'sub_title',
            'description',
            'link_url',
            'link_title',
        ]));
    }
}
