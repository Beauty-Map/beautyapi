<?php

\Illuminate\Support\Facades\Route::get('/', function () {
    $sub = \App\Models\Subscription::query()->get()->map(function ($item) {
        return $item->date;
    });
    dd($sub->toArray());
});
