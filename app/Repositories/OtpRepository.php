<?php


namespace App\Repositories;

use App\Interfaces\OtpInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OtpRepository
 *
 * @package \App\Repositories
 */
class OtpRepository extends BaseRepository implements OtpInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }

    /**
     * Find one based on a different column or through exception
     * @param array $data
     * @param string $orderBy
     * @param string $sortBy
     * @return mixed
     */
    public function findOneBy(array $data, $orderBy = 'id', $sortBy = 'desc') {
        return $this->model->where($data)
            ->where('updated_at', '>=', Carbon::now()->subMinute())
            ->orderBy($orderBy, $sortBy)->first();
    }

    public function make(array $inputs)
    {
        $otp = $this->findOneBy([
            'phone_number' => $inputs['phone_number'],
            'type' => $inputs['type'],
        ]);
        if ($otp) {
            $otp->update([
                'code' => $inputs['code']
            ]);
            return $otp;
        } else {
            return $this->create($inputs);
        }
    }

    public function validate(array $inputs)
    {
        $otp = $this->findOneBy([
            'phone_number' => $inputs['phone_number'],
            'type' => $inputs['type'],
        ]);
        if (!$otp) {
            return false;
        }
        $otp->delete();
        return true;
    }
}
