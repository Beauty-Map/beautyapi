<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArtistResource;
use App\Http\Resources\PortfolioResource;
use App\Interfaces\PortfolioInterface;
use App\Interfaces\UserInterface;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    protected PortfolioInterface $portfolioRepository;
    protected UserInterface $userRepository;

    public function __construct(
        PortfolioInterface $portfolioRepository,
        UserInterface $userRepository,
    )
    {
        $this->portfolioRepository = $portfolioRepository;
        $this->userRepository = $userRepository;
    }

    public function search()
    {
        $orderBy = request()->input('order_by', 'view');
        $sortBy = request()->input('sort_by', 'desc');
        $page = $this->getPage();
        $limit = $this->getLimit();
        $term = request()->input('term', '');
        $services = request()->input('service', '');
        $userID = request()->input('user_id', null);
        $services = Str::length($services) > 0 ? explode(',', $services) : [];
        $filter = [
            'services' => $services,
        ];
        $provinceID = \request()->input('province_id', null);
        $cityID = \request()->input('city_id', null);
        if ($provinceID) {
            $filter['province_id'] = $provinceID;
        }
        if ($cityID) {
            $filter['city_id'] = $cityID;
        }
        if ($term) {
            $filter['term'] = $term;
        }
        if ($term) {
            $filter['term'] = $term;
        }
        if ($userID) {
            $filter['user_id'] = $userID;
        }
        $result = $this->portfolioRepository->searchByPaginate($filter, $page, $limit, $orderBy, $sortBy);
        return [
            'last_page' => $result['last_page'],
            'data' => PortfolioResource::collection($result['data'])
        ];
    }

    public function searchArtists()
    {
        $orderBy = request()->input('order_by', 'created_at');
        $sortBy = request()->input('sort_by', 'desc');
        $page = $this->getPage();
        $limit = $this->getLimit();
        $term = request()->input('term', '');
        $provinceID = \request()->input('province_id', null);
        $cityID = \request()->input('city_id', null);
        $filter = [];
        if ($provinceID) {
            $filter['province_id'] = $provinceID;
        }
        if ($cityID) {
            $filter['city_id'] = $cityID;
        }
        if ($term) {
            $filter['term'] = $term;
        }
        $result = $this->userRepository->searchByPaginate($filter, $page, $limit, $orderBy, $sortBy);
        return [
            'last_page' => $result['last_page'],
            'data' => ArtistResource::collection($result['data'])
        ];
    }
}
