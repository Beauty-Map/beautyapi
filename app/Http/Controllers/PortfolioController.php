<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Http\Requests\PortfolioCreateRequest;
use App\Http\Requests\PortfolioUpdateRequest;
use App\Http\Resources\PortfolioResource;
use App\Interfaces\PortfolioInterface;
use App\Policies\PortfolioPolicy;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Http\FormRequest;

class PortfolioController extends Controller
{
    public PortfolioInterface $portfolioRepository;

    public function __construct(
        PortfolioInterface $portfolioRepository,
    )
    {
        $this->portfolioRepository = $portfolioRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function ownIndex()
    {
        $auth = $this->getAuth();
        $services = \request()->input('services', '');
        $filter = [
            'user_id' => $auth->id,
            'services' => explode(',', $services),
        ];
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $portfolios = $this->portfolioRepository->searchByPaginate($filter, $page, $limit, 'created_at', 'desc');
        } else {
            $portfolios = $this->portfolioRepository->searchBy($filter, 'created_at', 'desc');
        }
        return PortfolioResource::collection($portfolios);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = \request()->input('services', '');
        $filter = [
            'services' => explode(',', $services),
        ];
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $portfolios = $this->portfolioRepository->searchByPaginate($filter, $page, $limit, 'created_at', 'desc');
        } else {
            $portfolios = $this->portfolioRepository->searchBy($filter, 'created_at', 'desc');
        }
        return PortfolioResource::collection($portfolios);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PortfolioCreateRequest $request)
    {
        $auth = $this->getAuth();
        if (!$auth->hasRole('artist')) {
            abort(403, Constants::ACCESS_ERROR);
        }
        $request = $this->initRequest($request);
        return new PortfolioResource($this->portfolioRepository->create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $auth = $this->getAuth();
        $portfolio = $this->portfolioRepository->findOneOrFail($id);
        if (!$auth->can('show-portfolio', $portfolio)) {
            abort(403, Constants::ACCESS_ERROR);
        }
        return new PortfolioResource($portfolio);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PortfolioUpdateRequest $request, int $id)
    {
        $auth = $this->getAuth();
        $portfolio = $this->portfolioRepository->findOneOrFail($id);
        if (!$auth->can('update-portfolio', $portfolio)) {
            abort(403, Constants::ACCESS_ERROR);
        }
        $request = $this->initRequest($request);
        return $this->portfolioRepository->update($request->all(), $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $auth = $this->getAuth();
        if (!$auth->can('delete-portfolio')) {
            abort(403, Constants::ACCESS_ERROR);
        }
        return $this->portfolioRepository->delete($id);
    }

    private function initRequest(FormRequest $request)
    {
        $auth = $this->getAuth();
        $request['user_id'] = $auth->id;
        $request['images'] = implode(',', $request['images']);
        if ($request->has('has_tel') && $request['has_tel']) {
            $request['showing_phone_number'] = $auth->getMeta('tel');
        } else if ($request->has('has_phone_number') && $request['has_phone_number']) {
            $request['showing_phone_number'] = $auth->phone_number;
        } else {
            $request['showing_phone_number'] = $auth->second_phone_number;
        }
        return $request;
    }
}
