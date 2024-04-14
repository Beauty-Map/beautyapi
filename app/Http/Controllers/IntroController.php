<?php

namespace App\Http\Controllers;

use App\Http\Requests\IntroCreateRequest;
use App\Http\Requests\IntroUpdateRequest;
use App\Http\Resources\IntroResource;
use App\Interfaces\IntroInterface;

class IntroController extends Controller
{
    public IntroInterface $introRepository;

    public function __construct(
        IntroInterface $introRepository,
    )
    {
        $this->introRepository = $introRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filter = [];
        if (\request()->has('is_active')) {
            $filter['is_active'] = \request()->get('is_active');
        }
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $intros = $this->introRepository->findByPaginate($filter, $page, $limit, 'id', 'desc');
        } else {
            $intros = $this->introRepository->findBy($filter, 'id', 'desc');
        }
        return IntroResource::collection($intros);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IntroCreateRequest $request)
    {
        return new IntroResource($this->introRepository->create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return new IntroResource($this->introRepository->findOneOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IntroUpdateRequest $request, int $id)
    {
        return $this->introRepository->update($request->all(), $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->introRepository->delete($id);
    }
}
