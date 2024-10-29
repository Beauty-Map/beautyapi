<?php


namespace App\Interfaces;

/**
 * Interface ServiceInterface
 * @package App\Interfaces
 */
interface ServiceInterface extends BaseInterface
{
    public function findChildrenByPaginate(array $filter, int $page, int $limit, string $orderBy, string $sortBy);
    public function findChildrenBy(array $filter, string $orderBy, string $sortBy);
}
