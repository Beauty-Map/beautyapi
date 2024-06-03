<?php


namespace App\Interfaces;

/**
 * Interface PortfolioInterface
 * @package App\Interfaces
 */
interface PortfolioInterface extends BaseInterface
{

    public function searchByPaginate(array $filter, int $page, int $limit, string $orderBy = 'created_at', string $sortBy = 'desc');

    public function searchBy(array $filter, string $orderBy = 'created_at', string $sortBy = 'desc');

    public function doLadder(array $data);
}
