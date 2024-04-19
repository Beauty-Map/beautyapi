<?php


namespace App\Repositories;

use App\Interfaces\WalletInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WalletRepository
 *
 * @package \App\Repositories
 */
class WalletRepository extends BaseRepository implements WalletInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }
}
