<?php
/**
 * TODO:
 * 1. Enable tfoot
 * 2. Allow passsing body of table for simple tables
 * 3. Allow drawing a table dynamically with functions like MyTable::setColumns()->setClasses()->setData()->draw(); or something similar
 */

namespace Mondovo\DataTable;

use Request;
use Mondovo\DataTable\Contracts\DrawTableInterface;
use Mondovo\DataTable\Exceptions\UnsupportedTypeException;

/**
 * Class MyTable
 *
 * @package App\Helpers
 */
class DrawTable implements DrawTableInterface
{
    const ARRAY_FORMAT = 2;
    const FLAT_FORMAT = 1;
    const UNDEFINED_ARRAY_FORMAT = 0;

    /**
     * @var string
     */
    protected $default_datatable_class_names = "table table-hover table-light-blue table-bordered table-full-width dataTable no-footer";
    /**
     * @var string
     */
    protected $default_datatable_th_parent_class = "";
    /**
     * @var string
     */
    protected $default_datatable_th_class = "";
    /**
     * @var string
     */
    protected $default_datatable_td_class = "";
    /**
     * @var bool
     */
    protected $filter_status = false;
    /**
     * @var bool
     */
    protected $draw_checkbox = false;

    /**
     * @var string
     */
    protected $checkbox_col_group_value = '20px';

    /**
     * @var string
     */
    protected $checkbox_column_definitions = '<input type="checkbox" class="mv_checkbox_page_wise" />';

    /**
     * @var string
     */
    protected $filterTimeHeaderTitleWrapper = "<span class='table-header-column-title'>header_title</span>";

    protected $headerWrapper = '';

    protected $keyword_group_filter = false;

    protected $operations = false;

    /**
     * @var string
     */
    protected $checkbox_th_width = '20px';
    protected $checkbox_th_class = 'text-center nopadding';
    protected $sigle_checkbox_class = 'mv_single_checkbox';
    protected $rowspan = 1;
    protected $main_rowspan = 1;
    protected $colspan = 1;
    protected $next_column_definitions = [];
    protected $iteration_flag = 0;
    public $count_of_array_column_elements = 0;

    /**
     * @var string
     */
    //protected $tooltip_template = "<span class=\"popovers\" data-content=\"{tooltip_content}\" data-trigger=\"hover\" data-placement=\"top\" data-html=\"true\" data-original-title=\"{tooltip_heading}\"><i class=\"fa fa-info-circle customInfo\"></i></span>";

    protected $tooltip_template = "<span class=\"popovers dotted_underline\" data-content=\"{tooltip_content}\" data-trigger=\"hover\" data-placement=\"top\" data-container=\"body\" data-html=\"true\" data-original-title=\"{tooltip_heading}\">header_title</span>";

    protected $table_columns = []; //very important, since our classes can be called as a facade and so a new instance might not necessarily be created everytime, so we need to re-initialize the variables
    protected $table_column_parents = []; //very important, since our classes can be called as a facade and so a new instance might not necessarily be created everytime, so we need to re-initialize the variables
    public $column_definitions = [];
    public $column_definitions_raw_values = [];
    protected $html = "";
    protected $table_id = "";
    protected $table_classes = "";

    protected $toolbar_contents = '';
    protected $columns_data = []; /* Tbody Data in No Ajax */

    /**
     * @var bool
     */
    protected $is_a_parent_column = false;

    /**
     * @var int
     */
    protected $column_count = 0;

    /**
     * @var string
     */
    protected $default_body = "<tbody></tbody>";

    /**
     * @var
     */
    protected $current_column_name;

    protected $current_column_index = 0;

    protected $attributes_array = [];

    protected $in_col_group = false;

    protected $col_group = [];

    protected $delimiter = '|';

    protected $toolbar_visibility = true;

    protected $export_button_visibility = false;

    public $excel_column_delimiter = "^~^";

    protected $export_report_name = '';

    protected $export_report_date = '';

    protected $export_strip_columns = '';

    protected $style_attributes = ['width', 'background', 'color', 'border', 'background-color', 'bg-color', 'font-size', 'font', 'font-weight', 'min-width', 'max-width'];

    protected $dataColumns;

    protected $predefined_filters_in_toolbar = [];

    protected $tooltip_separator = '^^';

    protected $t_head_name_delimiter = '~^~';

    protected $temp_array = [];

    protected $check_box_limit = 0;

    protected $data_table_filter_settings = [
        "text" => ["=", "Not Equals", "Contains", "Does not contain", "Contains (multiple)", "Does not contain (multiple)", "Starts With", "Ends With", "Is Empty", "Is Not Empty"],
        "select" => ["=", "Not Equals"],
        "filter" => ['In Tags', 'In Keywords', 'In Pages', 'Is of', 'Not In Tags', 'Not In Keywords', 'Not In Pages'],
        "number" => ['=', "Not Equals", "&lt;", "&gt;", "&lt;=", "&gt;=", "Between", "Is Empty", "Is Not Empty"],
        "all" => ["=", "Not Equals", "Contains", "Does not contain", "&lt;", "&gt;", "&lt;=", "&gt;=", "Is Empty", "Is Not Empty"]
    ];
    protected $column_filters_html = "";
    protected $select_filter_values = [];
    protected $column_filter_heading_hierarchy_level = [];
    protected $column_filter_heading_hierarchy_delimiter = " -> ";
    protected $column_filter_heading_hierarchy_level_count = 0;
    protected $column_filter_heading_hierarchy_container = "";

    /**
     * @return boolean
     */
    public function isInColGroup()
    {
        return $this->in_col_group;
    }

    /**
     * @param boolean $in_col_group
     */
    public function setInColGroup($in_col_group)
    {
        $this->in_col_group = $in_col_group;
    }

    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
        return $this;
    }

    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     *
     */
    public function __construct()
    {

    }

    /*
     * This ensures that we can end our method chaining anywhere and the $this->html will be returned
     * */
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @return string
     */
    public function prepareCheckbox()
    {
        return $this->checkbox_column_definitions . $this->getDelimiter() . 'data-filter:off' . $this->getDelimiter() . $this->checkbox_th_width . $this->getDelimiter() . 'class:' . $this->checkbox_th_class;
    }

    /*
     * This will prepend the checkbox input tag in required arrays.
     */
    public function drawCheckbox()
    {
        if (!empty($this->column_definitions[0])) {
            array_unshift($this->column_definitions, $this->prepareCheckbox());
        } else {
            $this->column_definitions = [$this->prepareCheckbox() => []] + $this->column_definitions;
        }

        if (count($this->col_group) > 0) {
            array_unshift($this->col_group, $this->checkbox_col_group_value);
        }
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->count_of_array_column_elements = count($this->column_definitions, COUNT_RECURSIVE);

        $this->setCurrentColumnIndex(0);

        if ($this->isEnabledDrawCheckbox()) {
            $this->drawCheckbox();
        }

        if ($this->filterEnabled()) {

            //dd($this->setDataColToColumnDefinition( $this->column_definitions ));
            $this->setDataColToColumnDefinition($this->column_definitions);
            $this->generateFilterHtml($this->column_definitions);
        }

        $this->drawFilterDiv($this->getTableId());


        $this->openTable();

        $this->drawColGroup();

        $this->openThead();
        $this->openRow();

        $this->drawColumns();

        $this->closeRow();
        $this->closeThead();

        $this->drawBody();

        $this->closeTable();

        return $this->html;
    }

    /**
     * @return $this
     */
    public function openTable()
    {
        $table_classes = $this->getTableClasses();

        $this->html .= "<table id='$this->table_id' class='$table_classes' data-check-box-limit='$this->check_box_limit'>";

        return $this;
    }

    /**
     * @return string
     */
    public function getTableClasses()
    {
        return $this->default_datatable_class_names;
    }

    /**
     * @return string
     */
    public function getTableTheadCheckbox()
    {
        return $this->checkbox_column_definitions;
    }

    /**
     * @return string
     */
    public function getFilterTimeHeaderTitleWrapper()
    {
        return $this->filterTimeHeaderTitleWrapper;
    }

    /**
     * @return string
     */
    public function getCheckboxThClass()
    {
        return $this->checkbox_th_class;
    }

    /**
     * @return $this
     */
    public function openThead()
    {
        $this->html .= "<thead>";

        return $this;
    }

    /**
     * @param string $class_names
     * @return $this
     */
    public function openRow($class_names = "")
    {
        $display_class = "";

        if ($class_names != "") {
            $display_class = " class='$class_names";
        }

        $this->html .= "<tr{$display_class}>";

        return $this;
    }

    /**
     * @param string $class_names
     * @return $this
     */
    public function openColumn($class_names = "")
    {
        $display_class = "";

        if ($class_names != "") {
            $display_class = " class='$class_names";
        }

        $this->html .= "<td{$display_class}>";

        return $this;
    }

    /**
     * @param string $class_names
     * @return $this
     */
    public function openTbody($class_names = "")
    {
        $display_class = "";

        if ($class_names != "") {
            $display_class = " class='$class_names";
        }

        $this->html .= "<tbody{$display_class}>";

        return $this;
    }

    /**
     * @return $this
     */
    public function closeRow()
    {
        $this->html .= "</tr>";

        return $this;
    }

    /**
     * @return $this
     */
    public function closeColumn()
    {
        $this->html .= "</td>";

        return $this;
    }

    /**
     * @return $this
     */
    public function closeTbody()
    {
        $this->html .= "</tbody>";

        return $this;
    }

    /**
     * @return $this
     */
    public function closeThead()
    {
        $this->html .= "</thead>";

        return $this;
    }

    /**
     * @return $this
     */
    public function closeTable()
    {
        $this->html .= "</table>";

        return $this;
    }

    /**
     * @return boolean
     */
    public function filterEnabled()
    {
        return $this->filter_status;
    }

    /**
     * @return boolean
     */
    public function isEnabledDrawCheckbox()
    {
        return $this->draw_checkbox;
    }

    /**
     * @param boolean $filter_status
     */
    public function setFilterStatus($filter_status)
    {
        $this->filter_status = $filter_status;
    }

    /**
     * @param boolean $filter_status
     */
    public function setToDrawCheckbox($draw_checkbox)
    {
        $this->draw_checkbox = $draw_checkbox;
    }

    /**
     * @return $this
     */
    public function enableFilter()
    {
        $this->setFilterStatus(true);
        return $this;
    }

    /**
     * @return $this
     */
    public function enableCheckbox()
    {
        $this->setToDrawCheckbox(true);
        return $this;
    }

    /**
     * @return $this
     */
    public function disableFilter()
    {
        $this->setFilterStatus(false);
        return $this;
    }

    /**
     * @return $this
     */
    public function withFilter()
    {
        $this->enableFilter();
        return $this;
    }

    /**
     * @param string $checkbox_th_width
     * @return $this
     */
    public function withCheckbox($checkbox_th_width = '')
    {
        if (!empty($checkbox_th_width)) {
            $this->checkbox_th_width = $checkbox_th_width;
        }

        $this->enableCheckbox();
        return $this;
    }


    /**
     * @return string
     */
    public function draw()
    {
        return $this->render();
    }

    /**
     * @param $table_id
     * @param array $column_definitions
     * @param array $col_group
     * @param string $table_class_names You may over-write the classes by prefixing the beginning with an "o:"
     * @return $this
     */
    public function drawDataTable($table_id, $column_definitions = array(), $col_group = array(), $table_class_names = "")
    {
        $this->reinitializeVariables();

        $this->setTableId($table_id);

        $this->setTableClasses($table_class_names);

        $this->setColGroup($col_group);

        $this->setColumnDefinitions($column_definitions);

        return $this;
    }

    /**
     * @return $this
     */
    private function drawBody()
    {
        if ($this->isExistColumnsData()) {
            $this->fillTbody();
        } else {
            $this->html .= $this->getDefaultBody();
        }
        return $this;
    }


    /**
     * @param array $column_string
     * @return $this
     */
    private function drawColumnHeadings($column_string)
    {
        $span_text = '';
        if ($this->rowspan > 1) {
            $span_text .= " rowspan='" . $this->rowspan . "'";
        }

        if ($this->colspan > 1) {
            $span_text .= " colspan='" . $this->colspan . "'";
        }

        list($th, $column_string) = $this->prepareTag($column_string, "th$span_text");

        if (!empty($this->headerWrapper) && !$this->parentColumn()) {
            $column_string = str_replace('header_title', $column_string, $this->headerWrapper);
        }

        $this->html .= $th . $column_string . "</th>";

        return $this;
    }

    /**
     * @param $column_string
     * @return array
     * @internal param $column_tag
     */
    private function extractAttributesFromColumn($column_string)
    {
        $attributes_to_display = "";

        $column_name = $this->extractColumnNameFromColumnString($column_string);

        $this->setCurrentColumnName($column_name);

        $column_string = $this->appendDefaultFilters($column_string);

        if ($this->hasCustomAttributes($column_string)) {

            $attributes_string = $this->getAttributesString($column_string);

            $attributes_to_display = $this->formatAttributes($attributes_string);

            $column_name = $this->getCurrentColumnName();
            //debug_info("Column name: " . $column_name." and attributes to display: ".$attributes_to_display);
            return array($column_name, $attributes_to_display);
        }

        return array($column_string, $attributes_to_display);
    }

    /**
     * @param $attributes_string
     * @return string
     */
    private function formatAttributes($attributes_string)
    {
        $attribute_output = "";
        $attribute_values = "";
        $style_values = "";

        if (str_contains($attributes_string, $this->getDelimiter())) {//means multiple attributes to process

            $attribute_components = explode($this->getDelimiter(), $attributes_string);

            foreach ($attribute_components as $individual_attribute) {
                $result = $this->processIndividualAttribute($individual_attribute);
                $attribute_values .= $result[0];
                $style_values .= $result[1];
            }

            return $this->processAttributeDisplay($style_values, $attribute_values, $attribute_output);

        }

        list($attribute_values, $style_values) = $this->processIndividualAttribute($attributes_string);

        return $this->processAttributeDisplay($style_values, $attribute_values, $attribute_output);
    }

    /**
     * @param $attribute
     * @return string
     */
    private function processIndividualAttribute($attribute)
    {
        $style_attributes_string = "";
        $attribute_string = "";

        $attribute_parts = $this->explodeAndRetrieveIndividualAttributes($attribute);

        if (!is_array($attribute_parts)) {
            return ["", ""];
        }

        $attribute_key = $attribute_parts[0];
        $attribute_value = $attribute_parts[1];

        if ($attribute_key == "tooltip") {
            $this->handleToolTip($attribute_parts);
            return ["", ""];
        }
        if ($attribute_key == "class") {
            //Note by Sameer on 9th August, 2016: The below condition actually seems quite useless because classes anyway can be passed using spaces. This has been done just to maintain backward compatibility and to pass the test cases which have been written using comma. In practice, do not pass comma for multiple classes, it is not required.
            $attribute_values = str_replace(",", " ", $attribute_value);//needed for classes, e.g. class:green,red will be class='green red'
        } else {
            $attribute_values = $attribute_value;
        }


        if ($this->isStyleAttribute($attribute_key)) { //if it is a style attribute, process it differently

            $style_attributes_string = $attribute_key . ":$attribute_values;";

        } else {
            $attribute_string = " $attribute_key='$attribute_values'";
        }

        return [$attribute_string, $style_attributes_string];
    }

    /**
     * @param $style_values
     * @param $attribute_values
     * @param $attribute_output
     * @return string
     */
    private function processAttributeDisplay($style_values, $attribute_values, $attribute_output)
    {
        ($style_values != "") ? $attribute_output .= "$attribute_values style='$style_values'" : $attribute_output .= "$attribute_values";
        return $attribute_output;
    }

    /**
     * @param $column_string
     * @return array
     */
    private function prepareTag($column_string, $tag = "th")
    {
        $th = "<$tag";
        list($column_string, $attributes) = $this->extractAttributesFromColumn($column_string);
        $th .= "$attributes>";
        return array($th, $column_string);
    }

    /**
     * @param $col_group
     */
    public function drawColGroup()
    {
        $col_group = $this->col_group;

        if (count($col_group) > 0) {
            $this->setInColGroup(true);
            $this->html .= "<colgroup>";
            foreach ($col_group as $col) {
                $col_tag = "<col";
                $attributes_string = $this->formatAttributes($col);
                $col_tag .= $attributes_string . ">";
                $this->html .= $col_tag;
            }
            $this->setInColGroup(false);
            $this->html .= "</colgroup>";
        }

        return $this;
    }

    /**
     * @param $column_heading
     * @return bool
     */
    public function getHeaderWrapperForTh($column_heading)
    {
        if (!$this->filterEnabled()) {
            return false;
        }

        $pos = $this->isFilterOffForColumn($column_heading);
        if ($pos === false) {
            $this->headerWrapper = $this->filterTimeHeaderTitleWrapper;
            return true;
        }

        $this->headerWrapper = '';
    }

    /**
     * @param $column_string
     * @return bool|int
     */
    private function isFilterOffForColumn($column_string)
    {
        return strpos($column_string, "data-filter:off");
    }

    protected function resetRowSpan()
    {
        $this->rowspan = $this->main_rowspan;
    }

    protected function resetColSpan()
    {
        $this->colspan = 1;
    }

    public function drawColumns()
    {
        $column_definitions = $this->column_definitions;
        $this->resetRowSpan();
        $this->main_rowspan = array_depth($column_definitions);
        $this->setCurrentColumnIndex(0);

        $this->next_column_definitions = [];
        $this->drawTHead($column_definitions);
        return $this;
    }

    protected function drawTHead(array $column_definitions)
    {
        $this->next_column_definitions = [];
        $temp_key_suffix = 0;

        foreach ($column_definitions as $column_key => $column_heading) {

            if (strpos($column_key, $this->t_head_name_delimiter) !== false) {
                $column_key = substr($column_key, 0, strpos($column_key, $this->t_head_name_delimiter));
            }

            $this->resetColSpan();
            $this->resetRowSpan();
            $this->amOffParentColumn();

            if (is_array($column_heading)) {
                if (!empty($column_heading)) {
                    foreach ($column_heading as $key => $ch) {
                        $temp_key_suffix++;
                        if (is_array($ch)) {
                            if (!empty($ch)) {
                                $key = $key . $this->t_head_name_delimiter . $temp_key_suffix;
                                $this->next_column_definitions[$key] = $ch;
                            } else {
                                $this->next_column_definitions[] = $key;
                            }

                        } else {
                            $this->next_column_definitions[] = $ch;
                        }
                    }
                    $this->colspan = array_count_recursive($column_heading);
                    $this->rowspan = 1;
                    $this->amOnParentColumn();
                }
                $column_heading = $column_key;
            }

            $this->rowspan -= $this->iteration_flag;
            $this->getHeaderWrapperForTh($column_heading);
            $this->drawColumnHeadings($column_heading);
        }

        if (!empty($this->next_column_definitions)) {
            $this->iteration_flag++;
            $this->html .= "</tr><tr>";
            $this->drawTHead($this->next_column_definitions);
        }
    }

    /**
     * If you're on a parent th column, used in cases on merged child cells, then call amOnParentColumn() and when you're off it, pass amOffParentColumn()
     * If you want to check if you're on parent column, check with if(parentColumn()), will return true/false
     *
     * @param boolean $bool
     * @return bool
     */
    public function parentColumn()
    {
        return $this->is_a_parent_column;
    }

    /**
     *
     */
    public function amOnParentColumn()
    {
        $this->is_a_parent_column = true;
    }

    /**
     *
     */
    public function amOffParentColumn()
    {
        $this->is_a_parent_column = false;
    }

    public function amOnRegularHeadingColumn()
    {
        $this->amOffParentColumn();
    }

    function resetObject()
    {
        $blankInstance = new static; //requires PHP 5.3+  for older versions you could do $blankInstance = new get_class($this);
        $reflBlankInstance = new \ReflectionClass($blankInstance);
        foreach ($reflBlankInstance->getProperties() as $prop) {
            $prop->setAccessible(true);
            //debug_info("Going to reset " . $prop->name);
            $this->{$prop->name} = $prop->getValue($blankInstance);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function reinitializeVariables()
    {
        $this->html = "";
        $this->table_columns = []; //very important, since our classes can be called as a facade and so a new instance might not necessarily be created everytime, so we need to re-initialize the variables
        $this->table_column_parents = []; //very important, since our classes can be called as a facade and so a new instance might not necessarily be created everytime, so we need to re-initialize the variables
        $this->column_definitions = [];
        $this->table_id = "";
        $this->table_classes = "";
        $this->column_count = 0;
        $this->is_a_parent_column = false;

        $this->column_count = 0;

        $this->default_body = "<tbody></tbody>";

        $this->current_column_name;

        $this->current_column_index = 0;

        $this->attributes_array = [];

        $this->in_col_group = false;

        $this->col_group = [];

        $this->delimiter = '|';

        $this->draw_checkbox = false;

        $this->rowspan = 1;

        $this->toolbar_contents = '';

        $this->toolbar_visibility = true;

        $this->export_button_visibility = false;

        $this->headerWrapper = '';

        $this->predefined_filters_in_toolbar = [];

        $this->main_rowspan = 1;
        $this->colspan = 1;
        $this->next_column_definitions = [];
        $this->iteration_flag = 0;

        return $this;
    }

    /**
     * @param $table_id
     */
    public function setTableId($table_id)
    {
        $this->table_id = $table_id;

        return $this;
    }


    public function getTableId()
    {
        return $this->table_id;
    }

    /**
     * @param $table_class_names
     */
    public function setTableClasses($table_class_names)
    {
        //check if "o:" has been prefixed in the class, then we'll want to over-write
        if (str_contains($table_class_names, "o:")) {
            $this->default_datatable_class_names = str_replace("o:", "", $table_class_names);
            return $this;
        }

        $this->default_datatable_class_names = ($table_class_names != "") ? $table_class_names : $this->getTableClasses();

        return $this;
    }

    /**
     * @param $col_group
     */
    public function setColGroup($col_group)
    {
        $this->col_group = $col_group;

        return $this;
    }

    /**
     * @param $column_display_names
     * @return $this
     */
    public function setColumnDefinitions($column_display_names)
    {
        $this->column_definitions = $column_display_names;
        return $this;
    }

    /**
     * @param $column_display_names
     * @return $this
     */
    public function setColumnDefinitionsRawValues($column_display_names)
    {
        $this->column_definitions_raw_values = $column_display_names;
        return $this;
    }

    protected function setDataColToColumnString($column_string)
    {
        $current_colun_index = $this->getCurrentColumnIndex();
        $column_string .= $this->getDelimiter() . 'data-col:' . $current_colun_index;

        $this->increaseCurrentColumnIndex();
        return $column_string;
    }

    protected function getFilterTypeAndSelectId($column_string)
    {
        $filter_type = get_string_between($column_string, "data-filter-type:", "|");
        $select_id = get_string_between($column_string, "data-filter-type:", "|");

        return [$filter_type, $select_id];
    }

    public function setDataColToColumnDefinition(&$column_display_names, $nested = false, &$array_type = 0)
    {

        if ($this->count_of_array_column_elements == 0) {
            $this->count_of_array_column_elements = count($column_display_names, COUNT_RECURSIVE);
        }
        $column_index = 0;
        $pre_key = [];
        $move_index = [];


        foreach ($column_display_names as $key => &$column_string) {

            //If the previous key from a nested element exists
            /*if (in_array($key, $pre_key)) {
                if($key=="I_AM_ODD"){
                    print_r($pre_key);
                }
                    continue;
            }*/
            $flattened_column_string = json_encode($column_string);
            //This is only required for exceptions defined in either of the loops below
            $malformed_array_message = "The column definitions for a DataTable with filters cannot have a mix of fields ending with an array and ending without one. Consistency is required in the format. The column '$key'=>'$flattened_column_string' (after translation) in table " . $this->getTableId() . " does not match the other types defined before.";


            if (is_array($column_string)) {

                if (empty($column_string)) {

                    if ($array_type == self::UNDEFINED_ARRAY_FORMAT) {
                        $array_type = self::ARRAY_FORMAT;
                        //echo "This is an array format with empty elements";
                    }

                    if ($array_type != self::ARRAY_FORMAT) {
                        throw new UnsupportedTypeException($malformed_array_message);
                    }
                    $new_value = $this->setDataColToColumnString($key);

                    //$column_display_names[ $new_value ] = $column_string;
                    $this->temp_array[$key] = $new_value;
                    // unset( $column_display_names[$key] );
                    $pre_key[] = $new_value;
                    $move_index[] = $column_index;
                    //$elements_processed++;
                    //echo "Processed element $elements_processed [$column_index] out of ".$this->count_of_array_column_elements." elements from nested element $nested_count: ".$new_value."\n";
                    // $column_display_names = array_move($new_value, $column_index, $column_display_names);
                } else {
                    $this->addColumnFilterHeadingHierarchyLevel($key);
                    //  echo "Array nesting for $key:<br>";
                    $this->setDataColToColumnDefinition($column_string, true, $array_type);
                }
            } else {

                //once it comes in here, then the whole array should be in the same format for things to work properly. So either each element should end in an empty key or in a value. Value based arrays should come here.
                if ($array_type == self::UNDEFINED_ARRAY_FORMAT) {
                    $array_type = self::FLAT_FORMAT;
                    //echo "This is an array format with flat elements";
                }

                if ($array_type != self::FLAT_FORMAT) {
                    throw new UnsupportedTypeException($malformed_array_message);
                }

                $old_column_string = $column_string;
                $column_string = $this->setDataColToColumnString($column_string);
                $this->temp_array[$old_column_string] = $column_string;
                //echo "Processed element $elements_processed out of ".$this->count_of_array_column_elements." from default: ".$column_string."\n";
                //            $elements_processed++;
            }
            $column_index++;
        }

        if (!$nested) {
            //$i=0;
            /*foreach($move_index as $key=>$value)
            {
                echo "Going to move pre_key $i to $value <br>";
                $column_display_names = array_move($pre_key[$i], $value, $column_display_names);
                $i++;
            }*/

            if ($array_type == 1) {
                return $column_display_names;
            }
            $column_display_names = $this->replaceArrayKeys($column_display_names, $this->temp_array);


        }

        /*if($this->column_filter_heading_hierarchy_level_count > 0) {
            $this->column_filter_heading_hierarchy_level_count = $this->column_filter_heading_hierarchy_level_count - 1;
            if($this->column_filter_heading_hierarchy_level_count == 0){
                $this->closeColumnFilterHeadingHierarchyLevel();
            }
        }*/

        return $column_display_names;
    }

    public function setSelectFilterValues(array $options_values)
    {
        $this->select_filter_values = $options_values;
    }

    protected function checkForColumnFilterHeadingHierarchyLevels($first_column)
    {
        $hierarchy = "";
        if($first_column && !empty($this->column_filter_heading_hierarchy_level)){
            /*if($this->column_filter_heading_hierarchy_level_count == 1){
                $this->column_filters_html .= $this->setFilterContainerHierarchyContainer();
            }*/
            $hierarchy = "<div><b>" . implode($this->column_filter_heading_hierarchy_delimiter, $this->column_filter_heading_hierarchy_level) . "</b></div>";
            $this->unsetColumnFilterHeadingHierarchyLevel();
        }

        return $hierarchy;
    }

    protected function filterContainerStart($table_id, $column_heading, $column_index, $first_column)
    {
        $hierarchy = $this->checkForColumnFilterHeadingHierarchyLevels($first_column);

        $this->column_filters_html .= "$hierarchy <div class='panel panel-default filtered'>
                         <a role='button' data-toggle='collapse' data-parent='#accordion' href='#collapse$table_id$column_index' aria-expanded='true' aria-controls='collapse$table_id$column_index'>
                             <div class='panel-heading' role='tab' id='heading$table_id$column_index'>
                                 <h4 class=\"panel-title\">
                                     <svg>
                                         <use class='filter-line' xlink:href='/img//svg-sprites/manage-alerts.svg#filter-line'></use>
                                         <use class='filter-solid' style='display: none' xlink:href='/img//svg-sprites/manage-alerts.svg#filter-solid'></use>
                                     </svg>
                                     $column_heading
                                 </h4>
                                 <div id='$table_id-$column_index-alerts-tag'>
                                 </div>
                             </div>
                         </a>
                         <div id='collapse$table_id$column_index' class='panel-collapse collapse' role='tabpanel' aria-labelledby='heading$table_id$column_index'>
                             <div class='panel-body'>
                                 <div class='form-group margin-bottom-0'>";
    }

    protected function filterContainerEnd($table_id, $column_heading, $column_index)
    {
        $this->column_filters_html .= "<div>
                                             <button type='button' class='btn blue btn-med global-filter-btn filter_now' tableId='$table_id' data-col='$column_index' data-custom-title='$column_heading' onclick='addTableColumnFilter(this)'>Add Filter</button>
                                         </div>
                                     </form>
                                 </div>
                             </div>
                         </div>
                     </div>";
    }

    /*protected function setFilterContainerHierarchyContainer()
    {
        $this->column_filter_heading_hierarchy_container = "<div class='mv-table-heading-heirarchy'>";
    }*/

    protected function addColumnFilterHeadingHierarchyLevel($heading)
    {
        //$this->column_filter_heading_hierarchy_level_count++;

        $this->column_filter_heading_hierarchy_level[] = $heading;

        return $this->column_filter_heading_hierarchy_level;
    }

    protected function closeColumnFilterHeadingHierarchyLevel()
    {
        $this->column_filters_html .= "Closing hierarchy </div>";
    }

    protected function unsetColumnFilterHeadingHierarchyLevel()
    {
        array_pop($this->column_filter_heading_hierarchy_level);
        //$this->column_filter_heading_hierarchy_level = [];
    }

    protected function generateFilterHtml($columns, $current_colun_index = -1, $nested = false)
    {
        $first_column = true;
        if(!$nested){
            $this->column_filter_heading_hierarchy_level = [];
        }

        foreach ($columns as $parent_column => $column_string)
        {
            if(is_array($column_string))
            {
                if(!empty($column_string)) {
                    list($parent_column, $attributes) = $this->extractAttributesFromColumn($parent_column);
                    $this->column_filter_heading_hierarchy_level[] = $parent_column;
                    $this->generateFilterHtml($column_string, $current_colun_index, true);
                    continue;
                }else{
                    $column_string = $parent_column;
                }
            }

            $current_colun_index++;
            //Generate filter html for each column
            list($filter_type, $select_id) = $this->getFilterTypeAndSelectId($column_string);
            if($filter_type != "") {
                list($column_heading, $attributes) = $this->extractAttributesFromColumn($column_string);
                $this->addFilterType($this->table_id, $filter_type, $column_heading, $current_colun_index, $first_column, $select_id);
                $first_column = false;
            }
        }
    }

    protected function addFilterType($table_id, $filter_type, $column_heading, $column_index, $first_column, $select_id = "")
    {
        $this->filterContainerStart($table_id, $column_heading, $column_index, $first_column);
        switch ($filter_type) {

            case 'text':
                //domObj.eq(0).html(MvDataTableFilterDesign.DropDown(tableId + "Operator" + column_index, DataTableFilterSettings.text, tableId, column_index));
                $this->column_filters_html .= $this->dropdown($table_id . "Operator" . $column_index, $this->data_table_filter_settings['text'], $table_id, $column_index);

                //domObj.eq(1).html(MvDataTableFilterDesign.TextField(tableId + "FilterText" + column_index));
                $this->column_filters_html .= $this->textField($table_id . "FilterText" . $column_index);

                //$("body").append(MvDataTableFilterDesign.ContainsMultipleModal(tableId, column_index));
                $this->column_filters_html .= $this->containsMultipleModal($table_id, $column_index);

                //$("body").append(MvDataTableFilterDesign.DoesNotContainsMultipleModal(tableId, column_index));
                $this->column_filters_html .= $this->doesNotContainsMultipleModal($table_id, $column_index);
                break;

            case 'number':
                //domObj.eq(0).html(MvDataTableFilterDesign.DropDown(tableId + "Operator" + column_index, DataTableFilterSettings.number));
                $this->column_filters_html .= $this->dropdown($table_id . "Operator" . $column_index, $this->data_table_filter_settings['number'], $table_id, $column_index);

                //domObj.eq(1).html(MvDataTableFilterDesign.TextFieldForNumber(tableId + "FilterNumber" + column_index, tableId + "Operator" + column_index, tableId, column_index));
                $this->column_filters_html .= $this->textFieldForNumber($table_id . "FilterNumber" . $column_index, $table_id . "Operator" . $column_index,$table_id, $column_index);
                break;

            case 'select':
                /*var select_id = checkSelectId(self);
                if (!select_id) {
                    return false;
                }*/
                //domObj.eq(0).html(MvDataTableFilterDesign.DropDown(tableId + "Operator" + column_index, DataTableFilterSettings.select));
                $this->column_filters_html .= $this->dropdown($table_id . "Operator" . $column_index, $this->data_table_filter_settings['select']);

                //domObj.eq(1).html(MvDataTableFilterDesign.SecondDropDown(tableId + "FilterSelect" + column_index, select_id));
                $this->column_filters_html .= $this->secondDropDown($table_id . "FilterSelect" . $column_index, $select_id);
                break;
            case 'date':
                //domObj.eq(0).html(MvDataTableFilterDesign.DropDown(tableId + "Operator" + column_index, DataTableFilterSettings.all));
                $this->column_filters_html .= $this->dropdown($table_id . "Operator" . $column_index, $this->data_table_filter_settings['all']);

                //var $ref_id = tableId + "FilterDateTime" + column_index;
                //domObj.eq(1).html(MvDataTableFilterDesign.TextField(refId));
                //$('#' + refId).datepicker({autoclose: true, container: domObj.eq(1)});
                $this->column_filters_html .= $this->textField($table_id . "FilterDateTime" . $column_index);
                break;
            default:
                //domObj.eq(0).html(MvDataTableFilterDesign.DropDown(tableId + "Operator" + column_index, DataTableFilterSettings.all));
                $this->column_filters_html .= $this->dropdown($table_id . "Operator" . $column_index, $this->data_table_filter_settings['all']);

                //domObj.eq(1).html(MvDataTableFilterDesign.TextField(tableId + "FilterText" + column_index));
                $this->column_filters_html .= $this->textField($table_id . "FilterText" . $column_index);
        }
        $this->filterContainerEnd($table_id, $column_heading, $column_index);
    }

    protected function dropdown($ref_id, $options, $table_id = '', $column_index = '')
    {
        $contains_modal_id_part = '';
        if($table_id != '') {
            $contains_modal_id_part = $table_id . 'FilterTextArea' . $column_index;
        }

        $filter_select_tag = '<select id="' . $ref_id . '" class="operator_select form-control" table-id="' . $table_id.  '" column-index="' . $column_index . '" data-contains-modal-id-part="' . $contains_modal_id_part . '">';

        for ($i = 0; $i < count($options); $i++) {
            $filter_select_tag .= '<option value="' . $i . '">' . $options[$i] . '</option>';
        }

        $filter_select_tag .= '</select>';

        return $filter_select_tag;
    }

    protected function secondDropDown($ref_id, $select_id)
    {
        $select_html = '<select id="' . $ref_id . '" class="form-control">';
        $dropdown = (isset($this->select_filter_values[$select_id])) ? $this->select_filter_values[$select_id] : [];
        if(!empty($dropdown)) {
            $select_html .= '<option value="">' . $dropdown["blank_label"] . '</option>';
            for ($i = 0; $i < count($dropdown['list']); $i++) {
                $select_html .= '<option value="' . $dropdown['list'][$i]['value'] . '">' . $dropdown['list'][$i]['text'] . '</option>';
            }
        }
        $select_html .= '</select>';
        return $select_html;
    }

    protected function textField($ref_id)
    {
        return '<input type="text" id="' . $ref_id . '"  class="form-control operand_text mx_dt_text" >';
    }

    protected function textArea($ref_id)
    {
        return '<div class="mx_dt_text_area_container" style="display: none;" ><textarea id="' . $ref_id . '"  class="form-control operand_text mx_dt_text_area" ></textarea> <label class="mx_dt_radio_label"><input name="' . $ref_id . '_radio" type="radio" id="' . $ref_id . '_radio_all" value="all" checked > All</label>&nbsp;&nbsp;<label class="mx_dt_radio_label"><input name="' . $ref_id . '_radio" type="radio" id="' . $ref_id . '_radio_any_one" value="any" > Any one</label></div>';
    }

    protected function containsMultipleModal($table_id, $column_index) {
        $ref_id = $table_id . "FilterTextArea" . $column_index;
        return '<div id="contains_multiple_modal_for_' . $ref_id . '" data-keyboard="true" class="modal fade in new-modal-styles" aria-hidden="true">' .
            '<div class="modal-dialog modal-lg width-70">' .
            '<div class="modal-content">' .
            '<div class="modal-header">' .
            '<button aria-hidden="true" data-dismiss="modal" class="close contains-multiple-close" type="button" id="close_button" table-id="' . $table_id . '" column-index="' . $column_index . '"></button>' .
            '<h4 class="modal-title">Contains Multiple</h4>' .
            '</div>' .
            '<div class="modal-body nopadding">' .
            '<div class="form">' .
            '<div class="form_content"> <div class="form-horizontal form-bordered">' .
            '<div class="form-group form-md-line-input" style="padding-bottom: 0px !important;">' .
            '<div class="control-label col-xs-12 col-sm-4 col-md-4 col-lg-3">' .
            '<label for="select_tags">Enter Keywords:<div style="font-size:11px;text-align:center;">(one keyword/phrase per line)</div></label>' .
            '</div>' .
            '<div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">' .
            '<div class="input-xlarge input-inline">' .
            '<textarea id="' . $ref_id . '"  class="'. $table_id .' form-control operand_text mx_dt_text_area"></textarea>' .
            '</div>' .
            '<div class="input-xlarge" style="margin-top: 5px;">' .
            '<span class="pull-right">' .
            '<a data-toggle="modal" href="#" class="btn mini add-from-manager" data-type="keywords" data-manager-type="tag-pages" data-source-selector="' . $ref_id . '" data-max-attr-allowed="5000"><i class="fa fa-bitbucket"></i> Import Keywords </a>' .
            '</span>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '<div class="form-group form-md-line-input" style="padding-top: 5px !important;">' .
            '<div class="control-label col-xs-12 col-sm-4 col-md-4 col-lg-3">' .
            '<label for="select_tags">Contains:</label>' .
            '</div>' .
            '<div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">' .
            '<div class="md-radio-inline">' .
            '<div class="md-radio">' .
            '<input name="' . $ref_id . '_radio" type="radio" id="' . $ref_id . '_radio_any_one" value="any" checked="checked" radio_text="Any One"><label for="' . $ref_id . '_radio_any_one"><span class="inc"></span><span class="check"></span><span class="box"></span>Any One</label>' .
            '</div>' .
            '<div class="md-radio">' .
            '<input name="' . $ref_id . '_radio" type="radio" id="' . $ref_id . '_radio_all" value="all" radio_text="All"><label for="' . $ref_id . '_radio_all"><span class="inc"></span><span class="check"></span><span class="box"></span>All</label>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '<div class="form-actions clearfix">' .
            '<div class="col-md-offset-3 col-md-9 col-lg-offset-3 col-lg-9 margin-bottom-20">' .
            '<button type="button" data-dismiss="modal" class="filter_now_from_contains_modal btn blue btn-med" table-id="' . $table_id . '" column-index="' . $column_index . '">Filter</button>' .
            '<button type="button" data-dismiss="modal" class="btn default btn-med contains-multiple-close" table-id="' . $table_id . '" column-index="' . $column_index . '">Close</button>' .
            '</div>' .
            '</div>' .
            '</div></div>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '</div>';
    }

    protected function doesNotContainsMultipleModal($table_id, $column_index)
    {
        $ref_id = $table_id . "FilterTextArea" . $column_index;
        return '<div id="does_not_contain_multiple_modal_for_' . $ref_id . '" data-keyboard="true" class="modal fade in new-modal-styles" aria-hidden="true">' .
            '<div class="modal-dialog modal-lg width-70">' .
            '<div class="modal-content">' .
            '<div class="modal-header">' .
            '<button aria-hidden="true" data-dismiss="modal" class="close contains-multiple-close" type="button" id="close_button" table-id="' .$table_id . '" column-index="' . $column_index . '"></button>' .
            '<h4 class="modal-title">Does Not Contains Multiple</h4>' .
            '</div>' .
            '<div class="modal-body nopadding">' .
            '<div class="form">' .
            '<div class="form_content"> <div class="form-horizontal form-bordered">' .
            '<div class="form-group form-md-line-input" style="padding-bottom: 0px !important;">' .
            '<div class="control-label col-xs-12 col-sm-4 col-md-4 col-lg-3">' .
            '<label for="select_tags">Enter Keywords:<div style="font-size:11px;text-align:center;">(one keyword/phrase per line)</div></label>' .
            '</div>' .
            '<div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">' .
            '<div class="input-xlarge input-inline">' .
            '<textarea id="' . $ref_id . '_does_not"  class="'.$table_id.' form-control operand_text mx_dt_text_area"></textarea>' .
            '</div>' .
            '<div class="input-xlarge" style="margin-top: 5px;">' .
            '<span class="pull-right">' .
            '<a data-toggle="modal" href="#" class="btn mini add-from-manager" data-type="keywords" data-manager-type="tag-pages" data-source-selector="' . $ref_id . '_does_not" data-max-attr-allowed="5000"><i class="fa fa-bitbucket"></i> Import Keywords </a>' .
            '</span>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '<div class="form-actions clearfix">' .
            '<div class="col-md-offset-3 col-md-9 col-lg-offset-3 col-lg-9 margin-bottom-20">' .
            '<button type="button" data-dismiss="modal" class="filter_now_from_contains_modal btn blue btn-med" table-id="' .$table_id . '" column-index="' . $column_index . '">Filter</button>' .
            '<button type="button" data-dismiss="modal" class="btn default btn-med contains-multiple-close" table-id="' .$table_id . '" column-index="' . $column_index . '">Close</button>' .
            '</div>' .
            '</div>' .
            '</div></div>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '</div>';
    }

    protected function textFieldForNumber($ref_id, $select2_id, $table_id, $column_index)
    {
        return '<input type="text" id="' . $ref_id . '_1"  class="form-control operand_text" >' .
            '<div id="number_filter_to_div' . $table_id . $column_index . '" style="display: none">' .
            '<input type="text" id="' . $ref_id . '_2"  class="form-control operand_text" placeholder="To">' .
            '</div>' .
            "<script type='text/javascript'> $('#" . $select2_id . "').on('change', function () { var between_selected = 6; var selected_val = $('#" . $select2_id . "').val(); if (between_selected == selected_val) { $('#number_filter_to_div" . $table_id . $column_index . "').show('slow'); $('#" . $ref_id . "_1').attr('placeholder', 'From'); } else { $('#number_filter_to_div" . $table_id . $column_index . "').hide('slow'); $('#" . $ref_id . "_1').removeAttr('placeholder'); } });  </script>";
    }


    private function replaceArrayKeys($original_array, $temp_array)
    {
        //echo "HERE";
        //dd($original_array);
        $original_array_flattened = json_encode($original_array, JSON_UNESCAPED_SLASHES);

        //dd($temp_array);
        foreach ($temp_array as $key => $value) {
            // echo "Going to search and replace $key with $value <br>\n";
            $original_array_flattened = str_replace($key, $value, $original_array_flattened);
        }

        //echo $original_array_flattened;

        return json_decode($original_array_flattened, true);
    }


    /**
     * @return int
     */
    private function increaseColumnCount()
    {
        return $this->column_count++;
    }

    /**
     * @return int
     */
    public function getColumnCount()
    {
        return $this->column_count;
    }

    /**
     * @return mixed
     */
    public function getCurrentColumnName()
    {
        return $this->current_column_name;
    }

    /**
     * @param mixed $current_column_name
     */
    public function setCurrentColumnName($current_column_name)
    {
        return $this->current_column_name = $current_column_name;
    }

    /**
     * @param $attribute_part
     * @return bool
     */
    private function isStyleAttribute($attribute_part)
    {
        return in_array($attribute_part, $this->style_attributes);
    }

    /**
     * @param $attribute_parts
     */
    private function handleToolTip($attribute_parts)
    {
        $tooltip_parts = explode($this->getTooltipSeparator(), $attribute_parts[1]);
        $tooltip_content = $tooltip_parts[0];
        $tooltip_heading = isset($tooltip_parts[1]) ? $tooltip_parts[1] : "";
        $tooltip_template = $this->replaceTooltipTemplate($tooltip_content, $tooltip_heading);
        $this->appendToCurrentColumnName($tooltip_template);
    }

    public function appendToCurrentColumnName($string)
    {
        $column_name = $this->getCurrentColumnName();

        $column_name_after_appending = str_replace('header_title', $column_name, $string);

        //$column_name_after_appending = $column_name . $string;

        $this->setCurrentColumnName($column_name_after_appending);

    }

    /**
     * @return string
     */
    public function getTooltipTemplate()
    {
        return $this->tooltip_template;
    }

    /**
     * @param string $tooltip_template
     * @param string $tooltip_content
     * @param string $tooltip_heading
     */
    public function setTooltipTemplate($tooltip_template)
    {
        $this->tooltip_template = $tooltip_template;

        return $this;
    }

    /**
     * @param $tooltip_content
     * @param string $tooltip_heading
     */
    public function replaceTooltipTemplate($tooltip_content, $tooltip_heading = "")
    {
        return str_replace(['{tooltip_content}', '{tooltip_heading}'], [$tooltip_content, $tooltip_heading], $this->getTooltipTemplate());

    }

    /**
     * @param $column_string
     * @return bool
     */
    private function hasCustomAttributes($column_string)
    {
        return str_contains($column_string, $this->getDelimiter());
    }

    /**
     * @param $column_string
     * @return string
     */
    private function getAttributesString($column_string)
    {
        return substr($column_string, strpos($column_string, $this->getDelimiter()) + strlen($this->getDelimiter()));
    }

    /**
     * @param $column_string
     * @return string
     */
    private function extractColumnNameFromColumnString($column_string)
    {
        if ($this->hasCustomAttributes($column_string)) {
            $column_name = substr($column_string, 0, strpos($column_string, $this->getDelimiter()));
            return $column_name;
        } else {
            $column_name = $column_string;
            return $column_name;
        }
    }

    /**
     * @param $column_string
     * @return string
     */
    private function appendDefaultFilters($column_string)
    {
        if ($this->filterEnabled() && !$this->parentColumn()) {
            $pos = $this->isFilterOffForColumn($column_string);
            $filter_text = '';
            if ($pos === false) {
                $filter_text = $this->getDelimiter() . 'data-filter:on';
            }
            $column_string .= $filter_text . $this->getDelimiter() . 'data-custom-title:' . strip_tags($this->getCurrentColumnName());
            ////debug_info($column_string);
            return $column_string;
        }

        return $column_string;
    }

    /**
     * @return mixed
     */
    public function getCurrentColumnIndex()
    {
        return $this->current_column_index;
    }

    /**
     * @param int $current_column_index
     */
    public function setCurrentColumnIndex($current_column_index)
    {
        $this->current_column_index = $current_column_index;
    }

    /**
     * Increase Current Column Index to 1
     */
    public function increaseCurrentColumnIndex()
    {
        $this->setCurrentColumnIndex($this->getCurrentColumnIndex() + 1);
    }

    /**
     * @param $attribute
     * @return array
     */
    private function explodeAndRetrieveIndividualAttributes($attribute)
    {
        $attribute_parts = explode(":", $attribute);
        //echo "count of attribute parts in $attribute is ".count($attribute_parts);
        if (count($attribute_parts) == 1) {//this means it's an exception and will be a width attribute
            $attribute_parts[1] = $attribute_parts[0]; //take that as the value - DO NOT CHANGE THE ORDER OF THIS, should always be before the next line
            $attribute_parts[0] = "width"; //replace original to set to width attribute
        }

        $attribute_parts[1] = addslashes($attribute_parts[1]);

        //If the attribute has already been defined by the user and it's not a parent column (because parent column don't have column index and hence would result in exception being made, this added recently because of a bug where if parent comes after a regular column, this exception was raised and the attributes would not get appeended), then any defaults we may have specified should not get added/appended. Should not do for colgroup and tooltip, since no defaults available for those types.
        /*if ($this->attributeExistsInCurrentColumn($attribute_parts[0]) && !$this->parentColumn() && !$this->isInColGroup() && $attribute_parts[0] != "tooltip") {
            return false;

        }*/

        $this->addAttributeToCurrentColumn($attribute_parts[0], $attribute_parts[1]);

        return $attribute_parts;
    }

    /**
     * @return array
     */
    public function getAllAttributes()
    {
        return $this->attributes_array;
    }

    /**
     * @param array $attributes_array
     */
    public function setAllAttributes($attributes_array)
    {
        $this->attributes_array = $attributes_array;
    }

    public function addAttributeToColumn($column_index, $attribute_key, $attribute_value)
    {

        $this->attributes_array[$column_index][$attribute_key] = $attribute_value;

    }

    public function addAttributeToCurrentColumn($attribute_key, $attribute_value)
    {

        $this->addAttributeToColumn($this->getCurrentColumnIndex(), $attribute_key, $attribute_value);

    }

    public function getAttributesForColumn($column_index)
    {

        return $this->attributes_array[$column_index];

    }

    public function getAttributesForCurrentColumn()
    {

        return $this->attributes_array[$this->getCurrentColumnIndex()];

    }

    public function attributeExistsInColumn($column_index, $attribute_key)
    {
        if (isset($this->attributes_array[$column_index]) && is_array($this->attributes_array[$column_index]) && array_key_exists($attribute_key, $this->attributes_array[$column_index])) {
            //debug_info("Found $attribute_key in column $column_index");
            //debug_info($this->attributes_array[$column_index]);
            return true;
        }

        return false;

    }

    public function attributeExistsInCurrentColumn($attribute_key)
    {

        return $this->attributeExistsInColumn($this->getCurrentColumnIndex(), $attribute_key);

    }

    /**
     * @return string
     */
    public function getDefaultBody()
    {
        return $this->default_body;
    }

    /**
     * @param string $default_body
     */
    public function setDefaultBody($default_body)
    {
        $this->default_body = $default_body;
    }

    public function drawFilterDiv($table_id)
    {
        if (Request::input('pdf_view') == "yes") {
            $toolbar_visibility = false;
        } else {
            $toolbar_visibility = $this->toolbar_visibility;
        }

        $content = view('mondovo.datatable.datatable-filter')->with(['table_id' => $table_id, 'toolbar_contents' => $this->toolbar_contents, 'toolbar_visibility' => $toolbar_visibility, 'predefined_filters_in_toolbar' => $this->predefined_filters_in_toolbar, 'export_button_visibility' => $this->export_button_visibility, 'export_report_name' => $this->export_report_name, 'export_report_date' => $this->export_report_date, 'export_strip_columns' => $this->export_strip_columns, 'excel_column_delimiter' => $this->excel_column_delimiter, 'keyword_group_filter' => $this->keyword_group_filter, 'operations' => $this->operations, 'column_filters' => $this->column_filters_html]);

        $this->html = $content;

        return $content;
    }

    public function setColumnsData($data)
    {
        $this->columns_data = $data;
    }

    public function isExistColumnsData()
    {
        return !empty($this->columns_data);
    }

    public function fillTbody()
    {
        $data = $this->columns_data;
        $this->openTbody();
        foreach ($data as $column_data) {
            $this->openRow();
            $keys = array_keys($column_data);
            if ($this->isEnabledDrawCheckbox()) {
                $this->drawCheckBoxForTbody();
            }
            foreach ($keys as $value) {
                $this->openColumn();
                $this->fillDataInsideColumn($column_data[$value]);
                $this->closeColumn();
            }
            $this->closeRow();
        }
        $this->closeTbody();
    }

    public function drawCheckBoxForTbody()
    {
        $this->openColumn();
        $this->html .= '<input type="checkbox" class="' . $this->sigle_checkbox_class . '" />';
        $this->closeColumn();
    }

    public function fillDataInsideColumn($data)
    {
        $this->html .= $data;

        return $this;
    }

    /**
     * @param $html_string
     */
    public function addToToolbar($html_string)
    {
        $this->toolbar_contents .= $html_string;
    }

    public function showToolbar()
    {
        $this->toolbar_visibility = true;
    }

    public function hideToolbar()
    {
        $this->toolbar_visibility = false;
    }

    /**
     * @param array $export_settings ['report_name' => 'Report Name', 'report_date' => '2016-03-18', 'strip_column_index' => [0, 1, 2, 3]]
     */
    public function showExportButton($export_settings)
    {
        $this->export_report_name = isset($export_settings['report_name']) ? $export_settings['report_name'] : "";
        $this->export_report_date = isset($export_settings['report_date']) ? $export_settings['report_date'] : "";
        $this->export_strip_columns = isset($export_settings['strip_column_index']) ? implode($this->excel_column_delimiter, $export_settings['strip_column_index']) : "";
        $this->export_button_visibility = true;
    }

    public function hideExportButton()
    {
        $this->export_button_visibility = false;
    }

    /**
     * @param string[] $array_of_filter_conditions
     * Ex: [ ['column_name', 'operator_name', 'value' ], ['column_name', 'operator_name', 'value' ] ];
     * @return mixed
     */
    public function setPreFilterConditions(array $array_of_filter_conditions)
    {
        if ($array_of_filter_conditions[0] == 'filter_by_manager') {
            $manager = [];

            $attributes = $array_of_filter_conditions[1];
            foreach ($attributes as $attribute_name => $attribute_value) {
                $manager[$attribute_name] = $attribute_value;
            }

            return $manager;
        } else {
            if (!is_array($array_of_filter_conditions[0])) {
                $array_of_filter_conditions_to_send[0] = $array_of_filter_conditions;
            } else {
                $array_of_filter_conditions_to_send = $array_of_filter_conditions;
            }

            $data = $this->dataColumns;
            $filter_array = [];

            foreach ($array_of_filter_conditions_to_send as $filter_condition) {
                if (empty($filter_condition)) {
                    continue;
                }

                $column_index = array_search($filter_condition[0], $data);
                if ($column_index === false) {
                    continue;
                }

                if ($this->isEnabledDrawCheckbox() && $data[0] != 'check_box_id') {
                    $column_index = $column_index + 1;
                }

                if (empty($filter_array[$column_index])) {
                    $filter_array[$column_index] = [];
                }

                if (empty($filter_array[$column_index][$filter_condition[1]])) {
                    $filter_array[$column_index][$filter_condition[1]] = [];
                }

                if ($filter_condition[1] == 'Between') {
                    $filter_array[$column_index][$filter_condition[1]] = explode(',', $filter_condition[2]);
                } else {
                    $filter_array[$column_index][$filter_condition[1]][] = $filter_condition[2];
                }

            }

            return json_encode($filter_array);
        }
    }

    /**
     * @param $pre_filter_title
     * @param string[] $array_of_filter_conditions
     * Ex: [ ['column_name', 'operator_name', 'value' ], ['column_name', 'operator_name', 'value' ] ];
     * Or
     * Ex: ['column_name', 'operator_name', 'value' ]  If it has only one condition
     * @param bool|string $overwrite_existing_filter
     * @param string $filter_id
     * @return $this
     */
    public function setPreFilterConditionsInToolbar($pre_filter_title, $array_of_filter_conditions, $overwrite_existing_filter = 'true', $filter_id = '')
    {
        if ($overwrite_existing_filter) {
            $overwrite_existing_filter = "yes";
        } else {
            $overwrite_existing_filter = "no";
        }
        $this->predefined_filters_in_toolbar[] = [
            'table_id' => $this->table_id,
            'pre_filter_condition' => $this->setPreFilterConditions($array_of_filter_conditions),
            'overwrite_existing_filter' => $overwrite_existing_filter,
            'filter_title' => $pre_filter_title,
            'filter_id' => $filter_id,
        ];

        return $this;
    }

    /**
     * @param string [] $dataColumns
     * @return array
     */
    public function setDataColumns(array $dataColumns)
    {
        $this->dataColumns = $dataColumns;
    }

    /**
     * @return string
     */
    public function getTooltipSeparator()
    {
        return $this->tooltip_separator;
    }

    public function enableKeywordGroupFilter($column_name)
    {
        $columns = $this->getColumnDetails();
        $column_index = array_search($column_name, $columns);
        $this->keyword_group_filter = ['column_name' => $column_name, 'column_index' => $column_index];
        return $this;
    }

    public function enableTableOperations()
    {
        $this->operations = true;
        return $this;
    }

    public function getColumnDetails()
    {
        $column_raw_data = $this->column_definitions_raw_values;

        if (empty($column_raw_data))
        {
            return [];
        }

        $checkbox_col = $this->isEnabledDrawCheckbox() ? 1 : 0;
        $columns = [];
        $i = 0;

        $this->cols($column_raw_data, $i, $columns, $checkbox_col);

        return $columns;
    }

    public function cols($column_array, &$i, &$output_array, $checkbox_col)
    {
        foreach ($column_array as $index => $col_details)
        {
            if(!is_array($col_details)){
                $index = $col_details;
            }
            $output_array[$i + $checkbox_col] =  explode('|', $index)[0];

            $i++;

            if(is_array($col_details))
            {
                $this->cols($col_details, $i, $output_array, $checkbox_col);
            }
        }

        return $output_array;
    }

    public function enableCheckBoxLimit($limit)
    {
        $this->check_box_limit = $limit;
        return $this;
    }
}