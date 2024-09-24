<?php

namespace App\Http\Controllers;

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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Application $application)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application)
    {
        //
    }
}
