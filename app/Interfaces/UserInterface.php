<?php


namespace App\Interfaces;

/**
 * Interface UserInterface
 * @package App\Interfaces
 */
interface UserInterface extends BaseInterface
{
    public function nearest(string $sortBy = 'desc');

    public function nearestByPagination(int $page = 1, int $limit = 10, string $sortBy = 'desc');
}
