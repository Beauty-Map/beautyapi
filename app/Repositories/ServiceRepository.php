<?php


namespace App\Repositories;

use App\Interfaces\ServiceInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ServiceRepository
 *
 * @package \App\Repositories
 */
class ServiceRepository extends BaseRepository implements ServiceInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }

    public function findChildrenByPaginate(array $filter, int $page, int $limit, string $orderBy, string $sortBy)
    {
        return $this->findChildrenQuery($filter, $orderBy, $sortBy)->paginate($limit);
    }

    public function findChildrenBy(array $filter, string $orderBy, string $sortBy)
    {
        return $this->findChildrenQuery($filter, $orderBy, $sortBy)->get();
    }

    /**
     * @param array $filter
     * @param string $orderBy
     * @param string $sortBy
     * @return Builder
     */
    public function findChildrenQuery(array $filter, string $orderBy, string $sortBy)
    {
        return $this->model->newQuery()
            ->orderBy($orderBy, $sortBy)
            ->whereNotNull('parent_id')
            ->where($filter);
    }
}
