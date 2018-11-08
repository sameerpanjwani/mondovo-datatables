<?php
/**
 * Created by PhpStorm.
 * User: maximizer
 * Date: 5/6/15
 * Time: 2:44 PM
 */

namespace Mondovo\DataTable;

use Illuminate\Support\Facades\App;
use Mondovo\DataTable\Contracts\KeywordGroupPluginServiceInterface;
use Mondovo\DataTable\Contracts\DataTableFilterInterface;
use Mondovo\DataTable\Contracts\DataTableJsInterface;
use Mondovo\DataTable\Contracts\DrawTableInterface;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Excel;
use Illuminate\Support\Str;
use PHPExcel_Worksheet_Drawing;
use PHPExcel_Worksheet_MemoryDrawing;

/**
 * Class MyDataTable
 * @package App\Helpers\DataTable
 */
class MyDataTable
{
    protected $meta_data = [];

    protected $keyword_group_data = [];

    protected $keyword_group_data_md5_print = '';

    protected $builder;

    protected $drawtable;

    protected $datatable;

    protected $datatable_js;

    protected $request;

    protected $logo_path;

    /**
     * @param DrawTableInterface $drawtable
     * @param DataTableFilterInterface $datatable
     * @param DataTableJsInterface $datatable_js
     * @param Request $request
     */
    function __construct(DrawTableInterface $drawtable, DataTableFilterInterface $datatable, DataTableJsInterface $datatable_js, Request $request)
    {

        /* DrawTable Object */
        $this->drawtable = $drawtable;

        /* DatatableFilter Object / Datatable Object */
        $this->datatable = $datatable;

        /* DatatableJs Object */
        $this->datatable_js = $datatable_js;

        $this->request = $request;

    }


    /* Datatable Filter Related functions */
    /**
     * $builder can either be the query object or an array in the following format: ['query_object'=>$query_object_variable,'raw_indexes'=>$array_of_raw_indexes_for_the_query,'having_indexes'=>$array_of_having_indexes_for_the_query,'date_indexes'=>$array_of_data_indexes_for_the_query]. If passing an array format, the index arrays are completely optional and only the query_object need be passed.
     * @param $builder
     * @return $this
     */
    public function of($builder)
    {
        $this->builder = $builder;
        $this->prepareBuilderIfArray($builder);

        //Commented this function on 1st February, 2015 by Sameer Panjwani as the function above prepareBuilderIfArray initializes the builder depending on whether it's an array or not
        //$this->datatable->of($builder);

        //Adding Having columns and Raw Columns index automatically for QueryBuilder Object
        $input_columns_query_builder_object = $this->datatable->columns;
        $having_and_raw_columns = [];

        foreach ($input_columns_query_builder_object as $column_index => $column_expr) {
            if ($column_expr instanceof Expression) {
                $having_and_raw_columns[] = $column_index;
            }
        }

        if (!empty($having_and_raw_columns)) {
            $this->setAllHavingColumns($having_and_raw_columns)->setAllRawColumns($having_and_raw_columns);
        }

        return $this;
    }

    /**
     * @param Illuminate\Http\JsonResponse $response
     * @return Excel
     */
    public function exportTo($response)
    {
        ini_set('memory_limit', '4096M'); //This is required to export tables with many rows like 50,0000
        $file_type = \Request::get('file_type');
        $report_name = \Request::get('export_report_name');
        $report_date = \Request::get('export_report_date');
        $paying_user = true; //Hard coded to true for now

        $file_name = ($report_name != "") ? $report_name : "Mondovo Report";
        $file_name = $file_name . " - " . date('jS F Y');

        list($head_rows, $rows) = $this->prepareExcelRows($response, $paying_user);

        $excel = Excel::create($file_name, function ($excel) use ($file_name, $report_name, $report_date, $head_rows, $rows, $paying_user) {
            $sheet_name = (strlen($file_name) > 31) ? substr($file_name, 0, 29) . ".." : $file_name;
            $excel->sheet($sheet_name, function ($sheet) use ($report_name, $report_date, $head_rows, $rows, $paying_user) {
                $row_count = 3;

                if (!$paying_user) {
                    $sheet->setCellValue('B2', "Only 5 rows shown as your plan does not allow for full downloads or you are still under a free trial.");
                    $sheet->mergeCells('B2:H2');

                    $sheet->row(2, function ($row) {
                        $row->setFontWeight('bold');
                        $row->setFontColor('#ff0000');
                    });
                }

                if ($report_name != '') {
                    $sheet->setCellValue('A' . $row_count, "Report Name:");
                    $sheet->setCellValue('B' . $row_count, $report_name);

                    $sheet->row($row_count, function ($row) use ($paying_user) {
                        $row->setFontWeight('bold');

                        if (!$paying_user)
                            $row->setFontColor('#ff0000');
                    });

                    $row_count++;
                }

                if ($report_date != '') {
                    $sheet->setCellValue('A' . $row_count, "Report Date:");
                    $sheet->setCellValue('B' . $row_count, $report_date);

                    $sheet->row($row_count, function ($row) use ($paying_user) {
                        $row->setFontWeight('bold');

                        if (!$paying_user)
                            $row->setFontColor('#ff0000');
                    });

                    $row_count++;
                }

                $row_count++;


                /////////////
                //dd($head_rows);
                $header_starts_at = $row_count;
                $index = 0;
                $merged_rows = [];
                foreach ($head_rows as $row_heading) {
                    $alphabet_index = 0;
                    $col_index = 0;
                    foreach ($row_heading as $heading) {
                        //print_data($merged_rows);
                        if (isset($merged_rows[$row_count])) {
                            while (in_array($alphabet_index, $merged_rows[$row_count])) {
                                $alphabet_index++;
                            }
                        }

                        $cell = $this->getExcelColumnNameFromNumber($alphabet_index) . "$row_count";
                        $cell_val = strip_tags($heading->col_name);
                        //echo '$sheet->setCellValue("' . $cell . '", "'. $cell_val . '");' . "<br />";
                        while ($sheet->getCell($cell) != "") {
                            $alphabet_index++;
                            $cell = $this->getExcelColumnNameFromNumber($alphabet_index) . "$row_count";
                        }
                        $sheet->setCellValue($cell, $cell_val);

                        if ($heading->rowspan > 1) {
                            $merge = $this->getExcelColumnNameFromNumber($alphabet_index) . $row_count . ":" . $this->getExcelColumnNameFromNumber($alphabet_index) . ($row_count + $heading->rowspan - 1);
                            //echo '$sheet->mergeCells("' . $merge . '");' . "<br>";
                            $sheet->mergeCells($merge);

                            for ($i = $row_count + 1; $i < $row_count + $heading->rowspan; $i++) {
                                $merged_rows[$i][] = $alphabet_index;
                            }
                        }
                        if ($heading->colspan > 1) {
                            $merge = $this->getExcelColumnNameFromNumber($alphabet_index) . $row_count . ":" . $this->getExcelColumnNameFromNumber($alphabet_index + ($heading->colspan - 1)) . $row_count;
                            //echo "$alphabet_index + " . $heading->colspan . "<br>";
                            //echo '$sheet->mergeCells("' . $merge . '");' . "<br>";
                            $sheet->mergeCells($merge);
                        }
                        //echo "<br>";

                        $sheet->cell($cell, function ($cell) {
                            // manipulate the cell
                            $cell->setBorder('solid', 'solid', 'solid', 'solid');
                            $cell->setAlignment('center');
                            $cell->setValignment('center');
                        });

                        $alphabet_index += $heading->colspan;
                        $col_index++;
                    }

                    $index++;
                    $row_count++;
                }
                //dd('TEST');

                ////////////

                $j = $header_starts_at + count($head_rows);
                for ($i = $header_starts_at; $i < $j; $i++)
                    $sheet->row($i, function ($row) {
                        $row->setFontWeight('bold');
                        $row->setBackground('#366092');
                        $row->setFontColor('#ffffff');
                        $row->setFontSize(12);
                    });

                $sheet->fromArray($rows, null, 'A' . $row_count, true, false);

                $logo_location = $this->getLogo();

                if ($this->isExternalUrl($logo_location)) {
                    $image = $this->generateImage($logo_location);
                    $objDrawing = new PHPExcel_Worksheet_MemoryDrawing;
                    $objDrawing->setImageResource($image);
                    $objDrawing->setRenderingFunction(
                        PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG
                    );
                    $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);

                } else {
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath($logo_location);
                }

                $objDrawing->setCoordinates('A1');
                $objDrawing->setOffsetX(10);
                $objDrawing->setOffsetY(20);
                $objDrawing->setWorksheet($sheet);

                $sheet->getRowDimension('1')
                    ->setRowHeight(70);

            });
        })->export($file_type);

        return $excel;
    }

    protected function isExternalUrl($logo_location)
    {

        if (strpos($logo_location, "http") === 0) {
            return true;
        }

        return false;

    }

    protected function generateImage($logo_location)
    {
        $image_path_parts = explode(".", $logo_location);
        $image_extension = $image_path_parts[count($image_path_parts) - 1];
        switch ($image_extension) {
            case "gif":
                $image = imagecreatefromgif($logo_location);
                break;
            case "png":
                $image = imagecreatefrompng($logo_location);
                break;
            case "jpg" or "jpeg":
                $image = imagecreatefromjpeg($logo_location);
                break;
            default:
                throw new \Exception("Invalid Logo Image Extension. Needs to be .gif/.jpg/.jpeg/.png");
                break;
        }
        return $image;
    }


    protected function getLogo()
    {
        $logo_loc = config('mondovo-datatable.default_logo_url');
        return $logo_loc;
    }

    public function getExcelColumnNameFromNumber($num)
    {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return $this->getExcelColumnNameFromNumber($num2 - 1) . $letter;
        } else {
            return $letter;
        }
    }

    /**
     * @param Illuminate\Http\JsonResponse $response
     * @param $paying_user
     * @return Array
     */
    public function prepareExcelRows($response, $paying_user)
    {
        $aStripColumns = [];
        $column_names = \Request::get('export_column_names');
        $export_strip_columns = \Request::get('export_strip_columns');
        $export_num_rows = \Request::get('export_num_rows');

        $aColumnNames = json_decode($column_names);
        if ($export_strip_columns != "") {
            $aStripColumns = explode($this->drawtable->excel_column_delimiter, $export_strip_columns);
        }

        $rows = $this->fetchRecordsForExcel($response);

        $counter = 0;
        $excel_rows = [];
        foreach ($rows as $row) {
            $temp = (array)$row;
            $this->removeColumnsForExcel($aStripColumns, $temp);
            //$excel_rows[] = array_map('strip_tags', $temp);
            $temp = array_map('strip_tags', $temp);

            foreach ($temp as $key => $cell_value) {
                $cell_value = trim($cell_value);
                if (substr($cell_value, 0, 1) == '=') { //Check if the cell value starts with =. if yes then add a space at the beginning of cell value
                    $cell_value = " " . $cell_value;
                }
                $temp[$key] = (is_numeric($cell_value)) ? (float)$cell_value : $cell_value;
            }

            $excel_rows[] = $temp;

            $counter++;

            if (!$paying_user && $counter > 4)
                break;
        }

        if ($export_num_rows > 0) {
            $excel_rows = array_slice($excel_rows, 0, $export_num_rows);
        }

        $excel_rows = [$aColumnNames, $excel_rows];

        return $excel_rows;
    }

    /**
     * @param Illuminate\Http\JsonResponse $response
     * @return mixed
     */
    public function fetchRecordsForExcel($response)
    {
        $response_object = $response->getData();
        return $response_object->data;
    }

    /**
     * @param array $aStripColumns
     * @param array $array_columns
     */
    public function removeColumnsForExcel($aStripColumns, &$array_columns)
    {
        if (isset($array_columns['check_box_id']))
            unset($array_columns['check_box_id']);

        arsort($aStripColumns);
        foreach ($aStripColumns as $column_index) {
            if (is_numeric($column_index))
                array_splice($array_columns, $column_index, 1);
            else
                unset($array_columns[$column_index]);
        }
    }

    /**
     * When we use DB Raw in our query that column index should come here
     *
     * @param mixed $rawColumnsIndex Can be an array or comma separated
     * @return $this
     */
    public function setAllRawColumns($rawColumnsIndex)
    {
        $this->datatable->setAllRawColumns($rawColumnsIndex);
        return $this;
    }

    /**
     * When we use Having Condition in our query that column index should come here
     *
     * @param $havingColumnsIndex
     * @return $this
     */
    public function setAllHavingColumns($havingColumnsIndex)
    {
        $this->datatable->setAllHavingColumns($havingColumnsIndex);
        return $this;
    }

    /**
     * When we use Any Date Column Condition in our query that column index should come here
     *
     * @param $dateColumnsIndex
     * @return $this
     */
    public function setAllDateColumns($dateColumnsIndex)
    {
        $this->datatable->setAllDateColumns($dateColumnsIndex);
        return $this;
    }

    /**
     * @param $name
     * @param $content
     * @return $this
     */
    public function editColumn($name, $content)
    {
        $this->datatable->editColumn($name, $content);
        return $this;
    }

    public function disableCache()
    {
        $this->datatable->disableCache();
        return $this;
    }

    /**
     * Order: Not Done
     * Posion: always last
     *
     * @param $name
     * @param $content
     * @return $this
     */
    public function addColumn($name, $content, $order = false)
    {
        $this->datatable->addColumn($name, $content, $order);
        return $this;
    }

    /**
     * @param bool $mDataSupport
     * @return mixed
     */
    public function make($mDataSupport = false)
    {
        $export = \Request::get('export');
        $checkbox_column = \Request::get('checkbox_column');

        //For Column Data Copy - Added By Nikhil
        $data_copy_flag = \Request::get('data_copy') == 'on';
        $data_copy_column = \Request::get('data_copy_col_num');

        //For Column Data Copy - Added By Nikhil
        $data_keyword_grouping = \Request::get('data_keyword_grouping') == 'on';
        $keyword_grouping_column_name = \Request::get('keyword_grouping_column_name');
        $keyword_grouping_column_index = \Request::get('keyword_grouping_column_index');

        $data = $this->datatable->make($mDataSupport);
        if ($data_keyword_grouping && !empty($keyword_grouping_column_name)) {
            return $this->setKeywordGroups($keyword_grouping_column_name, $data, $keyword_grouping_column_index);
        } elseif ($data_keyword_grouping) {
            return json_encode([]);
        }

        $data = $this->appendMetaData($data);

        if ($data_copy_flag && is_numeric($data_copy_column)) {
            $column_name = \Request::get('columns')[$data_copy_column]['data'];
            $copied_data = $this->getGivenColumnValues($data, $column_name);
            return json_encode($copied_data);
        }

        if ($export) {
            $this->exportTo($data);
        } elseif ($checkbox_column) {
            //return $this->builder->pluck($checkbox_column); //only the selected checkbox column data will return // This give all the values so commented and below line is implemented
            return $this->getGivenColumnValues($data, $checkbox_column);
        } else {
            return $data;
        }
    }

    //This function is mainly used to get the checkbox column values when the user clicks on select all records button
    protected function getGivenColumnValues($data, $column)
    {
        $values = [];

        $main_data_array = $data->getData()->data;
        foreach ($main_data_array as $main_data) {
            $values[] = trim(strip_tags($main_data->$column));
        }

        return $values;
    }

    /**
     * @param array $column_name_default_value_pair
     * @return $this
     */
    public function setDefaultValues(array $column_name_default_value_pair)
    {
        $this->datatable->setDefaultValues($column_name_default_value_pair);
        return $this;
    }

    /**
     * /* This function as we r handling in filter no need now
     * If u have called this function somewhere doesn't makes any difference
     * @return $this no need
     */
    public function filterColumn()
    {
        $params = func_get_args();
        call_user_func_array(array($this->datatable, "filterColumn"), $params);

        return $this;
    }


    /* Draw Table Related functions */

    /**
     * @param $table_id
     * @return $this
     */
    public function setTableId($table_id)
    {
        $this->drawtable->setTableId($table_id);
        return $this;
    }

    public function setTableInfo($description)
    {
        $this->datatable_js->setTableInfo($description);
        return $this;
    }

    /**
     * @param $table_class_names
     * @return $this
     */
    public function setTableClasses($table_class_names)
    {
        $this->drawtable->setTableClasses($table_class_names);
        return $this;
    }

    /**
     * @param $col_group
     * @return $this
     */
    public function setColGroup($col_group)
    {
        $this->drawtable->setColGroup($col_group);
        return $this;
    }

    /**
     * @param $column_definitions
     * @return $this
     */
    public function setColumnDefinitions($column_definitions)
    {
        $this->drawtable->setColumnDefinitions($column_definitions);
        return $this;
    }

    /**
     * @param $column_definitions
     * @return $this
     */
    public function setColumnDefinitionsRawValues($column_definitions)
    {
        $this->drawtable->setColumnDefinitionsRawValues($column_definitions);
        return $this;
    }

    /**
     * @param $delimiter
     * @return $this
     */
    public function setDelimiter($delimiter)
    {
        $this->drawtable->setDelimiter($delimiter);
        return $this;
    }

    /**
     * @return $this
     */
    public function enableFilter()
    {
        if ($this->request->input('pdf_view') == "yes") {
            return $this;
        }
        $this->drawtable->withFilter();
        return $this;
    }

    public function enableKeywordGroupFilter($column_name)
    {
        $this->drawtable->enableKeywordGroupFilter($column_name);
        return $this;
    }

    /**
     * Column Name
     */
    public function enableTextSelectorFilter($column_name)
    {
        $data_columns = $this->datatable_js->getDataColumns();
        $check_box_enabled = $this->datatable_js->isEnabledDrawCheckbox();

        $column_index = array_search($column_name, $data_columns);
        if (is_bool($column_index)) {
            return $this;
        }

        if ($check_box_enabled) {
            $column_index++;
        }

        $this->datatable_js->enableTextSelectorFilter($column_index);
        return $this;
    }

    public function enableTableOperations()
    {
        $this->drawtable->enableTableOperations();
        return $this;
    }

    /**
     * @return mixed
     */
    public function drawHtml()
    {
        return $this->drawtable->draw();
    }

    public function renderHtml()
    {
        return $this->drawHtml();
    }

    /* Do not use this function directly */
    public function withCheckbox($checkbox_th_width = '', $rowspan = '1')
    {
        $access_level = config('mondovo-datatable.access_level');
        if ($this->request->input('pdf_view') == "yes" || $access_level == 3) {
            return $this;
        }
        $this->drawtable->withCheckbox($checkbox_th_width, $rowspan);
        $this->datatable_js->enableDrawCheckbox();
        return $this;
    }

    /* $columnIndex = [0,1,2];
     * <input class="mv_single_checkbox" type="checkbox">
     *
    */
    public function withCheckboxColumnsIndex($columnIndex, $checkbox_th_width = '', $rowspan = '1')
    {
        $this->withCheckbox($checkbox_th_width, $rowspan)->setCheckboxColumnsIndexInJs($columnIndex);
        return $this;
    }

    /*
     * $columnName = ['sfg','sdfg'];
     *  <input class="mv_single_checkbox" type="checkbox">
    */
    public function withCheckboxColumnsName($columnName, $checkbox_th_width = '', $rowspan = '1')
    {
        $this->withCheckbox($checkbox_th_width, $rowspan)->setCheckboxColumnsNameInJs($columnName);
        return $this;
    }

    public function setColumnsData($data)
    {
        $this->drawtable->setColumnsData($data);
        return $this;
    }

    /**
     * @param $html_string
     * @return $this
     */
    public function addToToolbar($html_string)
    {
        $this->drawtable->addToToolbar($html_string);
        return $this;
    }

    /**
     * @return $this
     */
    public function hideToolbar()
    {
        $this->drawtable->hideToolbar();
        return $this;
    }

    /**
     * @return $this
     */
    public function showToolbar()
    {
        $this->drawtable->showToolbar();
        return $this;
    }

    /**
     * @return $this
     */
    public function hideExportButton()
    {
        $this->drawtable->hideExportButton();
        return $this;
    }

    /**
     * @param array $export_settings ['report_name' => 'Report Name', 'report_date' => '2016-03-18', 'strip_column_index' => [0, 1, 2, 3]]
     * @return $this
     */
    public function showExportButton($export_settings = [])
    {
        $this->drawtable->showExportButton($export_settings);
        return $this;
    }

    /**
     * @param string[] $array_of_filter_conditions
     * Ex: [ ['column_name', 'operator_name', 'value' ], ['column_name', 'operator_name', 'value' ] ];
     * Or
     * Ex: ['column_name', 'operator_name', 'value' ]  If it has only one condition
     * @return string json_encode
     */
    public function setPreFilterConditions($array_of_filter_conditions)
    {
        $this->drawtable->setDataColumns($this->datatable_js->getDataColumns());

        return $this->drawtable->setPreFilterConditions($array_of_filter_conditions);
    }

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
    public function setPreFilterConditionsInToolbar($pre_filter_title, $array_of_filter_conditions, $overwrite_existing_filter = 'true', $filter_id = '')
    {
        $this->drawtable->setDataColumns($this->datatable_js->getDataColumns());

        $this->drawtable->setPreFilterConditionsInToolbar($pre_filter_title, $array_of_filter_conditions, $overwrite_existing_filter, $filter_id);

        return $this;
    }

    /* DatatableJs Related Functions */

    /**
     * @param $url
     * @return $this
     */
    public function setAjaxUrl($url)
    {
        $this->datatable_js->setAjaxUrl($url);
        return $this;
    }

    /**
     * @param $page_length
     * @return $this
     */
    public function setPageLength($page_length)
    {
        $this->datatable_js->setPageLength($page_length);
        return $this;
    }

    /**
     * @param $message
     * @return $this
     */
    public function setCustomEmptyTableMessage($message)
    {
        $this->datatable_js->setCustomEmptyTableMessage($message);
        return $this;
    }

    /**
     * @param $columns
     * @return $this
     */
    public function setDataColumnsInJs($columns)
    {
        $this->datatable_js->setDataColumnsInJs($columns);
        return $this;
    }

    /**
     * @return mixed
     */
    public function drawJs()
    {
        return $this->datatable_js->datatableJsOutput($this->drawtable->getTableId());
    }

    /**
     * @return mixed
     */
    public function renderJs()
    {
        return $this->drawJs();
    }

    /**
     * @param setNotSearchableSortableVisibleColumnsIndexInJs
     * @return $this
     */
    public function setNotSearchableSortableVisibleColumnsIndexInJs($notSearchableSortableVisibleColumnsIndex)
    {
        $this->setNotSearchableColumnsIndexInJs($notSearchableSortableVisibleColumnsIndex)
            ->setNotSortableColumnsIndexInJs($notSearchableSortableVisibleColumnsIndex)
            ->setNotVisibleColumnsIndexInJs($notSearchableSortableVisibleColumnsIndex);
        return $this;
    }

    /**
     * @param setNotSearchableSortableVisibleColumnsNameInJs
     * @return $this
     */
    public function setNotSearchableSortableVisibleColumnsNameInJs($notSearchableSortableVisibleColumnsName)
    {
        $this->setNotSearchableColumnsNameInJs($notSearchableSortableVisibleColumnsName)
            ->setNotSortableColumnsNameInJs($notSearchableSortableVisibleColumnsName)
            ->setNotVisibleColumnsNameInJs($notSearchableSortableVisibleColumnsName);
        return $this;
    }


    /**
     * @param $columns
     * @return $this
     */
    public function setNotVisibleColumnsIndexInJs($columnsIndex)
    {
        $this->datatable_js->setNotVisibleColumnsIndexInJs($columnsIndex);
        return $this;
    }

    /**
     * @param array $columnsIndexWithSortingOrder
     * @return $this
     */
    public function setSortingOrderColumnsIndexInJs(array $columnsIndexWithSortingOrder)
    {
        $this->datatable_js->setSortingOrderColumnsIndexInJs($columnsIndexWithSortingOrder);
        return $this;
    }

    /**
     * @param array $columnsNameWithSortingOrder
     * @return $this
     */
    public function setSortingOrderColumnsNameInJs(array $columnsNameWithSortingOrder)
    {
        $this->datatable_js->setSortingOrderColumnsNameInJs($columnsNameWithSortingOrder);
        return $this;
    }

    /**
     * @param $columns
     * @return $this
     */
    public function setNotVisibleColumnsNameInJs($columnsName)
    {
        $this->datatable_js->setNotVisibleColumnsNameInJs($columnsName);
        return $this;
    }

    //Added by Sameer on 17th June, 2016

    /**
     * If you have defined a class called 'default-hidden' for any column, it will hide those columns in JS
     * @return $this
     */
    public function hideDefaultHiddenColumns()
    {
        $this->datatable_js->hideDefaultHiddenColumns();
        return $this;
    }

    /**
     * @param $columns
     * @return $this
     */
    public function setNotSearchableColumnsIndexInJs($columnsIndex)
    {
        $this->datatable_js->setNotSearchableColumnsIndexInJs($columnsIndex);
        return $this;
    }

    /**
     * @param $columns
     * @return $this
     */
    public function setNotSearchableColumnsNameInJs($columnsName)
    {
        $this->datatable_js->setNotSearchableColumnsNameInJs($columnsName);
        return $this;
    }

    /**
     * @param $columns
     * @return $this
     */
    public function setNotSortableColumnsIndexInJs($columnsIndex)
    {
        $this->datatable_js->setNotSortableColumnsIndexInJs($columnsIndex);
        return $this;
    }

    /**
     * @param $columns
     * @return $this
     */
    public function setNotSortableColumnsNameInJs($columnsName)
    {
        $this->datatable_js->setNotSortableColumnsNameInJs($columnsName);
        return $this;
    }

    /**
     * @example setClassesForColumnsIndexInJs([ 'exclass'=>[0,1], 'rememberClass'=>[1,2] ])
     * @param $columnsIndexWithClassNameAsKey
     * @return $this
     */
    public function setClassesForColumnsIndexInJs($columnsIndexWithClassNameAsKey)
    {
        $this->datatable_js->setClassesForColumnsIndexInJs($columnsIndexWithClassNameAsKey);
        return $this;
    }

    /**
     * @example setClassesForColumnsNameInJs([ 'exclass'=>['from_page_id','keyword_id'], 'rememberClass'=>['from_page_id','link_id'] ])
     * @param $columnsNamesWithClassNameAsKey
     * @return $this
     */
    public function setClassesForColumnsNameInJs($columnsNamesWithClassNameAsKey)
    {
        $this->datatable_js->setClassesForColumnsNameInJs($columnsNamesWithClassNameAsKey);
        return $this;
    }

    /**
     * @param $columns
     * @return $this
     */
    public function setCheckboxColumnsNameInJs($columns)
    {
        $this->datatable_js->setCheckboxColumnsNameInJs($columns);
        $this->withCheckbox();
        return $this;
    }

    /**
     * @param $columns
     * @return $this
     */
    public function setCheckboxColumnsIndexInJs($columns)
    {
        $this->datatable_js->setCheckboxColumnsIndexInJs($columns);
        $this->withCheckbox();
        return $this;
    }

    /**
     * Pass in language file variables, which will also be used as the query alias
     *
     * @param array $column_definitions_with_alias
     * @param $lang_path
     * @return $this
     * @throws \Exception An exception will be thrown if lang file doesn't exist
     */
    public function setColumnDefinitionsWithAlias(array $column_definitions_with_alias, $lang_path)
    {
        list($column_definitions, $alias_array) = translate_multi_d_array_and_separate_alias($column_definitions_with_alias, $lang_path, $this->drawtable->getDelimiter());

        $this->setColumnDefinitions($column_definitions);
        $this->setColumnDefinitionsRawValues($column_definitions_with_alias);
        $this->setDataColumnsInJs($alias_array);

        return $this;
    }

    /**
     * Adding Text after checkbox
     *
     * @param string $html_string
     * @return $this
     */
    public function addToCheckboxControls($html_string)
    {
        $this->datatable_js->addToCheckboxControls($html_string);
        return $this;
    }

    public function addToKeywordManagerByColumnName($column_name, $type, $button_text = '')
    {
        if ($this->request->input('pdf_view') == "yes") {
            return $this;
        }
        $this->datatable_js->addToKeywordManager($column_name, $type, $button_text);
        $this->datatable_js->enableKeywordManagerColumnName();
        return $this;
    }

    public function addToKeywordManagerByColumnIndex($column_index, $type, $button_text = '')
    {
        if ($this->request->input('pdf_view') == "yes") {
            return $this;
        }
        $this->datatable_js->addToKeywordManager($column_index, $type, $button_text);
        $this->datatable_js->enableKeywordManagerColumnName();
        return $this;
    }

    public function addToPageManagerByColumnName($column_name, $type, $button_text = '')
    {
        if ($this->request->input('pdf_view') == "yes") {
            return $this;
        }
        $this->datatable_js->addToPageManager($column_name, $type, $button_text);
        $this->datatable_js->enablePageManagerColumnName();
        return $this;
    }

    public function addToPageManagerByColumnIndex($column_name, $type, $button_text = '')
    {
        if ($this->request->input('pdf_view') == "yes") {
            return $this;
        }
        $this->datatable_js->addToPageManager($column_name, $type, $button_text);
        $this->datatable_js->enablePageManagerColumnName();
        return $this;
    }

    public function addToTagManagerByColumnName($column_name, $type, $button_text = '')
    {
        if ($this->request->input('pdf_view') == "yes") {
            return $this;
        }
        $this->datatable_js->addToTagManager($column_name, $type, $button_text);
        $this->datatable_js->enableTagManagerColumnName();
        return $this;
    }

    public function enableCopyToClipboardByColumnName($column_name, $button_text, $callback_fn = '')
    {
        if ($this->request->input('pdf_view') == "yes") {
            return $this;
        }

        $this->datatable_js->copyToClipboard($column_name, $button_text, $callback_fn);
        $this->datatable_js->enableCopyToClipboardColumnName();
        return $this;
    }

    public function enableCopyAndAddToFilterByColumnName($column_name, $button_text, $callback_fn)
    {
        if ($this->request->input('pdf_view') == "yes") {
            return $this;
        }

        $this->datatable_js->copyAndAddToFilterByColumnName($column_name, $button_text, $callback_fn);
        $this->datatable_js->enableCopyAndAddToFilterByColumnName();
        return $this;
    }

    public function enableBulkKdProcess($column_name, $button_text)
    {
        if ($this->request->input('pdf_view') == "yes") {
            return $this;
        }

        $this->datatable_js->processBulkKeywordDifficulty($column_name, $button_text);
        $this->datatable_js->enableBulkKDColumnName();
        return $this;
    }

    public function addToTagManagerByColumnIndex($column_name, $type, $button_text = '')
    {
        if ($this->request->input('pdf_view') == "yes") {
            return $this;
        }
        $this->datatable_js->addToTagManager($column_name, $type, $button_text);
        $this->datatable_js->enableTagManagerColumnName();
        return $this;
    }

    /*
    * 'bwidth': 'fixed'
     * @param array $js_objects
     * @return $this
     */
    public function addOptionsToDataTables(array $js_objects)
    {
        $this->datatable_js->addOptionsToDataTables($js_objects);
        return $this;
    }

    /**
     * @param array $fixed_params
     * @return $this
     */
    public function setFixedColumns(array $fixed_params)
    {
        $this->datatable_js->setFixedColumns($fixed_params);
        return $this;
    }

    /**
     * It will post variables to server side
     *
     * function(ajax_data){
     *  ajax_data.key = 1;
     *
     * }
     * @param $complete_javascript_function_as_string
     * @return $this
     */
    public function setCallbackBeforeSendingToAjax($complete_javascript_function_as_string)
    {
        $this->datatable_js->setCallbackBeforeSendingToAjax($complete_javascript_function_as_string);
        return $this;
    }

    /**
     * It will post variables to server side
     *
     * function(ajax_data){
     *  ajax_data.key = 1;
     *
     * }
     * @param $complete_javascript_function_as_string
     * @return $this
     */
    public function setCallbackAfterAjaxSuccess($complete_javascript_function_as_string)
    {
        $this->datatable_js->setCallbackAfterAjaxSuccess($complete_javascript_function_as_string);
        return $this;
    }

    public function setPreDrawCallback($complete_javascript_function_as_string)
    {
        $this->datatable_js->setPreDrawCallback($complete_javascript_function_as_string);
        return $this;
    }

    /**
     * $builder = ['query_object' => $q, 'raw_indexes' => $raw_indexes,'having_indexes'=>$raw_indexes];
     * OR  $builder = Querybuilder Object Or Eloquent Object Or collection Object
     *
     * @param $builder
     * @return \BigShark\SQLToBuilder\BuilderClass|\Guzzle\Service\Builder\ServiceBuilderInterface
     */
    private function prepareBuilderIfArray($builder)
    {
        if (is_array($builder)) {

            $query_object = $builder['query_object'];
            $this->datatable->of($query_object);

            if (isset($builder['raw_indexes'])) {
                $this->setAllRawColumns($builder['raw_indexes']);
            }

            if (isset($builder['having_indexes'])) {
                $this->setAllHavingColumns($builder['having_indexes']);
            }

            if (isset($builder['date_indexes'])) {
                $this->setAllDateColumns($builder['date_indexes']);
            }

            return $query_object;
        }

        return $this->datatable->of($builder);
    }

    private function createShowHideColumnsToolBar($table_id, array $contents, $custom_title = "Show / Hide Columns", $list_id = '')
    {
        return view('components.show-hide-columns-toolbar', ['table_id' => $table_id, 'hide_show_elements' => $contents, 'custom_title' => $custom_title, 'list_id' => $list_id]);
    }

    /* What Needs to be Done:
     *      1. Add a Unique Class Name to identify each column in the Column Definition in Components ((For Example: search_volume_box class added for profile_search_volume)
     *          Example of Component format:
     *              $profile_ranking_insights_table_columns[$engine] = [
                        'profile_search_volume^'.$z.$hidden_shown.'|class:search_volume_box',
                        $from_date.'^'.$z,
                        $comparison_date.'^'.$z.'|class:default-hidden comparison_date_box',
                        'profile_change^'.$z.'|class:change_box',
                        'profile_ranking_domain^'.$z.'|class:default-hidden ranking_domain_box',
                        'profile_ranking_url^'.$z.'|class:default-hidden ranking_url_box',
                        'profile_cpc^'.$z.$hidden_shown.'|class:default-hidden cpc_box',
                        'profile_visibility^'.$z.'|class:default-hidden visibility_box',
                        'profile_traffic_flow^'.$z.'|class:default-hidden traffic_flow_box',
                        'profile_result_type^'.$z.'|class:default-hidden result_type_box'
                    ];
     *      2. Then Create an array like show Below:
     *          Example of array format:
     *              $contents = [
                        "Search Volume" => [
                            "class" => "search-volume-box",
                            "status" => "checked"
                        ],
                        'Ranking Details' => [
                            'option_group' => [
                                "Comparison" => [
                                    "class" => "comparison-date-box",
                                    "status" => "unchecked"
                                ],
                                "Change" => [
                                    "class" => "change-box",
                                    "status" => "checked"
                                ]
                            ]
                        ]
                    ];
     * */
    public function addHideShowColumnsToToolbar(array $contents, $custom_title = "Show / Hide Columns", $list_id = '')
    {
        $table_id = $this->drawtable->getTableId();
        $show_hide_content = $this->createShowHideColumnsToolBar($table_id, $contents, $custom_title, $list_id);
        $this->addToToolbar($show_hide_content);
        return $this;
    }

    /**
     * @param $key
     * @return array
     */
    private function detectHiddenColumn($key, array $hidden_columns, $column_index)
    {
        if (Str::contains($key, "hidden")) {
            array_push($hidden_columns, $column_index);
            return array($hidden_columns, $column_index);
        }
        return array($hidden_columns, $column_index);
    }

    public function setMetaData($meta_data)
    {
        $this->meta_data = $meta_data;
        return $this;
    }

    /**
     * @param $keyword_column_name
     * @param $data
     * @param $column_index
     * @return $this
     */
    public function setKeywordGroups($keyword_column_name, $data, $column_index)
    {
        $data_value_array = $data->getData()->data;

        $keywords = [];
        $counter = 0;
        $max_analyzed_keywords = 25000;
        $warning_flag = false;

        if (empty($column_index)) {
            return json_encode([]);
        }

        if (!empty($data_value_array)) {
            foreach ($data_value_array as $item) {
                if ($counter >= $max_analyzed_keywords) {
                    $warning_flag = true;
                    break;
                }
                $keywords[] = trim(strip_tags($item->$keyword_column_name));
                $counter++;
            }
        }

        if (empty($keywords)) {
            return json_encode([]);
        }

        $keyword_group_plugin_service = App::make(KeywordGroupPluginServiceInterface::class);

        try {
            $keyword_groups = $keyword_group_plugin_service->keywordGroupingType3($keywords);
        } catch (\Exception $e) {
            mail_me('nikhil', 'Msg: ' . $e->getMessage() . ' Line: ' . $e->getLine() . ' File: ' . $e->getFile());
            $keyword_groups = [];
        }

        if (empty($keyword_groups)) {
            return json_encode([]);
        }

        $data_md5_print = md5(serialize($keyword_groups));
        $warning_msg = $warning_flag ? "Warning! We have analyzed only the first $max_analyzed_keywords Keywords." : '';

        return json_encode(['keyword_groups' => $keyword_groups, 'column_index' => $column_index, 'column_name' => $keyword_column_name, 'keyword_data_print' => $data_md5_print, 'warnings' => $warning_msg]);
    }

    /**
     * If you want to give analytics on the data that you supply to data_table, you can set it as 'meta_data' as array or string.
     * You can retrive the 'meta_data' from the table response by using setCallbackAfterAjaxSuccess()
     *
     * @param JsonResponse $data
     * @return JsonResponse
     */
    private function appendMetaData(JsonResponse $data)
    {

        if (empty($this->meta_data) && empty($this->keyword_group_data)) {
            return $data;
        }

        //----------------------------------------------------------//
        //----------------     Adding Meta data    -----------------//
        //----------------------------------------------------------//

        $temp_data = $data->getData();

        if (!empty($this->meta_data)) {
            $temp_data->meta_data = $this->meta_data;
        }

        //----------------------------------------------------------//
        //------------     Adding Keyword Group data    ------------//
        //----------------------------------------------------------//

        if (!empty($this->keyword_group_data)) {
            $temp_data->keyword_group_data = $this->keyword_group_data;
            $temp_data->keyword_group_data_md5_print = $this->keyword_group_data_md5_print;
        }

        $data = new JsonResponse($temp_data);

        return $data;
    }

    /**
     * @param boolean $auto_width
     * @return $this
     */
    public function setAutoWidth($auto_width)
    {
        $this->datatable_js->setAutoWidth($auto_width);
        return $this;
    }

    public function enableLoadingOnScroll()
    {
        $this->datatable_js->enableLoadingOnScroll();

        return $this;
    }

    public function disableLoadingOnScroll()
    {
        $this->datatable_js->disableLoadingOnScroll();

        return $this;
    }

    public function setScrollY($scroll_y)
    {
        $this->datatable_js->setScrollY($scroll_y);

        return $this;
    }


    public function disableSearchingPagingOrderingAndInfo()
    {
        $this->datatable_js->disableSearchingPagingOrderingAndInfo();

        return $this;
    }

    public function disableSearchingPagingAndInfo()
    {
        $this->datatable_js->disableSearchingPagingAndInfo();

        return $this;
    }

    public function disableSearching()
    {
        $this->datatable_js->disableSearching();

        return $this;
    }

    public function disablePaging()
    {
        $this->datatable_js->disablePaging();

        return $this;
    }

    public function disableOrdering()
    {
        $this->datatable_js->disableOrdering();

        return $this;
    }

    public function disableInfo()
    {
        $this->datatable_js->disableInfo();

        return $this;
    }

    public function enableCheckBoxLimit($limit = 10)
    {
        $this->drawtable->enableCheckBoxLimit($limit);

        return $this;
    }

}