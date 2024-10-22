<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseCreateRequest;
use App\Http\Requests\CourseUpdateRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $courses = Course::query()->orderByDesc('created_at')->paginate($limit);
        } else {
            $courses = Course::query()->orderByDesc('created_at')->get();
        }
        return CourseResource::collection($courses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseCreateRequest $request)
    {
        $course = Course::query()->create([
           'title' => $request->get('title'),
           'body' => $request->get('body'),
           'user_id' => $this->getAuth()->id,
        ]);
        return new CourseResource($course);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return new CourseResource($course);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CourseUpdateRequest $request, Course $course)
    {

        return $course->update([
            'title' => $request->get('title'),
            'body' => $request->get('body'),
            'user_id' => $this->getAuth()->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        return $course->delete();
    }
}
