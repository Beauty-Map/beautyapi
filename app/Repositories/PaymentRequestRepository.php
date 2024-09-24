<?php


namespace App\Repositories;

use App\Interfaces\PaymentRequestInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentRequestRepository
 *
 * @package \App\Repositories
 */
class PaymentRequestRepository extends BaseRepository implements PaymentRequestInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }
}
