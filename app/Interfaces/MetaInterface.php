<?php


namespace App\Interfaces;

/**
 * Interface MetaInterface
 * @package App\Interfaces
 */
interface MetaInterface extends BaseInterface
{

    public function insertOrAdd(array $values, int $id, string $type = 'user');
}
