<?php


namespace App\Repositories;

use App\Interfaces\ServiceInterface;
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
}
