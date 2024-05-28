<?php


namespace App\Repositories;

use App\Interfaces\MetaInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class MetaRepository
 *
 * @package \App\Repositories
 */
class MetaRepository extends BaseRepository implements MetaInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }

    public function insertOrAdd(array $values, int $id, string $type = 'user')
    {
        DB::beginTransaction();
        try {
            foreach ($values as $key => $value) {
                if (gettype($value) == 'array') {
                    $value = json_encode($value);
                }
                if (gettype($value) == 'boolean') {
                    $value = $value ? 'true' : 'false';
                }
                if ($value) {
                    $meta = $this->findOneBy([
                        'metaable_id' => $id,
                        'metaable_type' => $type,
                        'key' => $key,
                    ]);
                    if ($meta) {
                        $meta->update([
                            'key' => $key,
                            'value' => $value,
                        ]);
                    } else {
                        $this->model->newQuery()->create([
                            'metaable_id' => $id,
                            'metaable_type' => $type,
                            'key' => $key,
                            'value' => $value,
                        ]);
                    }
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }
}
