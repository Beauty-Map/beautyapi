<?php


namespace App\Interfaces;

/**
 * Interface UserInterface
 * @package App\Interfaces
 */
interface UserInterface extends BaseInterface
{
    public function nearest(array $filter = [], string $sortBy = 'desc');

    public function nearestByPagination(array $filter = [],  int $page = 1, int $limit = 10, string $sortBy = 'desc');

    public function doLadder();

    public function referredBy(array $filter = [], string $sortBy = 'desc');

    public function referredByPagination(array $filter = [],  int $page = 1, int $limit = 10, string $sortBy = 'desc');
}
