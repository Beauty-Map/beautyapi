<?php


namespace App\Repositories;

use App\Interfaces\UserInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

    public function nearest(array $filter = [], string $sortBy = 'desc')
    {
        return $this->nearestQuery()->get();
    }

    public function nearestByPagination(array $filter = [], int $page = 1, int $limit = 10, string $sortBy = 'desc')
    {
        return $this->nearestQuery($filter)->paginate($limit);
    }

    public function nearestQuery(array $filter = [],string $sortBy = 'desc')
    {
        $query = $this->model->newQuery();
        if (!empty($filter['province_id'])) {
            $query = $query->whereHas('city', function ($query) use ($filter) {
                $query->whereHas('province', function ($q) use ($filter) {
                    $q->where('id', $filter['province_id']);
                });
            });
        }
        if (!empty($filter['city_id'])) {
            $query = $query->where('city_id', $filter['city_id']);
        }
        return $query;
    }

    public function doLadder()
    {
        /** @var User $auth */
        $auth = Auth::user();
        return $auth->update(['laddered_at' => now()]);
    }

    public function referredBy(array $filter = [], string $sortBy = 'desc')
    {
        return $this->referredByQuery($filter)->get();
    }

    public function referredByPagination(array $filter = [], int $page = 1, int $limit = 10, string $sortBy = 'desc')
    {
        return $this->referredByQuery($filter)->paginate($limit);
    }

    public function referredByQuery(array $filter = [],string $sortBy = 'desc')
    {
        return $this->model->newQuery()->where('referrer_code', $filter['referrer_code']);
    }

    public function searchByPaginate(array $filter, int $page, int $limit, mixed $orderBy, mixed $sortBy)
    {
        $query = $this->searchQuery($filter, $orderBy, $sortBy);
        return ['last_page' => ceil($query->count() / $limit), 'data' => $query->offset(($page - 1) * $limit)->limit($limit)->get()];
    }

    public function searchQuery(array $filter, string $orderBy = 'created_at', string $sortBy = 'desc'): Builder
    {
        $query = $this->model->newQuery();
        if (!empty($filter['term'])) {
            $query = $query->where('full_name', 'like', '%'.$filter['term'].'%');
        }
        if (!empty($filter['province_id'])) {
            $query = $query->whereHas('city', function ($query) use ($filter) {
                $query->whereHas('province', function ($q) use ($filter) {
                    $q->where('id', $filter['province_id']);
                });
            });
        }
        if (!empty($filter['city_id'])) {
            $query = $query->where('city_id', $filter['city_id']);
        }
        return $query->orderBy($orderBy, $sortBy);
    }

    public function findByPaginate(array $data, int $page = 1, int $limit = 10, string $orderBy = 'id', string $sortBy = 'desc') {
        $query = $this->findUsers($orderBy, $sortBy, $data);
        return $query->paginate($limit);
    }

    public function findBy(array $data, string $orderBy = 'id', string $sortBy = 'desc') {
        $query = $this->findUsers($orderBy, $sortBy, $data);
        return $query->get();
    }

    public function findUsers(string $orderBy, string $sortBy, array $data): Builder
    {
        $query = $this->model->newQuery()->orderBy($orderBy, $sortBy);
        if (!empty($data['q'])) {
            $q = $data['q'];
            $query = $query->orWhere('full_name', 'like', "%$q%");
            $query = $query->orWhere('email', 'like', "%$q%");
            unset($data['q']);
        }
        return $query->where($data);
    }
}
