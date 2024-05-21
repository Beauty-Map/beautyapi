<?php


namespace App\Repositories;

use App\Interfaces\TicketInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TicketRepository
 *
 * @package \App\Repositories
 */
class TicketRepository extends BaseRepository implements TicketInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }
}
