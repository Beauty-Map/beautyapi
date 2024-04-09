<?php

namespace App\Http\Controllers;


use App\Constants\Constants;
use App\Http\Requests\UploadRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function adminUpload(UploadRequest $request)
    {
        if (!$request->hasFile('file')) {
            return $this->createError('file', Constants::FILE_NOT_FOUND_ERROR, 404);
        }
        $file = $request->file('file');
        $type = $request->input('type', 'avatar');
        $ext = $file->getClientOriginalExtension();
        $name = Carbon::now()->format('Y_m_d_H_i_s').Str::random(4);
        $path = "/images/$type";
        if (!File::exists(storage_path($path))) {
            File::makeDirectory($path, true, true);
        }
        Storage::disk('public')->putFileAs($path, $file, $name.'.'.$ext);
        return [
            'url' => url('/storage'.$path.'/'.$name.'.'.$ext),
            'name' => $name.'.'.$ext,
        ];
    }
}
