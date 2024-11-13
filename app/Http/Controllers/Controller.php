<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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
}
