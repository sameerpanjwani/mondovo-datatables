<?php namespace Mondovo\Datatable\Contracts;

/**
 * Created by PhpStorm.
 * User: maximizer
 * Date: 14/5/15
 * Time: 1:24 PM
 */

// Simple Interface for each Adapter we create for Datatable
interface DataTableAdapterInterface {

    /**
     * Gets query and returns instance of class
     *
     * @param $builder
     * @return object
     */
    public function of($builder);

    /**
     * Organizes works for creating datatable json output
     *
     * @param bool $mDataSupport
     * @return string
     */
    public function make($mDataSupport);

    /**
     * Edit column's content
     *
     * @param  string $name
     * @param  string $content
     * @return Datatables
     */
    public function editColumn($name, $content);

    /**
     * Add column in collection
     *
     * @param string $name
     * @param string $content
     * @param bool|int $order
     * @return Datatables
     */
    public function addColumn($name, $content, $order = false);

    /**
     * Override default column filter search
     *
     * @param int $index
     * @param string $method
     * @param mixed ...,... All the individual parameters required for specified $method
     * @return $this
     */
    public function filterColumn();
}