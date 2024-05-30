<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlanUpdateRequest;
use App\Http\Resources\PlanResource;
use App\Interfaces\PlanInterface;

class PlanController extends Controller
{
    public PlanInterface $planRepository;

    public function __construct(
        PlanInterface $planRepository,
    )
    {
        $this->planRepository = $planRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filter = [];
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $services = $this->planRepository->findByPaginate($filter, $page, $limit, 'id', 'asc');
        } else {
            $services = $this->planRepository->findBy($filter, 'id', 'asc');
        }
        return PlanResource::collection($services);
    }

    /**
     * Display a listing of the resource.
     */
    public function indexBuyable()
    {
        $filter = [];
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $services = $this->planRepository->findByPaginate($filter, $page, $limit, 'id', 'asc');
        } else {
            $services = $this->planRepository->findBy($filter, 'id', 'asc');
        }
        return PlanResource::collection($services->where(function ($i) {return $i->id != 1;}));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return new PlanResource($this->planRepository->findOneOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlanUpdateRequest $request, int $id)
    {
        return $this->planRepository->update($request->all(), $id);
    }
}
