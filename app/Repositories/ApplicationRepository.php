<?php


namespace App\Repositories;

use App\Interfaces\ApplicationInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ApplicationRepository
 *
 * @package \App\Repositories
 */
class ApplicationRepository extends BaseRepository implements ApplicationInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }
}
