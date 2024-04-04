<?php


namespace App\Repositories;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Interfaces\UserInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserRepository
 *
 * @package \App\Repositories
 */
class UserRepository extends BaseRepository implements UserInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        parent::__construct($model);
    }
}
