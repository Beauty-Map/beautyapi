<?php


namespace App\Repositories;

use App\Interfaces\PortfolioInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PortfolioRepository
 *
 * @package \App\Repositories
 */
class PortfolioRepository extends BaseRepository implements PortfolioInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }

    public function searchByPaginate(array $filter, int $page, int $limit, string $orderBy = 'created_at', string $sortBy = 'desc')
    {
        $query = $this->searchQuery($filter, $orderBy, $sortBy);
        return ['last_page' => ceil($query->count() / $limit), 'data' => $query->offset(($page - 1) * $limit)->limit($limit)->get()];
    }

    public function searchBy(array $filter, string $orderBy = 'created_at', string $sortBy = 'desc')
    {
        $query = $this->searchQuery($filter, $orderBy, $sortBy);
        return ['last_page' => 1, 'data' => $query->get()];
    }

    public function searchQuery(array $filter, string $orderBy = 'created_at', string $sortBy = 'desc'): Builder
    {
        $query = $this->model->newQuery();
        $query->join('users', 'users.id', '=', 'portfolios.user_id');
        $query->join('cities', 'cities.id', '=', 'users.city_id');
        if (!empty($filter['user_id'])) {
            $query->where('portfolios.user_id', $filter['user_id']);
        }
        if (!empty($filter['services']) && is_array($filter['services'])) {
            $query->whereIn('portfolios.service_id', $filter['services']);
        }
        if (!empty($filter['term'])) {
            $query->where('portfolios.title', 'like', '%' . $filter['term'] . '%');
        }
        if (!empty($filter['city_id'])) {
            $query->where('users.city_id', $filter['city_id']);
        }
        if (!empty($filter['province_id'])) {
            $query->join('provinces', 'provinces.id', '=', 'cities.province_id')
                ->where('provinces.id', $filter['province_id']);
        }
        $orderBy = in_array($orderBy, ['discount', 'created_at', 'laddered_at']) ? $orderBy : 'created_at';
        $sortBy = in_array($sortBy, ['asc', 'desc']) ? $sortBy : 'desc';
        $scoreQuery = "COALESCE(portfolios.laddered_at, portfolios.created_at) as score";
        if ($orderBy == 'discount') {
            $query->selectRaw("portfolios.*, (price - COALESCE(discount_price, 0)) as discount, $scoreQuery");
        } else {
            $query->selectRaw("portfolios.*, $scoreQuery");
        }
        $query->orderByDesc('score')->orderBy($orderBy, $sortBy);
        return $query;
    }

    public function doLadder(array $data)
    {
        return true;
    }
}
