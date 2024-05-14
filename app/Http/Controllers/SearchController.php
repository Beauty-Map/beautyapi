<?php

namespace App\Http\Controllers;

use App\Http\Resources\PortfolioResource;
use App\Interfaces\PortfolioInterface;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    protected PortfolioInterface $portfolioRepository;

    public function __construct(
        PortfolioInterface $portfolioRepository,
    )
    {
        $this->portfolioRepository = $portfolioRepository;
    }

    public function search()
    {
        $orderBy = request()->input('order_by', 'view');
        $sortBy = request()->input('sort_by', 'desc');
        $page = $this->getPage();
        $limit = $this->getLimit();
        $term = request()->input('term', '');
        $services = request()->input('services', '');
        $services = Str::length($services) > 0 ? explode(',', $services) : [];
        $filter = [
            'services' => $services,
        ];
        if ($term) {
            $filter['term'] = $term;
        }
        $result = $this->portfolioRepository->searchByPaginate($filter, $page, $limit, $orderBy, $sortBy);
        return [
            'last_page' => $result['last_page'],
            'data' => PortfolioResource::collection($result['data'])
        ];
    }
}
