<?php
/**
 * Created by PhpStorm.
 * User: maximizer
 * Date: 9/6/15
 * Time: 9:10 AM
 */

namespace Mondovo\Datatable\Contracts;

interface DataTableJsInterface {

    public function setAjaxUrl($url);

    public function setPageLength($page_length);

    public function setDataColumnsInJs($columns);

    public function datatableJsOutput($table_id);

    public function setNotVisibleColumnsIndexInJs($columns);

    public function setNotVisibleColumnsNameInJs($columns);

    public function setNotSearchableColumnsIndexInJs($columns);

    public function setNotSearchableColumnsNameInJs($columns);

    public function setNotSortableColumnsIndexInJs($columns);

    public function setNotSortableColumnsNameInJs($columns);

    public function setClassesForColumnsIndexInJs($columnsIndexWithClassNameAsKey);

    public function setClassesForColumnsNameInJs($columnsNamesWithClassNameAsKey);

    public function setCheckboxColumnsIndexInJs($columns);

    public function setCheckboxColumnsNameInJs($columns);

    public function enableDrawCheckbox();

    public function setSortingOrderColumnsIndexInJs(array $columnsIndexWithSortingOrder);

    public function setSortingOrderColumnsNameInJs(array $columnsNameWithSortingOrder);

    public function addToCheckboxControls($html_string);

    /**
     * @param string $button_text
     * @param string $column_name_or_index
     * @param $type
     * @return $this
     */
    public function addToKeywordManager($column_name_or_index, $type, $button_text='');

    /**
     * @param string $button_text
     * @param string $column_name_or_index
     * @param string $type
     * @return $this
     */
    public function addToPageManager($column_name_or_index, $type, $button_text='');

    /**
     * @param string $column_name_or_index
     * @param string $button_text
     * @param $type
     * @return $this
     */
    public function addToTagManager($column_name_or_index, $type, $button_text='');

    public function processBulkKeywordDifficulty($column_name_or_index, $button_text='');

    public function copyToClipboard($column_name, $button_text, $callback_fn = '');

    public function copyAndAddToFilterByColumnName($column_name, $button_text, $callback_fn);

    public function addOptionsToDataTables($js_objects);

    /**
     * @return array
     */
    public function getDataColumns();

    /**
     * @param array $fixed_params
     * @return $this
     */
    public function setFixedColumns(array $fixed_params);

    public function enableKeywordManagerColumnName();

    public function enablePageManagerColumnName();

    public function enableTagManagerColumnName();

    public function enableCopyToClipboardColumnName();

    public function enableCopyAndAddToFilterByColumnName();

    public function enableBulkKDColumnName();

    public function hideDefaultHiddenColumns();

    public function setCustomEmptyTableMessage($message);

    public function setAutoWidth($auto_width);

    public function enableLoadingOnScroll();

    public function disableLoadingOnScroll();

    public function setScrollY($scroll_y);

    public function enableTextSelectorFilter($column_index);

    public function setTableInfo($description);

}