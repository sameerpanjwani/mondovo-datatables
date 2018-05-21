<?php namespace Mondovo\DataTable;

/**
 * Created by PhpStorm.
 * User: maximizer
 * Date: 9/6/15
 * Time: 9:04 AM
 */
use Mondovo\DataTable\Contracts\DataTableFilterInterface;
use Mondovo\DataTable\Contracts\DataTableJsInterface;

class DataTableJs implements DataTableJsInterface
{

    protected $jsColumns = [];
    protected $jsOrder = [];

    protected $checkbox_columns = [];
    protected $checkbox_column_name = false;

    protected $keyword_manager_index = -1;
    protected $keyword_manager_column_name = [];

    protected $page_manager_index = -1;
    protected $page_manager_column_name = [];

    protected $tag_manager_index = -1;
    protected $tag_manager_column_name = [];

    protected $keyword_manager_column = [];
    protected $page_manager_column = [];
    protected $tag_manager_column = [];

    protected $keyword_manager_column_type = [];
    protected $page_manager_column_type = [];
    protected $tag_manager_column_type= [];

    protected $keyword_manager_button_text = [];
    protected $tag_manager_button_text= [];
    protected $page_manager_button_text = [];

    protected $dataColumns = [];

    protected $ajax_url;

    protected $page_length;

    protected $notSearchableColumns = [];
    protected $searchableColumnName = false;

    protected $notSortableColumns = [];
    protected $sortableColumnName = false;

    protected $notVisibleColumns = [];
    protected $visibleColumnName = false;

    protected $columnsForClasses = [];
    protected $columnNameForClasses = false;

    protected $sortingOrderColumns = [];
    protected $sortingOrderColumnName = false;

    protected $check_box_column_name;
    protected $check_box_column_index;
    protected $draw_checkbox = false;
    protected $datatable_js_objects = [];

    protected $reserve_column_count = 0;
    protected $hidden_column_class_name = 'hidden';
    protected $checkbox_td_class = 'text-center nopadding';
    protected $checkbox_controls_html_string = '';
    protected $datatable_fixed_columns_objects = [];

    protected $ajax_callback_function = '';

    protected $ajax_success_callback_function = '';

    protected $pre_draw_callback_function = '';

    protected $language_path = '';

    protected $hide_default_hidden_columns = false;

    protected $custom_empty_table_message= '';

    protected $auto_width = true;

    protected $loading_on_scroll = 'no';

    protected $scroll_y = 200;

    protected $copy_to_clipboard_index = -1;
    protected $copy_and_add_to_filter_index = -1;

    protected $copy_to_clipboard_column = [];
    protected $copy_to_clipboard_column_callback = [];
    protected $copy_and_add_to_filter_column_callback = [];
    protected $copy_to_clipboard_button_text = [];
    protected $copy_and_add_to_filter_button_text = [];
    protected $copy_to_clipboard_column_name = [];

    protected $copy_and_add_to_filter_column_name = [];
    protected $copy_and_add_to_filter_column = [];

    protected $process_bulk_kd_index = -1;
    protected $process_bulk_kd_column = [];
    protected $process_bulk_kd_column_name = [];
    protected $process_bulk_kd_button_text = [];

    protected $text_selector_filter = false;

    protected $table_info = false;

    function __construct(DataTableFilterInterface $datatable)
    {

        /* DatatableFilter Object / Datatable Object */
        $this->datatable = $datatable;

        $this->check_box_column_name = $this->datatable->check_box_column_name;
        $this->check_box_column_index = $this->datatable->check_box_column_index;
        $this->checkbox_controls_html_string = '';

        $this->language_path = 'app/manager';

        $this->page_length = 10;

//        $this->keyword_manager_column_type = $this->setType('page');
//        $this->page_manager_column_type = $this->setType('keyword');
//        $this->tag_manager_column_type = $this->setType('keyword');
    }

    /**
     * @param $columns
     * @return $this
     */
    public function setDataColumnsInJs($columns)
    {
        foreach ($columns as $column) {
            $this->addDataColumn($this->datatable->extractAliasName($column));
        }
        return $this;
    }

    /**
     * @param $notSearchableColumnsIndex
     * @return $this
     */
    public function setNotSearchableColumnsIndexInJs($notSearchableColumnsIndex)
    {
        $this->disableSearchableColumnName();
        $this->setNotSearchableColumnsInJs($notSearchableColumnsIndex);
        return $this;
    }

    /**
     * @param $notSearchableColumnsName
     * @return $this
     */
    public function setNotSearchableColumnsNameInJs($notSearchableColumnsName)
    {
        $this->enableSearchableColumnName();
        $this->setNotSearchableColumnsInJs($notSearchableColumnsName);
        return $this;
    }

    /**
     * @param $notVisibleColumnsIndex
     * @return $this
     */
    public function setNotVisibleColumnsIndexInJs($notVisibleColumnsIndex)
    {
        $this->disableVisibleColumnName();
        $this->setNotVisibleColumnsInJs($notVisibleColumnsIndex);
        return $this;
    }

    /**
     * @param $notVisibleColumnsName
     * @return $this
     */
    public function setNotVisibleColumnsNameInJs($notVisibleColumnsName)
    {
        $this->enableVisibleColumnName();
        $this->setNotVisibleColumnsInJs($notVisibleColumnsName);
        return $this;
    }

    /**
     * @param $columnsIndexWithClassNameAsKey
     * @return $this
     */
    public function setClassesForColumnsIndexInJs($columnsIndexWithClassNameAsKey)
    {
        $this->disableColumnNameForClasses();
        $this->setClassesForColumnsInJs($columnsIndexWithClassNameAsKey);
        return $this;
    }

    /**
     * @param $columnsNamesWithClassNameAsKey
     * @return $this
     */
    public function setClassesForColumnsNameInJs($columnsNamesWithClassNameAsKey)
    {
        $this->enableColumnNameForClasses();
        $this->setClassesForColumnsInJs($columnsNamesWithClassNameAsKey);
        return $this;
    }

    /**
     * @param $notSortableColumnsIndex
     * @return $this
     */
    public function setNotSortableColumnsIndexInJs($notSortableColumnsIndex)
    {
        $this->disableSortableColumnName();
        $this->setNotSortableColumnsInJs($notSortableColumnsIndex);
        return $this;
    }

    /**
     * @param $notSortableColumnsName
     * @return $this
     */
    public function setNotSortableColumnsNameInJs($notSortableColumnsName)
    {
        $this->enableSortableColumnName();
        $this->setNotSortableColumnsInJs($notSortableColumnsName);
        return $this;
    }

    /**
     * @param array $columnsIndexWithSortingOrder
     * @return $this
     */
    public function setSortingOrderColumnsIndexInJs(array $columnsIndexWithSortingOrder)
    {
        $this->disableSortingOrderColumnName();
        $this->setSortingOrderColumnsInJs($columnsIndexWithSortingOrder);
        return $this;
    }

    /**
     * @param array $columnsNameWithSortingOrder
     * @return $this
     */
    public function setSortingOrderColumnsNameInJs(array $columnsNameWithSortingOrder)
    {
        $this->enableSortingOrderColumnName();
        $this->setSortingOrderColumnsInJs($columnsNameWithSortingOrder);
        return $this;
    }

    /**
     * @param $checkboxColumnsIndex
     * @return $this
     * @internal param $notSortableColumnsIndex
     */
    public function setCheckboxColumnsIndexInJs($checkboxColumnsIndex)
    {
        $this->disableCheckboxColumnName();
        $this->setCheckboxColumnsInJs($checkboxColumnsIndex);
        return $this;
    }

    /**
     * @param $checkboxColumnsName
     * @return $this
     */
    public function setCheckboxColumnsNameInJs($checkboxColumnsName)
    {
        $this->enableCheckboxColumnName();
        $this->setCheckboxColumnsInJs($checkboxColumnsName);
        return $this;
    }

    /**
     * @param $table_id
     * @return mixed
     */
    public function datatableJsOutput($table_id)
    {
        if ($this->isEnabledDrawCheckbox()) {
            $this->setConditionForCheckboxColumn();
        }
        $this->prepareJsColumns();

        $this->prepareManagers($table_id);
        $this->prepareCopyToClipboard($table_id);
        $this->prepareCopyToFilter($table_id);
        $this->prepareBulkKeywordDifficulty($table_id);


        if ($this->isEnabledDrawCheckbox() && empty($this->jsOrder)) {
            $this->addJsOrder([1, 'asc']);
        }

        $hide_default_columns = $this->getDefaultHiddenColumnsStatus();

        $keyword_manager_column = count($this->keyword_manager_column);
        $tag_manager_column = count($this->tag_manager_column);
        $page_manager_column = count($this->page_manager_column);
        $enable_kd = count($this->process_bulk_kd_column);

        if(\Request::ajax()){
            $ajax_request = "is_ajax";
        } else
        {
            $ajax_request = "not_ajax";
        }

        if(\Request::input('pdf_view')=="yes"){
            $pdf_view="yes";
            if (\Request::input('data_rows') != "") {
                $data_rows = \Request::input('data_rows');
            } else {
                $data_rows = 1000;
            }
        } else {
            $pdf_view = "no";
            $data_rows = "na";
        }

        $js_variables = ['table_id' => $table_id, 'ajax_url' => $this->getAjaxUrl(), 'page_length' => $this->getPageLength(), 'js_columns' => json_encode($this->jsColumns), 'js_order' => json_encode($this->jsOrder), 'checkbox_columns' => json_encode($this->checkbox_columns), 'checkbox_control_text' => $this->checkbox_controls_html_string, 'datatable_js_objects' => $this->datatable_js_objects, 'datatable_fixed_columns_objects' => $this->datatable_fixed_columns_objects, 'ajax_callback_function' => $this->ajax_callback_function, 'ajax_success_callback_function' => $this->ajax_success_callback_function, 'pre_draw_callback_function' => $this->pre_draw_callback_function, 'keyword_manager_column' => ($keyword_manager_column > 0) ? $keyword_manager_column : -1, 'tag_manager_column' => ($tag_manager_column > 0) ? $tag_manager_column : -1, 'page_manager_column' => ($page_manager_column > 0) ? $page_manager_column : -1, 'manager_url' => config('mondovo-datatable.get_all_manager'),'ajax_request'=>$ajax_request,'hide_default_columns'=>$hide_default_columns,'pdf_view'=>$pdf_view,'data_rows'=>$data_rows, 'custom_empty_table_message' => $this->getCustomEmptyTableMessage(), 'auto_width' => $this->auto_width, 'loading_on_scroll' => $this->loading_on_scroll, 'scroll_y' => $this->scroll_y, 'enable_kd' => ($enable_kd > 0) ? $enable_kd : -1, 'kd_modal_url' => config('mondovo-datatable.kd_modal_url'), 'text_selector_filter' => $this->text_selector_filter, 'table_info' => $this->table_info ];
        return view('mondovo.datatable.datatable-js', $js_variables);
    }

    /**
     * @param boolean $filter_status
     */
    public function enableDrawCheckbox()
    {
        $this->draw_checkbox = true;
        $this->increaseReserveColumnCount();
    }

    /**
     * @param $url
     * @return $this
     */
    public function setAjaxUrl($url)
    {
        $this->ajax_url = $url;
        return $this;
    }

    /**
     * @param $page_length
     * @return $this
     */
    public function setPageLength($page_length)
    {
        $this->page_length = $page_length;
        return $this;
    }

    /**
     * @param $message
     * @return $this
     */
    public function setCustomEmptyTableMessage($message)
    {
        $this->custom_empty_table_message = $message;
        return $this;
    }

    /**
     * @param string $html_string
     * @return $this
     */
    public function addToCheckboxControls($html_string)
    {
        $this->checkbox_controls_html_string .= '&nbsp;' . $html_string;
    }

    private function alterManagers($column, $column_index)
    {
        foreach($this->keyword_manager_column as $key => $keyword_column)
        {
            $this->keyword_manager_index = $key;
            if ($this->isEnableKeywordManagerColumnName() && $keyword_column == $column) {
                $this->keyword_manager_column[$key] = $column_index;
            }
        }

        foreach($this->page_manager_column as $key => $page_column)
        {
            $this->page_manager_index = $key;
            if ($this->isEnablePageManagerColumnName() && $page_column == $column) {
                $this->page_manager_column[$key] = $column_index;
            }
        }

        foreach($this->tag_manager_column as $key=>$tag_column)
        {
            $this->tag_manager_index = $key;
            if ($this->isEnableTagManagerColumnName() && $tag_column == $column) {
                $this->tag_manager_column[$key] = $column_index;
            }
        }
    }

    private function alterCopyToClipboardColumns($column, $column_index)
    {
        foreach($this->copy_to_clipboard_column as $key => $copy_column)
        {
            $this->copy_to_clipboard_index = $key;
            if ($this->isEnableCopyToClipboardColumnName() && $copy_column == $column) {
                $this->copy_to_clipboard_column[$key] = $column_index;
            }
        }
    }

    private function alterCopyToFilterColumns($column, $column_index)
    {
        foreach($this->copy_and_add_to_filter_column as $key => $copy_column)
        {
            $this->copy_and_add_to_filter_index = $key;
            if ($this->isEnableCopyToFilterColumnName() && $copy_column == $column) {
                $this->copy_and_add_to_filter_column[$key] = $column_index;
            }
        }
    }

    private function alterBulkKDProcessColumns($column, $column_index)
    {
        foreach($this->process_bulk_kd_column as $key => $kd_column)
        {
            $this->process_bulk_kd_index = $key;
            if ($this->isEnableBulkKDProcessColumnName() && $kd_column == $column) {
                $this->process_bulk_kd_column[$key] = $column_index;
            }
        }
    }

    private function prepareManagers($table_id)
    {

        foreach($this->tag_manager_column as $key=>$tag_column) {
                $html_string = ' <a class="icon-btn btn blue add-to-tag-manager" data-toggle="modal" href="#" data-table-id="' . $table_id . '" data-column-index="' . $tag_column . '" data-column-type="' . $this->tag_manager_column_type[$key] . '" ><i class="fa fa-plus"></i> <div>' . trans($this->language_path . '.assign') . ' ' . trans($this->language_path . '.tags_info') . ' ' . $this->tag_manager_button_text[$key] . '</div></a>';
                $this->addToCheckboxControls($html_string);
        }

        foreach ($this->keyword_manager_column as $key => $keyword_column) {
            $html_string = ' <a class="icon-btn btn blue add-to-keyword-manager" data-toggle="modal" href="#" data-table-id="' . $table_id . '" data-column-index="' . $keyword_column . '" data-column-type="' . $this->keyword_manager_column_type[$key] . '" ><i class="fa fa-plus"></i> <div>' . trans($this->language_path . '.add_to') . ' ' . $this->keyword_manager_button_text[$key] . '<br>' . trans($this->language_path . '.keyword_manager') . '</div></a>';

            $this->addToCheckboxControls($html_string);
        }

        foreach ($this->page_manager_column as $key => $page_column) {
            $html_string = ' <a class="icon-btn btn blue add-to-page-manager" data-toggle="modal" href="#" data-table-id="' . $table_id . '" data-column-index="' . $page_column . '" data-column-type="' . $this->page_manager_column_type[$key] . '" ><i class="fa fa-plus"></i> <div>' . trans($this->language_path . '.add_to') . ' ' . $this->page_manager_button_text[$key] . '<br>' . trans($this->language_path . '.page_manager') . '</div></a>';

            $this->addToCheckboxControls($html_string);
        }
    }

    private function prepareBulkKeywordDifficulty($table_id){
        foreach($this->process_bulk_kd_column as $key => $kd_column) {
            $html_string = ' <a class="icon-btn btn blue bulk-kd-process" data-toggle="modal" href="#" data-table-id="' . $table_id . '" keyword-id-column-index="' . $kd_column['keyword_id'] . '" location-id-column-index="' . $kd_column['location_id'] . '" difficulty-column-index="' . $kd_column['difficulty'] . '"><i class="fa fa-copy"></i> <div style="width:125px; height:28px; white-space: pre-line;"><i class="copy-loading fa fa-spinner fa-spin" style="font-size: 14px; color: white !important; opacity: 1; margin-top: 14px; margin-left: 2px; display: none;"></i>' . $this->process_bulk_kd_button_text[$key] . '</div></a>';
            $this->addToCheckboxControls($html_string);
        }
    }


    private function prepareCopyToClipboard($table_id)
    {
        foreach($this->copy_to_clipboard_column as $key => $copy_column) {

            $callback = isset($this->copy_to_clipboard_column_callback[$key]) ? 'callback="'. $this->copy_to_clipboard_column_callback[$key] .'"' : '';

                $html_string = ' <a class="icon-btn btn blue copy-to-clipboard" ' . $callback . ' data-toggle="modal" href="#" data-table-id="' . $table_id . '" data-column-index="' . $copy_column . '"><i class="fa fa-copy"></i> <div style="width:112px; height:28px; white-space: pre-line;"><i class="copy-loading fa fa-spinner fa-spin" style="font-size: 14px; color: white !important; opacity: 1; margin-top: 14px; margin-left: 2px; display: none;"></i>' . $this->copy_to_clipboard_button_text[$key] . '</div></a>';
                $this->addToCheckboxControls($html_string);
        }
    }

    private function prepareCopyToFilter($table_id)
    {
        foreach($this->copy_and_add_to_filter_column as $key => $copy_column) {

            $callback = isset($this->copy_and_add_to_filter_column_callback[$key]) ? 'callback="'. $this->copy_and_add_to_filter_column_callback[$key] .'"' : '';

                $html_string = ' <a class="icon-btn btn blue copy-to-filter" ' . $callback . ' data-dismiss="modal" href="#" data-table-id="' . $table_id . '" data-column-index="' . $copy_column . '"><i class="fa fa-copy"></i> <div style="width:110px; height:28px; white-space: pre-line;"><i class="copy-loading fa fa-spinner fa-spin" style="font-size: 14px; color: white !important; opacity: 1; margin-top: 14px; margin-left: 2px; display: none;"></i>' . $this->copy_and_add_to_filter_button_text[$key] . '</div></a>';
                $this->addToCheckboxControls($html_string);
        }
    }

    /**
     * @param $button_text
     * @param string $column_name_or_index
     * @param string $type
     * @return $this
     */
    public function addToKeywordManager($column_name_or_index, $type, $button_text='')
    {
        $this->keyword_manager_index++;
        $this->keyword_manager_column[ $this->keyword_manager_index ] = $column_name_or_index;
        $this->keyword_manager_column_type[ $this->keyword_manager_index ] = $this->setType($type);
        $this->keyword_manager_button_text[ $this->keyword_manager_index ] = $button_text;
        if(empty($button_text)){
            $this->keyword_manager_button_text[ $this->keyword_manager_index ] = $this->setButtonText($type);
        }
        return $this;
    }

    /**
     * @param $button_text
     * @param string $column_name_or_index
     * @param string $type
     * @return $this
     */
    public function addToPageManager($column_name_or_index, $type, $button_text='')
    {
        $this->page_manager_index++;
        $this->page_manager_column[ $this->page_manager_index ] = $column_name_or_index;
        $this->page_manager_column_type[ $this->page_manager_index ] = $this->setType($type);
        $this->page_manager_button_text[ $this->page_manager_index ] = $button_text;
        if(empty($button_text))
        {
            $this->page_manager_button_text[ $this->page_manager_index ] = $this->setButtonText($type);
        }
        return $this;
    }

    /**
     * @param $button_text
     * @param string $column_name_or_index
     * @param string $type
     * @return $this
     */
    public function addToTagManager($column_name_or_index, $type, $button_text='')
    {
        $this->tag_manager_index++;
        $this->tag_manager_column[ $this->tag_manager_index ] = $column_name_or_index;
        $this->tag_manager_column_type[ $this->tag_manager_index ] = $this->setType($type);
        $this->tag_manager_button_text[ $this->tag_manager_index ] = $button_text;
        if(empty($button_text))
        {
            $this->tag_manager_button_text[ $this->tag_manager_index ] = $this->setButtonText($type);
        }
        return $this;
    }

    public function processBulkKeywordDifficulty($column_name_or_index, $button_text='')
    {
        $this->process_bulk_kd_index++;
        $this->process_bulk_kd_column[$this->process_bulk_kd_index] = $column_name_or_index;
        $this->process_bulk_kd_button_text[$this->process_bulk_kd_index] = $button_text;
    }

    public function copyToClipboard($column_name, $button_text, $callback_fn = '')
    {
        $this->copy_to_clipboard_index++;
        $this->copy_to_clipboard_column[$this->copy_to_clipboard_index] = $column_name;
        $this->copy_to_clipboard_column_callback[$this->copy_to_clipboard_index] = $callback_fn;
        $this->copy_to_clipboard_button_text[$this->copy_to_clipboard_index] = $button_text;
    }

    public function copyAndAddToFilterByColumnName($column_name, $button_text, $callback_fn)
    {
        $this->copy_and_add_to_filter_index++;
        $this->copy_and_add_to_filter_column[$this->copy_and_add_to_filter_index] = $column_name;
        $this->copy_and_add_to_filter_column_callback[$this->copy_and_add_to_filter_index] = $callback_fn;
        $this->copy_and_add_to_filter_button_text[$this->copy_and_add_to_filter_index] = $button_text;
    }

    protected function setButtonText($type)
    {
        switch ($type) {
            case 'tag_id':
                return 'Tags';
                break;
            case 'tag_campaign_id':
                return 'Tags';
                break;
            case 'keyword_id':
                return 'Keywords';
                break;
            case 'page_id':
                return 'Pages';
                break;
            case 'tag':
                return 'Tags';
                break;
            case 'keyword':
                return 'Keywords';
                break;
            case 'page':
                return 'Pages';
                break;
            default:
                return false;
        }
    }

    /**
     * @param $type
     * @return bool|string
     */
    protected function setType($type)
    {
        switch ($type) {
            case 'tag_id':
                return 'tag_ids';
                break;
            case 'tag_campaign_id':
                return 'tag_campaign_ids';
                break;
            case 'keyword_id':
                return 'keyword_ids';
                break;
            case 'page_id':
                return 'page_ids';
                break;
            case 'tag':
                return 'tags';
                break;
            case 'keyword':
                return 'keywords';
                break;
            case 'page':
                return 'pages';
                break;
            default:
                return false;
        }
    }

    /**
     * @param $js_objects
     */
    public function addOptionsToDataTables($js_objects)
    {
        $this->datatable_js_objects = $js_objects;
    }

    /**
     * @param array $fixed_params
     * @return $this
     */
    public function setFixedColumns(array $fixed_params)
    {
        $this->datatable_fixed_columns_objects = $fixed_params;
    }

    /**
     * @return array
     */
    public function getDataColumns()
    {
        return $this->dataColumns;
    }

    /**
     * @param $ajax_callback_function
     * @return $this
     */
    public function setCallbackBeforeSendingToAjax($ajax_callback_function)
    {
        $this->ajax_callback_function = $ajax_callback_function;
        return $this;
    }

    /**
     * @param $ajax_success_callback_function
     * @return $this
     */
    public function setCallbackAfterAjaxSuccess($ajax_success_callback_function)
    {
        $this->ajax_success_callback_function = $ajax_success_callback_function;
        return $this;
    }

    /**
     * @param $pre_draw_callback_function
     * @return $this
     */
    public function setPreDrawCallback($pre_draw_callback_function)
    {
        $this->pre_draw_callback_function = $pre_draw_callback_function;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnabledDrawCheckbox()
    {
        return $this->draw_checkbox;
    }

    protected function increaseReserveColumnCount()
    {
        $this->reserve_column_count++;
    }

    protected function enableSearchableColumnName()
    {
        $this->searchableColumnName = true;
    }

    protected function disableSearchableColumnName()
    {
        $this->searchableColumnName = false;
    }

    protected function isEnableSearchableColumnName()
    {
        return $this->searchableColumnName;
    }

    protected function enableVisibleColumnName()
    {
        $this->visibleColumnName = true;
    }

    protected function disableVisibleColumnName()
    {
        $this->visibleColumnName = false;
    }

    protected function isEnableVisibleColumnName()
    {
        return $this->visibleColumnName;
    }

    protected function enableColumnNameForClasses()
    {
        $this->columnNameForClasses = true;
    }

    protected function disableColumnNameForClasses()
    {
        $this->columnNameForClasses = false;
    }

    protected function isEnableColumnNameForClasses()
    {
        return $this->columnNameForClasses;
    }

    protected function enableSortingOrderColumnName()
    {
        $this->sortingOrderColumnName = true;
    }

    protected function disableSortingOrderColumnName()
    {
        $this->sortingOrderColumnName = false;
    }

    protected function isEnableSortingOrderColumnName()
    {
        return $this->sortingOrderColumnName;
    }

    protected function enableSortableColumnName()
    {
        $this->sortableColumnName = true;
    }

    protected function disableSortableColumnName()
    {
        $this->sortableColumnName = false;
    }

    protected function isEnableSortableColumnName()
    {
        return $this->sortableColumnName;
    }

    protected function enableCheckboxColumnName()
    {
        $this->checkbox_column_name = true;
    }

    protected function disableCheckboxColumnName()
    {
        $this->checkbox_column_name = false;
    }

    public function enableKeywordManagerColumnName()
    {
        $this->keyword_manager_column_name[$this->keyword_manager_index] = true;
    }

    public function enablePageManagerColumnName()
    {
        $this->page_manager_column_name[$this->page_manager_index] = true;
    }

    public function enableTagManagerColumnName()
    {
        $this->tag_manager_column_name[$this->tag_manager_index] = true;
    }

    public function enableCopyToClipboardColumnName()
    {
        $this->copy_to_clipboard_column_name[$this->copy_to_clipboard_index] = true;
    }

    public function enableCopyAndAddToFilterByColumnName()
    {
        $this->copy_and_add_to_filter_column_name[$this->copy_and_add_to_filter_index] = true;
    }

    public function enableBulkKDColumnName()
    {
        $this->process_bulk_kd_column_name[$this->process_bulk_kd_index] = true;
    }

    protected function isEnableCheckboxColumnName()
    {
        return $this->checkbox_column_name;
    }

    protected function isEnableKeywordManagerColumnName()
    {
        return $this->keyword_manager_column_name[$this->keyword_manager_index];
    }

    protected function isEnablePageManagerColumnName()
    {
        return $this->page_manager_column_name[$this->page_manager_index];
    }

    protected function isEnableTagManagerColumnName()
    {
        return $this->tag_manager_column_name[$this->tag_manager_index];
    }

    protected function isEnableCopyToClipboardColumnName()
    {
        return $this->copy_to_clipboard_column_name[$this->copy_to_clipboard_index];
    }

    protected function isEnableCopyToFilterColumnName()
    {
        return $this->copy_and_add_to_filter_column_name[$this->copy_and_add_to_filter_index];
    }

    protected function isEnableBulkKDProcessColumnName()
    {
        return $this->process_bulk_kd_column_name[$this->process_bulk_kd_index];
    }

    /**
     * @return mixed
     */
    protected function getAjaxUrl()
    {
        return $this->ajax_url;
    }

    /**
     * @return mixed
     */
    protected function getPageLength()
    {
        return $this->page_length;
    }

    protected function getCustomEmptyTableMessage()
    {
        return $this->custom_empty_table_message;
    }

    /**
     * @return mixed
     */
    protected function isExistAjaxUrl()
    {
        return !empty($this->ajax_url);
    }

    /**
     * @param $column
     */
    protected function addDataColumn($column)
    {
        $this->dataColumns[] = $column;
    }

    /**
     * @param $column
     */
    protected function addToNotSearchableColumns($column)
    {
        $this->notSearchableColumns[] = $column;
    }

    /**
     * @param $column
     */
    protected function addToNotVisibleColumns($column)
    {
        $this->notVisibleColumns[] = $column;
    }

    /**
     * @param $classNames
     * @param array $columns
     */
    protected function addToClassesForColumns($classNames, array $columns)
    {
        $this->columnsForClasses[$classNames] = $columns;
    }

    /**
     * @param $column
     */
    protected function addToNotSortableColumns($column)
    {
        $this->notSortableColumns[] = $column;
    }

    /**
     * @param $column
     * @param $order
     */
    protected function addToSortingOrderColumns($column, $order)
    {
        $this->sortingOrderColumns[] = [$column, $order];
    }

    /**
     * @param $column
     */
    protected function addToCheckboxColumns($column)
    {
        $this->checkbox_columns[] = $column;
    }

    /**
     * @param $column
     */
    protected function setCheckboxColumnsInIndex($column, $column_index)
    {
        $this->checkbox_columns[$column_index] = $column;
    }

    /**
     * @param array $column
     */
    protected function addJsColumns(array $column)
    {
        $this->jsColumns[] = $column;
    }

    /**
     * @param array $column
     */
    protected function addJsOrder(array $column)
    {
        $this->jsOrder[] = $column;
    }

    /**
     *  prepare all columns that should be passed in js columnDef
     */
    private function prepareJsColumns()
    {
        $column_index = 0;
        foreach ($this->dataColumns as $column) {
            $prepareProperties = [];
            if ($this->isExistAjaxUrl()) {
                $prepareProperties['data'] = $column;
            }

            $this->alterManagers($column, $column_index);

            $this->alterCopyToClipboardColumns($column, $column_index);
            $this->alterCopyToFilterColumns($column, $column_index);

            $this->alterBulkKDProcessColumns($column, $column_index);

            $prepareProperties['searchable'] = $this->isSearchable($column, $column_index);

            $prepareProperties['className'] = $this->isVisible($column, $column_index);
            $className = $this->getClassForColumns($column, $column_index);
            if (!empty($className)) {
                $prepareProperties['className'] .= ' ' . $className;
            }

            if ($this->isEnabledDrawCheckbox() && $column_index == 0) {
                $prepareProperties['className'] = $this->checkbox_td_class;
            }

            $prepareProperties['sortable'] = $this->isSortable($column, $column_index);

            $order = $this->isSortingOrder($column, $column_index);
            if (!is_bool($order)) {
                $this->addJsOrder($order);
            }

            if (($pos = $this->isCheckBoxColumns($column, $column_index)) !== false && $this->isExistAjaxUrl()) {
                $this->setCheckboxColumnsInIndex($column, $pos);
            }
            $this->addJsColumns($prepareProperties);
            $column_index++;
        }

        if (!$this->isExistAjaxUrl()) {
            $this->adjustCheckBoxIndexForNonAjax();
        }
    }

    private function adjustCheckBoxIndexForNonAjax()
    {
        $column_index = 0;
        foreach ($this->checkbox_columns as $column) {
            $this->checkbox_columns[$column_index] = $column;
        }
    }

    private function isExistInCheckBoxColumns($column)
    {
        return array_search($column, $this->checkbox_columns);
    }

    private function isCheckBoxColumns($column, $column_index)
    {
        if ($this->isEnableCheckboxColumnName()) {
            return $this->isExistInCheckBoxColumns($column);
        }
        return $this->isExistInCheckBoxColumns($column_index);
    }

    private function isExistInSortingOrderColumns($column, $column_index)
    {
        foreach ($this->sortingOrderColumns as $columns) {
            if ($columns[0] == $column) {
                return [$column_index, $columns[1]];
            }
        }
        return false;
    }

    private function isSortingOrder($column, $column_index)
    {
        if ($this->isEnableSortingOrderColumnName()) {
            return $this->isExistInSortingOrderColumns($column, $column_index);
        }

        return $this->isExistInSortingOrderColumns($column, $column_index);
    }

    private function isNotExistInNotSearchableColumns($column)
    {
        return !in_array($column, $this->notSearchableColumns);
    }

    private function isSearchable($column, $column_index)
    {
        if ($this->isEnableSearchableColumnName()) {
            return $this->isNotExistInNotSearchableColumns($column);
        }

        return $this->isNotExistInNotSearchableColumns($column_index);
    }

    private function isNotExistInNotVisibleColumns($column)
    {
        return in_array($column, $this->notVisibleColumns) ? $this->hidden_column_class_name : '';
    }

    private function isVisible($column, $column_index)
    {
        if ($this->isEnableVisibleColumnName()) {
            return $this->isNotExistInNotVisibleColumns($column);
        }

        return $this->isNotExistInNotVisibleColumns($column_index);
    }

    private function isExistClassesForColumns($column)
    {
        $finalClassNames = '';
        foreach ($this->columnsForClasses as $className => $columnNamesOrIndexes) {
            $tempClassName = in_array($column, $columnNamesOrIndexes) ? $className : '';
            if (!empty($tempClassName)) {
                $finalClassNames .= ' ' . $tempClassName;
            }
        }
        return trim($finalClassNames);
    }

    private function getClassForColumns($column, $column_index)
    {
        if ($this->isEnableColumnNameForClasses()) {
            return $this->isExistClassesForColumns($column);
        }

        return $this->isExistClassesForColumns($column_index);
    }

    private function isNotExistInNotSortableColumns($column)
    {
        return !in_array($column, $this->notSortableColumns);
    }

    private function isSortable($column, $column_index)
    {
        if ($this->isEnableSortableColumnName()) {
            return $this->isNotExistInNotSortableColumns($column);
        }

        return $this->isNotExistInNotSortableColumns($column_index);
    }

    /**
     * @param $notSearchableColumns
     */
    private function setNotSearchableColumnsInJs($notSearchableColumns)
    {
        $notSearchableColumns = $this->datatable->stringToArray($notSearchableColumns);

        foreach ($notSearchableColumns as $column) {
            $this->addToNotSearchableColumns($column);
        }
    }

    /**
     * @param $notVisibleColumns
     */
    private function setNotVisibleColumnsInJs($notVisibleColumns)
    {
        $notVisibleColumns = $this->datatable->stringToArray($notVisibleColumns);

        foreach ($notVisibleColumns as $column) {
            $this->addToNotVisibleColumns($column);
        }
    }

    /**
     * @param $columnsNamesWithClassNameAsKey
     */
    private function setClassesForColumnsInJs($columnsNamesWithClassNameAsKey)
    {
        foreach ($columnsNamesWithClassNameAsKey as $className => $columns) {
            $this->addToClassesForColumns($className, $columns);
        }
    }

    /**
     * @param $notSortableColumns
     */
    private function setNotSortableColumnsInJs($notSortableColumns)
    {
        $notSortableColumns = $this->datatable->stringToArray($notSortableColumns);

        foreach ($notSortableColumns as $column) {
            $this->addToNotSortableColumns($column);
        }
    }

    /**
     * @param array $columnsWithSortingOrder
     * @example: ['1'=>'asc', '2'=>'desc']
     */
    private function setSortingOrderColumnsInJs(array $columnsWithSortingOrder)
    {
        foreach ($columnsWithSortingOrder as $column => $order) {
            $this->addToSortingOrderColumns($column, $order);
        }
    }

    /**
     * @param $checkboxColumns
     * @internal param $columns
     */
    private function setCheckboxColumnsInJs($checkboxColumns)
    {
        $checkboxColumns = $this->datatable->stringToArray($checkboxColumns);

        foreach ($checkboxColumns as $column) {
            $this->addToCheckboxColumns($column);
        }
    }

    /**
     * @param $array
     * @param $increase_from
     * @return array
     */
    private function increaseByOne($array, $increase_from)
    {
        $final_array = [];
        foreach ($array as $value) {
            $numeric = intval($value);
            if ($numeric < $increase_from) {
                $final_array[] = $numeric;
            } else {
                $final_array[] = $numeric + 1;
            }
        }
        return $final_array;
    }

    private function increaseByOneForSingle($single_column_index, $increase_from)
    {
        $numeric = intval($single_column_index);
        if ($numeric < $increase_from) {
            return $numeric;
        } else {
            return $numeric + 1;
        }
    }

    private function setConditionForCheckboxColumn()
    {
        /* add data column name */
        array_unshift($this->dataColumns, $this->check_box_column_name);

        /* Not Searchable */
        if ($this->isEnableSearchableColumnName()) {
            $column_name_or_index = $this->check_box_column_name;
        } else {
            $column_name_or_index = intval($this->check_box_column_index);
            $this->notSearchableColumns = $this->increaseByOne($this->notSearchableColumns, $column_name_or_index);
        }
        array_unshift($this->notSearchableColumns, $column_name_or_index);

        /* Not Sortable */
        if ($this->isEnableSortableColumnName()) {
            $column_name_or_index = $this->check_box_column_name;
        } else {
            $column_name_or_index = intval($this->check_box_column_index);
            $this->notSortableColumns = $this->increaseByOne($this->notSortableColumns, $column_name_or_index);
        }
        array_unshift($this->notSortableColumns, $column_name_or_index);

        /* Not Visible */
        if ($this->isEnableVisibleColumnName()) {
            $column_name_or_index = $this->check_box_column_name;
        } else {
            $column_name_or_index = intval($this->check_box_column_index);
            $this->notVisibleColumns = $this->increaseByOne($this->notVisibleColumns, $column_name_or_index);
        }
        array_unshift($this->notVisibleColumns, $column_name_or_index);

        /* Managers */
        foreach($this->keyword_manager_column as $key => $keyword_manager_column)
        {
            $this->keyword_manager_index = $key;
            if (!$this->isEnableKeywordManagerColumnName()) {
                $column_name_or_index = intval($this->check_box_column_index);
                $this->keyword_manager_column[$key] = $this->increaseByOneForSingle($keyword_manager_column, $column_name_or_index);
            }
        }

        foreach($this->page_manager_column_name as $key => $page_manager_column)
        {
            $this->page_manager_index = $key;
            if (!$this->isEnablePageManagerColumnName()) {
                $column_name_or_index = intval($this->check_box_column_index);
                $this->page_manager_column[$key] = $this->increaseByOneForSingle($page_manager_column, $column_name_or_index);
            }
        }

        foreach($this->tag_manager_column as $key=>$tag_manager_column)
        {
            $this->tag_manager_index = $key;
            if (!$this->isEnableTagManagerColumnName()) {
                $column_name_or_index = intval($this->check_box_column_index);
                $this->tag_manager_column[ $key ] = $this->increaseByOneForSingle($tag_manager_column, $column_name_or_index);
            }
        }

        foreach($this->copy_to_clipboard_column as $key => $copy_to_clipboard_column)
        {
            $this->copy_to_clipboard_index = $key;
            if (!$this->isEnableCopyToClipboardColumnName()) {
                $column_name_or_index = intval($this->check_box_column_index);
                $this->copy_to_clipboard_column[$key] = $this->increaseByOneForSingle($copy_to_clipboard_column, $column_name_or_index);
            }
        }

        foreach($this->copy_and_add_to_filter_column as $key => $copy_to_filter_column)
        {
            $this->copy_and_add_to_filter_index = $key;
            if (!$this->isEnableCopyToFilterColumnName()) {
                $column_name_or_index = intval($this->check_box_column_index);
                $this->copy_and_add_to_filter_column[$key] = $this->increaseByOneForSingle($copy_to_filter_column, $column_name_or_index);
            }
        }

        foreach($this->process_bulk_kd_column as $key => $bulk_kd_column)
        {
            $this->process_bulk_kd_index = $key;
            if (!$this->isEnableBulkKDProcessColumnName()) {
                $column_name_or_index = intval($this->process_bulk_kd_index);
                $this->process_bulk_kd_column[$key]['keyword_id'] = $this->increaseByOneForSingle($bulk_kd_column['keyword_id'], $column_name_or_index);
                $this->process_bulk_kd_column[$key]['location_id'] = $this->increaseByOneForSingle($bulk_kd_column['location_id'], $column_name_or_index);
                $this->process_bulk_kd_column[$key]['difficulty'] = $this->increaseByOneForSingle($bulk_kd_column['difficulty'], $column_name_or_index);
            }
        }

        /* Checkbox Columns */
        if (!$this->isEnableCheckboxColumnName()) {
            $column_name_or_index = intval($this->check_box_column_index);
            $this->checkbox_columns = $this->increaseByOne($this->checkbox_columns, $column_name_or_index);
        }

        /* Add Class to Columns */
        if (!$this->isEnableColumnNameForClasses()) {
            $column_name_or_index = $this->check_box_column_index;
            foreach ($this->columnsForClasses as $key => $value) {
                $this->columnsForClasses[$key] = $this->increaseByOne($value, $column_name_or_index);
            }
        }
    }

    public function hideDefaultHiddenColumns()
    {
        $this->setDefaultHiddenColumnsStatus(true);
    }

    /**
     * @return boolean
     */
    public function getDefaultHiddenColumnsStatus()
    {
        return $this->hide_default_hidden_columns;
    }

    /**
     * @param boolean $hide_default_hidden_columns
     */
    public function setDefaultHiddenColumnsStatus($hide_default_hidden_columns)
    {
        $this->hide_default_hidden_columns = $hide_default_hidden_columns;
    }

    /**
     * @param boolean $auto_width
     */
    public function setAutoWidth($auto_width)
    {
        $this->auto_width = $auto_width;
    }

    public function enableLoadingOnScroll()
    {
        $this->loading_on_scroll = 'yes';

        return $this;
    }

    public function disableLoadingOnScroll()
    {
        $this->loading_on_scroll = 'no';

        return $this;
    }

    /**
     * @param integer $scroll_y
     * @return $this
     */
    public function setScrollY($scroll_y)
    {
        $this->scroll_y = $scroll_y;

        return $this;
    }

    public function enableTextSelectorFilter($column_index)
    {
        $this->text_selector_filter = $column_index;
        return $this;
    }

    public function setTableInfo($description)
    {
        $this->table_info = $description;
        return $this;
    }
}