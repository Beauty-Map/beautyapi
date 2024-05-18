<?php


namespace App\Interfaces;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;

/**
 * Interface OtpInterface
 * @package App\Interfaces
 */
interface OtpInterface extends BaseInterface
{
    public function make(array $inputs);

    public function validate(array $inputs);
}
