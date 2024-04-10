<?php


namespace App\Repositories;

use App\Interfaces\PlanInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PlanRepository
 *
 * @package \App\Repositories
 */
class PlanRepository extends BaseRepository implements PlanInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        parent::__construct($model);
    }
}
