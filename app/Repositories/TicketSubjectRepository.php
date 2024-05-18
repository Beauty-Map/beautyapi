<?php


namespace App\Repositories;

use App\Interfaces\TicketSubjectInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TicketSubjectRepository
 *
 * @package \App\Repositories
 */
class TicketSubjectRepository extends BaseRepository implements TicketSubjectInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }
}
