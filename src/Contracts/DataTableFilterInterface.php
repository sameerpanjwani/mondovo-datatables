<?php
/**
 * Created by PhpStorm.
 * User: maximizer
 * Date: 5/6/15
 * Time: 3:34 PM
 */

namespace Mondovo\DataTable\Contracts;


interface DataTableFilterInterface {

    public function setAllColumnNames();

    public function setAllRawColumns($rawColumnsIndex);

    public function setAllHavingColumns($havingColumnsIndex);

    public function setAllDateColumns($dateColumnsIndex);

    public function processGlobalFilterInCollection();

    public function processGlobalFilterInQuery();

    public function processIndividualFilter($individual_filter_operator);

    public function processCustomFilter();

    public function extractColumnName($column_name, $type);

    public function extractAliasName($column_name);

    public function stringToArray($arrayOfElements);

    public function setConditionForCheckboxColumn();

    public function setDefaultValues(array $column_name_default_value_pair);
}