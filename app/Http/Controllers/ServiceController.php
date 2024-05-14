<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceCreateRequest;
use App\Http\Requests\ServiceUpdateRequest;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\ServiceShowResource;
use App\Interfaces\ServiceInterface;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public ServiceInterface $serviceRepository;

    public function __construct(
        ServiceInterface $serviceRepository,
    )
    {
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filter = [];
        if (\request()->has('service_id')) {
            $filter['parent_id'] = \request()->get('service_id');
        } else {
            $filter['parent_id'] = null;
        }
        $filter['is_active'] = true;
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $services = $this->serviceRepository->findByPaginate($filter, $page, $limit, 'id', 'asc');
        } else {
            $services = $this->serviceRepository->findBy($filter, 'id', 'asc');
        }
        return ServiceResource::collection($services);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceCreateRequest $request)
    {
        return new ServiceResource($this->serviceRepository->create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return new ServiceShowResource($this->serviceRepository->findOneOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceUpdateRequest $request, int $id)
    {
        return $this->serviceRepository->update($request->all(), $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->serviceRepository->delete($id);
    }
}
