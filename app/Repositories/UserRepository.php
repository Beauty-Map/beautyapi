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

    public function nearest(string $sortBy = 'desc')
    {
        return $this->nearestQuery()->get();
    }

    public function nearestByPagination(int $page = 1, int $limit = 10, string $sortBy = 'desc')
    {
        return $this->nearestQuery()->paginate($limit);
    }

    public function nearestQuery(string $sortBy = 'desc')
    {
        $query = $this->model->newQuery();
        return $query;
    }
}
