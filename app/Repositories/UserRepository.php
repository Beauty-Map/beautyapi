<?php


namespace App\Repositories;

use App\Interfaces\UserInterface;
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
}
