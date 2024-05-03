<?php


namespace App\Repositories;

use App\Interfaces\ProvinceInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProvinceRepository
 *
 * @package \App\Repositories
 */
class ProvinceRepository extends BaseRepository implements ProvinceInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }
}
