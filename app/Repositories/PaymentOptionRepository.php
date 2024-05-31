<?php


namespace App\Repositories;

use App\Interfaces\PaymentOptionInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentOption
 *
 * @package \App\Repositories
 */
class PaymentOptionRepository extends BaseRepository implements PaymentOptionInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }
}
