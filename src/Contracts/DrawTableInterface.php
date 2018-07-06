<?php
/**
 * Created by PhpStorm.
 * User: maximizer
 * Date: 5/6/15
 * Time: 4:15 PM
 */

namespace Mondovo\DataTable\Contracts;


interface DrawTableInterface {

    public function setTableId($table_id);

    public function setDelimiter($delimiter);

    public function getDelimiter();

    public function setTableClasses($table_class_names);

    public function setColGroup($col_group);

    public function setColumnDefinitions($column_definitions);

    public function setColumnDefinitionsRawValues($column_definitions);

    public function draw();

    public function withFilter();

    public function withCheckbox($checkbox_th_width='');

    public function setColumnsData($data);

    public function addToToolbar($html_string);

    public function hideToolbar();

    /**
     * @param string[] $array_of_filter_conditions
     * Ex: [ ['column_name', 'operator_name', 'value' ], ['column_name', 'operator_name', 'value' ] ];
     * @return string json_encode
     */
    public function setPreFilterConditions(array $array_of_filter_conditions);

    /**
     * @param string [] $dataColumns
     * @return array
     */
    public function setDataColumns(array $dataColumns);

    /**
     * @param $pre_filter_title
     * @param string[] $array_of_filter_conditions
     * Ex: [ ['column_name', 'operator_name', 'value' ], ['column_name', 'operator_name', 'value' ] ];
     * Or
     * Ex: ['column_name', 'operator_name', 'value' ]  If it has only one condition
     * @param string $overwrite_existing_filter
     * @param string $filter_id
     * @return $this
     */
    public function setPreFilterConditionsInToolbar($pre_filter_title, $array_of_filter_conditions, $overwrite_existing_filter = 'true', $filter_id= '');

    /**
     * @param $table_id
     * @param array $column_definitions
     * @param array $col_group
     * @param string $table_class_names You may over-write the classes by prefixing the beginning with an "o:"
     * @return $this
     */
    public function drawDataTable($table_id, $column_definitions = array(), $col_group = array(), $table_class_names = "");

    /**
     * @return string
     */
    public function getTooltipSeparator();

    public function enableKeywordGroupFilter($column_name);

    public function enableTableOperations();

    public function enableCheckBoxLimit($limit);

}