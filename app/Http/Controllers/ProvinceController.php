<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProvinceCreateRequest;
use App\Http\Resources\CityResource;
use App\Http\Resources\ProvinceResource;
use App\Interfaces\CityInterface;
use App\Interfaces\ProvinceInterface;
use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public ProvinceInterface $provinceRepository;
    public CityInterface $cityRepository;

    public function __construct(
        ProvinceInterface $provinceRepository,
        CityInterface $cityRepository,
    )
    {
        $this->provinceRepository = $provinceRepository;
        $this->cityRepository = $cityRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProvinceResource::collection($this->provinceRepository->all('*', 'id', 'asc'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProvinceCreateRequest $request)
    {
        $province = $this->provinceRepository->create($request->only([
            'name'
        ]));
        return new ProvinceResource($province);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return new ProvinceResource($this->provinceRepository->findOneOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        return $this->provinceRepository->update($request->only([
            'name',
        ]), $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->provinceRepository->delete($id);
    }

    /**
     * Display a listing of the resource.
     */
    public function indexCities(int $id)
    {
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $cities = $this->cityRepository->provinceCitiesByPagination($id, $page, $limit);
        } else {
            $cities = $this->cityRepository->provinceCities($id);
        }
        return CityResource::collection($cities);
    }
}
