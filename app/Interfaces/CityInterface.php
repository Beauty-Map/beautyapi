<?php


namespace App\Interfaces;

/**
 * Interface Province
 * @package App\Interfaces
 */
interface CityInterface extends BaseInterface
{
    public function provinceCitiesByPagination(int $provinceId, int $page, int $limit);
    public function provinceCities(int $provinceId);
}
