<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationCreateRequest;
use App\Http\Requests\ApplicationUpdateRequest;
use App\Http\Resources\ApplicationResource;
use App\Interfaces\ApplicationInterface;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    protected ApplicationInterface $applicationRepository;

    public function __construct(
        ApplicationInterface $applicationRepository,
    )
    {
        $this->applicationRepository = $applicationRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $apps = $this->applicationRepository->allByPagination('*', 'id', 'asc', $page, $limit);
        } else {
            $apps = $this->applicationRepository->all('*', 'id', 'asc');
        }
        return ApplicationResource::collection($apps);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ApplicationCreateRequest $request)
    {
        $app = $this->applicationRepository->create($request->only([
            'app_id',
            'app_name',
            'app_link',
        ]));
        return new ApplicationResource($app);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return new ApplicationResource($this->applicationRepository->findOneOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ApplicationUpdateRequest $request, int $id)
    {
        return $this->applicationRepository->update($request->only([
            'app_id',
            'app_name',
            'app_link',
        ]), $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->applicationRepository->delete($id);
    }
}
