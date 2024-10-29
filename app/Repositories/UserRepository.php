<?php


namespace App\Repositories;

use App\Interfaces\UserInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserRepository
 *
 * @package \App\Repositories
 */
class UserRepository extends BaseRepository implements UserInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }

    public function nearest(array $filter = [], string $sortBy = 'desc')
    {
        return $this->nearestQuery()->get();
    }

    public function nearestByPagination(array $filter = [], int $page = 1, int $limit = 10, string $sortBy = 'desc')
    {
        return $this->nearestQuery($filter)->paginate($limit);
    }

    public function nearestQuery(array $filter = [],string $sortBy = 'desc')
    {
        $query = $this->model->newQuery();
        if (array_key_exists('lat', $filter) &&
            $filter['lat'] != null &&
            array_key_exists('lng', $filter) &&
            $filter['lng'] != null) {

        }
        return $query;
    }

    public function doLadder()
    {
        return true;
    }

    public function referredBy(array $filter = [], string $sortBy = 'desc')
    {
        return $this->referredByQuery($filter)->get();
    }

    public function referredByPagination(array $filter = [], int $page = 1, int $limit = 10, string $sortBy = 'desc')
    {
        return $this->referredByQuery($filter)->paginate($limit);
    }

    public function referredByQuery(array $filter = [],string $sortBy = 'desc')
    {
        return $this->model->newQuery()->where('referrer_code', $filter['referrer_code']);
    }

    public function searchByPaginate(array $filter, int $page, int $limit, mixed $orderBy, mixed $sortBy)
    {
        $query = $this->searchQuery($filter, $orderBy, $sortBy);
        return ['last_page' => ceil($query->count() / $limit), 'data' => $query->offset(($page - 1) * $limit)->limit($limit)->get()];
    }

    public function searchQuery(array $filter, string $orderBy = 'created_at', string $sortBy = 'desc'): Builder
    {
        $query = $this->model->newQuery();
        if (array_key_exists('term', $filter) && $filter['term']) {
            $query = $query->where('full_name', 'full_name', '%'.$filter['term'].'%');
        }
        return $query->orderBy($orderBy, $sortBy);
    }
}
