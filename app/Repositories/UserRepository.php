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
}
