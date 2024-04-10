<?php


namespace App\Repositories;

use App\Interfaces\IntroInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class IntroRepository
 *
 * @package \App\Repositories
 */
class IntroRepository extends BaseRepository implements IntroInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }
}
