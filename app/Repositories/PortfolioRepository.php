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
        return $this->searchQuery($filter)->offset(($page - 1) * $limit)->limit($limit);
    }

    public function searchBy(array $filter, string $orderBy = 'created_at', string $sortBy = 'desc')
    {
        return $this->searchQuery($filter)->get();
    }

    public function searchQuery(array $filter, string $orderBy = 'created_at', string $sortBy = 'desc'): Builder
    {
        $query = $this->model->newQuery();
        if (array_key_exists('user_id', $filter) && $filter['user_id']) {
            $query = $query->where('user_id', $filter['user_id']);
        }
        if (array_key_exists('services', $filter) && $filter['services'] && count($filter['services']) > 0) {
            $query = $query->whereIn('service_id', $filter['services']);
        }
        return $query->orderBy($orderBy, $sortBy);
    }
}
