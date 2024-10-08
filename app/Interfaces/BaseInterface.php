<?php


namespace App\Interfaces;

/**
 * Interface BaseInterface
 * @package App\Interfaces
 */
interface BaseInterface
{
    /**
     * Create a model instance
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes);

    /**
     * Update a model instance
     * @param array $attributes
     * @param int $id
     * @return mixed
     */
    public function update(array $attributes, $id);

    /**
     * Return all model rows
     * @param array $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return mixed
     */
    public function all($columns = array('*'), $orderBy = 'desc', $sortBy = 'id');

    /**
     * Return all model rows by paginate
     * @param string[] $columns
     * @param string $orderBy
     * @param string $sortBy
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function allByPagination($columns = array('*') ,$orderBy = 'id', $sortBy = 'desc', $page = 1, $limit = 10);

    /**
     * Find one by ID
     * @param int $id
     * @return mixed
     */
    public function find($id);

    /**
     * Find one by ID or throw exception
     * @param int $id
     * @return mixed
     */
    public function findOneOrFail($id);

    /**
     * Find based on a different column
     * @param array $data
     * @param string $orderBy
     * @param string $sortBy
     * @return mixed
     */
    public function findBy(array $data, string $orderBy = 'id', string $sortBy = 'desc');

    /**
     * Find one based on a different column or through exception
     * @param array $data
     * @param string $orderBy
     * @param string $sortBy
     * @return mixed
     */
    public function findOneBy(array $data, $orderBy = 'id', $sortBy = 'desc');

    /**
     * Find one based on a different column or through exception
     * @param array $data
     * @return mixed
     */
    public function findOneByOrFail(array $data);

    /**
     * Find based on a different column
     * @param array $data
     * @param int $page
     * @param int $limit
     * @param string $orderBy
     * @param string $sortBy
     * @return mixed
     */
    public function findByPaginate(array $data, int $page = 1, int $limit = 10, string $orderBy = 'id', string $sortBy = 'desc');


    /**
     * Delete one by Id
     * @param int $id
     * @return mixed
     */
    public function delete($id);
}
