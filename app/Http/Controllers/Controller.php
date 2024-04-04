<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class Controller
{
    protected function createError(string $name, string $error, int $status): JsonResponse
    {
        return response()->json(['errors' => [ $name => [$error]]], $status);
    }

    protected function createCustomResponse($content = null, int $status = 200): JsonResponse
    {
        return response()->json($content, $status);
    }

    protected function accessDeniedError(): JsonResponse
    {
        return $this->createError('access', Constants::ACCESS_ERROR, 403);
    }

    /**
     * @return bool
     */
    public function hasPage(): bool
    {
        return request()->has('page');
    }

    /**
     * @return bool
     */
    public function hasLimit(): bool
    {
        return request()->has('limit');
    }

    /**
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getPage(): int
    {
        return request()->has('page') ? request()->get('page') : 1;
    }

    /**
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getLimit(): int
    {
        return request()->has('limit') ? request()->get('limit') : 10;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getParam($key)
    {
        return request()->get($key);
    }

    public function getAuth(): User
    {
        /** @var User $auth */
        $auth = Auth::user();
        return $auth;
    }

    function randomCode($length): string
    {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'), range(0, 9), range('A', 'Z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }

    public function normalizePhoneNumber($phoneNumber)
    {
        if (Str::of($phoneNumber)->test('/(0|0098|\+98)9(0[1-5]|[1 3]\d|2[0-2]|98|00)\d{7}/')) {
            if (Str::of($phoneNumber)->test('/(\+98)9\d{9}/')) {
                return "0" . Str::substr($phoneNumber, 3);
            } else {
                return $phoneNumber;
            }
        } else {
            return "";
        }
    }
}
