<?php


namespace App\Repositories;

use App\Interfaces\CityInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProvinceRepository
 *
 * @package \App\Repositories
 */
class CityRepository extends BaseRepository implements CityInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }

    public function provinceCitiesByPagination(int $provinceId, int $page, int $limit)
    {
        return $this->provinceCitiesQuery($provinceId)->paginate($limit);
    }

    public function provinceCities(int $provinceId)
    {
        return $this->provinceCitiesQuery($provinceId)->get();
    }

    public function provinceCitiesQuery(int $provinceId)
    {
        $query = $this->model->newQuery();
        return $query->where('province_id', $provinceId);
    }
}
