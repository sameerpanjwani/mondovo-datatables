<?php namespace Mondovo\DataTable;

/**
 * Created by PhpStorm.
 * User: Ashutosh
 * Date: 05-05-2015
 * Time: 11:39
 */

use Mondovo\DataTable\Contracts\DataTableFilterInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class DataTableFilter extends DataTableAdapter implements DataTableFilterInterface
{
    protected $raw_columns_indexes = [];
    protected $having_columns_indexes = [];

    protected $raw_columns_names_in_filter = [];
    protected $or_raw_columns_names = [];
    protected $raw_columns_values = [];

    protected $having_columns_names_in_filter = [];
    protected $or_having_columns_names = [];
    protected $having_columns_values = [];

    protected $concat_condition_in_filter;

    public $check_box_column_name = 'check_box_id';
    public $check_box_column_index = 0;
    protected $reserve_column_count = 0;

    public function of($builder)
    {
    	//Check if special filter operator is available
	    $this->checkSpecialFilterOperatorsAvailable();

        parent::of($builder);

        if ($this->isEnabledDrawCheckbox()) {
            /* process Checkbox If exist */
            $this->setConditionForCheckboxColumn();
        }

        if (!empty($this->columns)) {
            /* Set Column Name If not passed from Javascript */
            $this->setAllColumnNames();
        } else {
            $this->setDefaultOrderForEmptyResults();
        }

        return $this;
    }

	protected function checkSpecialFilterOperatorsAvailable() {
    	if(!$this->existsCustomFilter()){
    		return false;
	    }
		$custom_filter = $this->getCustomFilter();

		foreach ($custom_filter as $column_index => $operators) {
			foreach ($operators as $operator) {
				if ( $this->isSpecialFilterOperator( $operator ) ) {
					$this->special_filter = true;
					return true;
				}
			}
		}

		return true;
    }

    /**
     * Edit column's content
     *
     * @param  string $name
     * @param  string $content
     * @return $this
     */
    public function editColumn($name, $content)
    {
        if (empty(array_filter($this->columns)) || !$this->isAliasColumnNameExists($name)) {
            return $this;
        }

        if (!is_callable($content)) {
            return parent::editColumn($name, $content);
        }

        parent::editColumn($name, function ($data) use ($content) {
            $new_data = $data;
            /* Eloquent Builder */
            if (is_array($data) || property_exists($data, 'attributes')) {
                $new_data = $data;
            } /* Query Builder */
            else if (is_object($data)) {
                $new_data = (array)$data;
            }

            return $content($new_data);
        });

        return $this;
    }

	public function disableCache() {
		$this->disable_cache = true;
    }

    /**
     * @param array $column_name_default_value_pair
     * @return $this
     */
    public function setDefaultValues(array $column_name_default_value_pair)
    {
        foreach ($column_name_default_value_pair as $column_alias_name => $column_default_value)
        {
            $this->editColumn($column_alias_name, function ($data) use($column_alias_name, $column_default_value) {

                $current_column_data = $data[$column_alias_name];

                //If column doesn't have any value or is null, then give the default value.
                if ( (empty($current_column_data) && $current_column_data != 0) || is_null($current_column_data))
                {
                    return $column_default_value;
                }

                return $current_column_data;

            });
        }

        return $this;
    }

    /**
     * @param $search_column_name
     * @return int
     */
    protected function traverseAllInputColumnsAndSearchColumnName($search_column_name)
    {
        $index = 0;
        foreach ($this->input['columns'] as $column_name) {
            if ($column_name['data'] == $search_column_name) {
                return $index;
            }
            $index++;
        }
        return false;
    }

    /**
     * Add column in collection
     *
     * @param string $name
     * @param string $content
     * @param bool|int $order
     * @return Datatables
     */
    public function addColumn($name, $content, $order = false)
    {
        $column_index = $this->traverseAllInputColumnsAndSearchColumnName($name);
        if (is_bool($column_index)) {
            return false;
        }

        $this->disableSearchable($column_index);
        $this->disableSortable($column_index);

        if (!is_callable($content)) {
            return parent::addColumn($name, $content, $order);
        }

        parent::addColumn($name, function ($data) use ($content) {
            $new_data = $data;
            /* Eloquent Builder */
            if (is_array($data) || property_exists($data, 'attributes')) {
                $new_data = $data;
            } /* Query Builder */
            else if (is_object($data)) {
                $new_data = (array)$data;
            }

            return $content($new_data);
        }, $order);

        return $this;
    }

    /**
     * Organizes works
     *
     * @param bool $mDataSupport
     * @return null
     */
    public function make($mDataSupport = false)
    {
        if ($this->existsCustomFilter()) {
            /* Custom Filter */
            $this->processCustomFilter();
        }

        /* Indivisual Filter If Any*/
        $this->processIndividualFilter();

        /* Global Filter Process */
        if ($this->existsGlobalSearchValue()) {
            if ($this->isCollection()) {
                $this->processGlobalFilterInCollection();
            } else {
                $this->processGlobalFilterInQuery();
            }

            /* So that Yajra should not proceed it with global filter*/
            $this->setGlobalSearchValue('');
        }

        /* Any case Yajra should not handle having as well as raw columns. This code has no such great effect because all types of filter i m handling it. But anyway for precaution purpose let it be*/
        $this->disableSearchableForAllHavingColumns();
        $this->disableSearchableForAllRawColumns();

        return parent::make($mDataSupport);
    }

    /**
     * Process Individual Filter
     */
    public function processIndividualFilter($individual_filter_operator = 'Contains')
    {
        $this->resetCustomFilter();

        for ($column_index = 0; $column_index < $this->getCountOfColumns(); $column_index++) {

            if (!$this->isSearchable($column_index) || !$this->existsSearchValue($column_index)) {
                continue;
            }

            $this->input['maximizerFilter'][$column_index][$individual_filter_operator][0] = ['search_value' => $this->getSearchValue($column_index), 'concat_columns' => '_'];

            /* So that Yajra should not proceed it with individual filter*/
            $this->setSearchValue($column_index, '');
        }

        if ($this->existsCustomFilter()) {
            /* Custom Filter */
            $this->processCustomFilter();
        }
    }

    /**
     * @param $rawColumnsIndex
     * @return $this
     */
    public function setAllRawColumns($rawColumnsIndex)
    {
	    if($this->disable_cache === false && $this->special_filter === false){
		    return $this;
	    }
        $rawColumnsIndex = $this->stringToArray($rawColumnsIndex);
        $columns = $this->columns;

        foreach ($rawColumnsIndex as $column_index) {
            $column_index = intval($column_index);
            $column = $this->extractColumnName($columns[$column_index]);
            $column_index += $this->reserve_column_count;
            $this->setRawColumnName($column_index, $column);
            $this->setColumnName($column_index, $this->getAliasName($column_index));
            $this->escapeRawColumnAlias($column_index);
        }
        return $this;
    }

    /**
     * @param $havingColumnsIndex
     * @return $this
     */
    public function setAllHavingColumns($havingColumnsIndex)
    {
        $havingColumnsIndex = $this->stringToArray($havingColumnsIndex);
        foreach ($havingColumnsIndex as $column_index) {
            $column_index = intval($column_index) + $this->reserve_column_count;
            $this->enableHaving($column_index);
        }
        return $this;
    }

    /**
     * Added by Nikhil when there was problem in SERP Report Form, where the query select field index name alias was in format 'website_url^12452' with symbol: ^
     *
     * Situation:
     * Here, in this situation, while giving DB::raw() for this column, the alias was given like : 'website_url^12452' . Since symbol: ^ is a problem in sql unless its in proper quotes. So while the data-table filter takes that column for filtering, it uses the alias name with quotes and again encloses it in : ``, which was a problem.
     * Only one of the quotes should be used.
     *
     * What's happening Inside:
     * So in this function, if there is a raw column, then, single_quotes from that column's alias is removed.
    */
    public function escapeRawColumnAlias($column_index_number)
    {
        $column_alias = $this->input['columns'][$column_index_number]['alias_name'];
        $escaped_alias_name = str_replace("'", '', $column_alias);
        $this->input['columns'][$column_index_number]['alias_name'] = $this->input['columns'][$column_index_number]['name'] = $escaped_alias_name;
    }

    /**
     * @param $dateColumnsIndex
     * @return $this
     */
    public function setAllDateColumns($dateColumnsIndex)
    {
        if (!$this->isCollection()) {
            return $this;
        }

        $dateColumnsIndex = $this->stringToArray($dateColumnsIndex);
        foreach ($dateColumnsIndex as $column_index) {
            $column_index = intval($column_index) + $this->reserve_column_count;
            $this->enableDate($column_index);
        }
        return $this;
    }

    /**
     * Global Filter for all the columns using LIKE Operator
     */
    public function processGlobalFilterInCollection()
    {
        $collections[] = $this->collection->all();

        for ($column_index = 0; $column_index < $this->getCountOfColumns(); $column_index++) {

            if (!$this->isSearchable($column_index)) {

                continue;
            }

            $collections[] = $this->collection->filter(function ($row) use ($column_index) {
                return $this->handleCollectionOperator($row, $column_index, $this->getGlobalSearchValue(), 'LIKE');
            })->all();
        }

        $this->collection = $this->mergeAllCollections($collections);
    }


    /**
     * Global Filter for all the columns using LIKE Operator
     */
    public function processGlobalFilterInQuery()
    {
        $search_value = $this->prepareLikeValue($this->getGlobalSearchValue());

        if (empty($this->having_columns_indexes)) {
            $this->query->where(function ($query) use ($search_value) {
                $this->prepareQueryForGlobalSearch($search_value, $query);
            });

            return true;
        }

        $this->prepareQueryForGlobalSearch($search_value, $this->query, true);

        /* Handled Having Columns And*/
        if (!empty($this->having_columns_names_in_filter)) {
            $this->query->havingRaw(implode(' OR ', $this->having_columns_names_in_filter));
        }

        return true;
    }

    /**
     * process custom filter as passed in url
     */
    public function processCustomFilter()
    {
        $custom_filter = $this->getCustomFilter();

        foreach ($custom_filter as $column_index => $operators) {

            if (empty($operators)) {
                continue;
            }

            /*if (!$this->isSearchable($column_index)) {
                continue;
            }*/

            $this->resetRawColumns();
            $this->resetHavingColumns();

            $this->processOperatorsInFilter($column_index, $operators);
        }
    }

    /**
     * @param $column_name
     * @param string $type
     * @return mixed
     */
    public function extractColumnName($column_name, $type = '')
    {
        $column_name = preg_replace('!\s+!', ' ', $column_name);
        $column_name = trim(str_replace(', ', ',', $column_name));
        $column_components = explode(' ', $column_name);
        $alias = array_pop($column_components);

        if ($type == 'alias') {
            /* Return Column alias Name; */
            return $alias;
        }

        /*Pop Up AS from query */
        $as = array_pop($column_components);

        $column_name = implode(' ', $column_components);

        /* Return Column  Name; */
        return $column_name;
    }

    public function extractAliasName($column_name)
    {
        return $this->extractColumnName($column_name, "alias");
    }

    public function isEnabledDrawCheckbox()
    {
        if (empty($this->columns[$this->check_box_column_index])) {
            return false;
        }
        return $this->input['columns'][$this->check_box_column_index]['data'] == $this->check_box_column_name;
    }

    /**
     * Set All column Names If not passed from javascript side
     */
    public function setConditionForCheckboxColumn()
    {
        $this->setColumnName($this->check_box_column_index, $this->check_box_column_name);
        $this->increaseReserveColumnCount();

        $this->addColumn($this->check_box_column_name, function ($data) {
            return '<input type="checkbox" class="mv_single_checkbox" />';
        });
    }

    /**
     * For Collection if results are empty then empty the array, otherwise BaseEngine from Yajra/Datatable gives exception
     *
     */
    public function setDefaultOrderForEmptyResults()
    {
        $order_cols = $this->input['order'];
        foreach ($order_cols as $order_no) {
            $order_col = (int)$order_no['column'];
            $this->columns[$order_col] = '';
        }
    }

    /**
     * Set All column Names If not passed from javascript side
     */
    public function setAllColumnNames()
    {
        $columns = $this->columns;

        for ($column_index = 0; $column_index < $this->getCountOfColumns(); $column_index++) {

            if (!empty($this->getColumnName($column_index))) {
                $this->setAliasName($column_index, $this->getColumnName($column_index));
                continue;
            }

            if (empty($columns[$this->correctColumnIndex($column_index)])) {
                continue;
            }

            if (empty($this->getColumnName($column_index))) {
                $this->setColumnName($column_index, $columns[$this->correctColumnIndex($column_index)]);
                $this->setAliasName($column_index, $columns[$this->correctColumnIndex($column_index)]);
            }

            if ($this->hasAlias($columns[$this->correctColumnIndex($column_index)])) {
                $this->setColumnName($column_index, $this->extractColumnName($columns[$this->correctColumnIndex($column_index)]));
                $this->setAliasName($column_index, $this->extractAliasName($columns[$this->correctColumnIndex($column_index)]));
            }
        }
    }

    /**
     * @param $arrayOfElements
     * @return array
     */
    public function stringToArray($arrayOfElements)
    {
        if (!is_array($arrayOfElements)) {
            $elements = explode(',', $arrayOfElements);
            return array_map('trim', $elements);
        }
        return $arrayOfElements;
    }


    /********************ALL Protected  Methods are Down************************************/

    /**
     * Check wheather given column name exists Or not
     *
     * @param string $column_name
     * @return bool
     */
    protected function isAliasColumnNameExists($column_name)
    {
        $count_of_column_name = $this->getCountOfColumns();
        for ($i = 0; $i < $count_of_column_name; $i++) {
            if ($column_name == $this->getAliasName($i)) {
                return true;
            }
        }
        return false;
    }

    protected function getCountOfColumns()
    {
        return count($this->input['columns']);
    }

    protected function correctColumnIndex($column_index)
    {
        return $column_index - $this->reserve_column_count;
    }

    protected function increaseReserveColumnCount()
    {
        $this->reserve_column_count++;
    }

    protected function setColumnName($column_index, $column_name)
    {
        $this->input['columns'][$column_index]['name'] = trim($column_name);
    }

    protected function getColumnName($column_index)
    {
        return $this->input['columns'][$column_index]['name'];
    }

    protected function hasAlias($column)
    {
        return Str::contains(Str::upper($column), ' AS ');
    }

    protected function setAliasName($column_index, $alias_name)
    {
        $this->input['columns'][$column_index]['alias_name'] = trim($alias_name);
    }

    protected function getAliasName($column_index)
    {
    	try{
		    return $this->input['columns'][$column_index]['alias_name'];

	    }catch (\Exception $e){
		    dd($this->input['columns'],$column_index);

	    }
    }

    protected function setRawColumnName($column_index, $column)
    {
        $this->input['columns'][$column_index]['rawCol'] = $column;
        $this->raw_columns_indexes[] = $column_index;
    }

    protected function getRawColumnName($column_index)
    {
        return $this->input['columns'][$column_index]['rawCol'];
    }

    protected function existsRawColumnName($column_index)
    {
        return !empty($this->input['columns'][$column_index]['rawCol']);
    }

    protected function isSearchable($column_index)
    {
        return $this->input['columns'][$column_index]['searchable'] == "true";
    }

    protected function existsGlobalSearchValue()
    {
        return !empty($this->input['search']['value']);
    }

    protected function existsSearchValue($column_index)
    {
        return !empty($this->input['columns'][$column_index]['search']['value']);
    }

    protected function enableSearchable($column_index)
    {
        return $this->input['columns'][$column_index]['searchable'] = "true";
    }

    protected function disableSearchable($column_index)
    {
        $this->input['columns'][$column_index]['searchable'] = "false";
    }

    protected function disableSortable($column_index)
    {
        $this->input['columns'][$column_index]['orderable'] = "false";
    }

    protected function enableHaving($column_index)
    {
        $this->input['columns'][$column_index]['having'] = "true";
        $this->having_columns_indexes[] = $column_index;
    }

    protected function enableDate($column_index)
    {
        $this->input['columns'][$column_index]['date'] = "true";
    }

    protected function existsHaving($column_index)
    {
        return !empty($this->input['columns'][$column_index]['having']);
    }

    protected function existsDate($column_index)
    {
        return !empty($this->input['columns'][$column_index]['date']);
    }

    protected function getGlobalSearchValue()
    {
        return $this->input['search']['value'];
    }

    protected function setGlobalSearchValue($value)
    {
        $this->input['search']['value'] = $value;
    }

    protected function getSearchValue($column_index)
    {
        return $this->input['columns'][$column_index]['search']['value'];
    }

    protected function setSearchValue($column_index, $search_value)
    {
        $this->input['columns'][$column_index]['search']['value'] = $search_value;
    }

    protected function existsCustomFilter()
    {
        return !empty($this->input['maximizerFilter']);
    }

    protected function getCustomFilter()
    {
        return $this->input['maximizerFilter'];
    }

    protected function resetCustomFilter()
    {
        $this->input['maximizerFilter'] = [];
    }

    protected function prepareLikeValue($value)
    {
        return '%' . $value . '%';
    }

    protected function prepareStartsWithValue($value)
    {
        return $value . '%';
    }

    protected function prepareEndsWithValue($value)
    {
        return '%' . $value;
    }

    protected function prepareIfLikeOperator($operator, $search_value)
    {
        if ($operator == 'LIKE' || $operator == 'NOT LIKE') {
            return $this->prepareLikeValue($search_value);
        }

        if ($operator == 'Starts With') {
            return $this->prepareStartsWithValue($search_value);
        }

        if ($operator == 'Ends With') {
            return $this->prepareEndsWithValue($search_value);
        }

        return $search_value;
    }

    protected function resetRawColumns()
    {
        $this->or_raw_columns_names = [];
        $this->raw_columns_values = [];
    }

    protected function resetHavingColumns()
    {
        $this->or_having_columns_names = [];
        $this->having_columns_values = [];
    }

    /********************ALL Private Methods Down************************************/

    /**
     * @param $search_value
     * @param $query
     * @param $use_global_having
     * @return bool
     */
    private function prepareQueryForGlobalSearch($search_value, &$query, $use_global_having = false)
    {
        /* Reset All Default Values */
        $this->resetRawColumns();
        $this->resetHavingColumns();

        $this->having_columns_names_in_filter = [];
        $this->raw_columns_names_in_filter = [];
        $this->concat_condition_in_filter = '';

        for ($column_index = 0; $column_index < $this->getCountOfColumns(); $column_index++) {
            if (!$this->isSearchable($column_index)) {
                continue;
            }

            if ($use_global_having) {
                $this->handleHavingColumnsForGlobalSearch($column_index, 'LIKE', $search_value);
                continue;
            }

            if ($this->handleRawColumns($column_index, 'LIKE', $search_value)) {
                continue;
            }

            $query->orWhere($this->getColumnName($column_index), 'LIKE', $search_value);
        }

        /* Handled Raw Columns*/
        if (!empty($this->raw_columns_names_in_filter)) {
            $query->orWhereRaw(implode(' OR ', $this->raw_columns_names_in_filter), $this->raw_columns_values);
        }
    }

    /**
     * @param $column_index
     * @param $concat_columns
     * @return string
     */
    private function prepareConcatCondition($column_index, $concat_columns)
    {
        if (!$this->existsRawColumnName($column_index)) {
            return '';
        }

        if ($concat_columns == '') {
            return '';
        }

        $concat_columns_indexes = explode("_", $concat_columns);

        /* In last concat delimiter expected to passed, so deducting from column count */
        $concat_columns_indexes_length = (count($concat_columns_indexes) - 1);

        if ($concat_columns_indexes_length == 0) {
            return '';
        }

        $concat_condition = str_ireplace(array('concat(', ' ', '"'), array('', '', '\''), $this->getRawColumnName($column_index));
        $concat_condition = rtrim($concat_condition, ')');

        $delimiter = ",'" . $concat_columns_indexes[$concat_columns_indexes_length] . "',";
        $column_names = explode($delimiter, $concat_condition);

        if (count($column_names) == 1) {
            return '';
        }

        $concat_conditions = [];

        for ($k = 0; $k < $concat_columns_indexes_length; $k++) {

            if (empty($column_names[$concat_columns_indexes[$k]])) {
                continue;
            }
            $concat_conditions[] = trim($column_names[$concat_columns_indexes[$k]]);
        }


        if (empty($concat_conditions)) {
            return '';
        }

        if (count($concat_conditions) == 0) {
            return '';
        }

        $pos = strpos($concat_conditions[0], ',');
        if ($pos === false && count($concat_conditions) == 1) {
            return $concat_conditions[0];
        }

        return "CONCAT(" . implode($delimiter, $concat_conditions) . ")";
    }

    /**
     * @param $column_index
     * @param $operator
     * @return string
     */
    private function prepareConcatQuery($column_index, $operator)
    {
        if (empty($this->concat_condition_in_filter)) {
            return $this->getRawColumnName($column_index) . ' ' . $operator;
        }

        return $this->concat_condition_in_filter . ' ' . $operator;
    }

    /**
     * @param $column_index
     * @param $operator
     * @param string $condition_value
     * @return bool
     */
    private function handleRawColumns($column_index, $operator, $condition_value = '[!empty!]')
    {
        if (!$this->existsRawColumnName($column_index)) {
            return false;
        }

        $condition_statement = $this->prepareConcatQuery($column_index, $operator);

        if ($condition_value != '[!empty!]') {
            $condition_statement .= ' ?';
            $this->raw_columns_values[] = $condition_value;
        }

        $this->raw_columns_names_in_filter[] = $condition_statement;
        return true;
    }

    /**
     * @param $column_index
     * @param $operator
     * @param string $condition_value
     * @return bool
     */
    private function handleHavingColumns($column_index, $operator, $condition_value = '[!empty!]')
    {
        if (!$this->existsHaving($column_index) || !empty($this->concat_condition_in_filter)) {
            return false;
        }

        $condition_statement = '`'. $this->getAliasName($column_index) . '`'. ' ' . $operator;

        if ($condition_value != '[!empty!]') {
            $condition_statement .= ' ?';
            $this->having_columns_values[] = $condition_value;
        }

        $this->having_columns_names_in_filter[] = $condition_statement;
        return true;
    }


    /**
     * @param $column_index
     * @param $operator
     * @param $condition_value
     * @return bool
     */
    private function handleHavingColumnsForGlobalSearch($column_index, $operator, $condition_value)
    {
        //Added By Nikhil
        //The Alias Name is wrapped in Single quotes, since there is possible string Aliases that will have symbols like '^'. Eg: Used in SERP
        $condition_statement = "`". $this->getAliasName($column_index) ."`" . ' ' . $operator . ' "' . $condition_value . '" COLLATE utf8mb4_unicode_ci';

        $this->having_columns_names_in_filter[] = $condition_statement;

        return true;
    }

    /**
     * @param $column_index
     * @param $query
     */
    private function handleIsNull($column_index, &$query)
    {
        if ($this->handleRawColumns($column_index, 'IS NULL')) {
            return;
        }
        if ($this->handleHavingColumns($column_index, 'IS NULL')) {
            return;
        }

        $query->orWhereNull($this->getColumnName($column_index));
    }

    /**
     * @param $column_index
     * @param $query
     */
    private function handleIsNotNull($column_index, &$query)
    {
        if ($this->handleRawColumns($column_index, 'IS NOT NULL')) {
            return;
        }
        if ($this->handleHavingColumns($column_index, 'IS NOT NULL')) {
            return;
        }
        $query->orWhereNotNull($this->getColumnName($column_index));
    }

    /**
     * @param $search_value
     * @param $operator
     * @param $column_index
     * @param $query
     * @param $all_values
     * @return bool
     */
    private function processCustomQuery($search_value, $operator, $column_index, &$query, $all_values = '')
    {

        if ($this->equivalentToNullCondition($search_value, $operator)) {
            $this->handleIsNull($column_index, $query);
            return true;
        }

        if ($this->equivalentToNotNullCondition($search_value, $operator)) {
            $this->handleIsNotNull($column_index, $query);
            return true;
        }

        /* For empty Value Check */
        if ($search_value == '[!empty!]') {
            /* Make It searchValue blank */
            $search_value = '';
            if ($operator != '=' && $operator != '<>') {
                return false; /* Not Possible to run query other then EqualTo & NotEqualTo Operator If $search_value is blank */
            }
        }

        $search_value = $this->prepareIfLikeOperator($operator, $search_value);

        $operator = $operator == "Starts With" || $operator == "Ends With" ? "LIKE" : $operator;

        if($operator == "Contains (multiple)" || $operator == "Does not contain (multiple)")
        {
            if($operator == "Does not contain (multiple)")
            {
                $operator = "NOT LIKE";
                $all_values = 'all';
            }
            else
            {
                $operator = "LIKE";
            }

            $search_values_array = explode("\n", $search_value);
            foreach ($search_values_array as $search_value)
            {
                $search_value = trim($search_value);
                $search_value = $this->prepareIfLikeOperator($operator, $search_value);

                if ($this->handleHavingColumns($column_index, $operator, $search_value)) {
                    continue;
                }

                if ($this->handleRawColumns($column_index, $operator, $search_value)) {
                    continue;
                }

                if($all_values == "all")
                    $query->where($this->getColumnName($column_index), $operator, $search_value);
                else
                    $query->orWhere($this->getColumnName($column_index), $operator, $search_value);
            }

        } else
        {
            if ($this->handleHavingColumns($column_index, $operator, $search_value)) {
                return true;
            }

            if ($this->handleRawColumns($column_index, $operator, $search_value)) {
                return true;
            }

            $query->orWhere($this->getColumnName($column_index), $operator, $search_value);
        }

        return true;
    }

    /**
     * @param $search_results
     * @param $operator
     * @param $column_index
     * @param $query
     */
    private function prepareQuery($search_results, $operator, $column_index, &$query)
    {
        $this->having_columns_names_in_filter = [];
        $this->raw_columns_names_in_filter = [];

        foreach ($search_results as $search_concat) {

            if (empty($search_concat)) {
                continue;
            }

            $search_value = $search_concat['search_value'];
            $concat_columns = $search_concat['concat_columns'];
            $all_values = isset($search_concat['all_values']) ? $search_concat['all_values'] : '';

            $this->concat_condition_in_filter = $this->prepareConcatCondition($column_index, $concat_columns);

            $this->processCustomQuery($search_value, $operator, $column_index, $query, $all_values);
        }

        /* Handled Raw Columns for filterColumn OR */
        if (!empty($this->raw_columns_names_in_filter)) {
            $this->or_raw_columns_names[] = '(' . implode(' OR ', $this->raw_columns_names_in_filter) . ')';
        }

        /* Handled Having Columns OR*/
        if (!empty($this->having_columns_names_in_filter)) {
            $this->or_having_columns_names[] = '(' . implode(' OR ', $this->having_columns_names_in_filter) . ')';
        }
    }

    /**
     * @param $row
     * @param $column_index
     * @param $search_value
     * @param $operator
     * @param $all_values
     * @return bool
     */
    private function handleCollectionOperator($row, $column_index, $search_value, $operator, $all_values = '')
    {
        //Added for multiple keywords : By Nikhil - Separator: ' || '
        if(!is_bool(strpos($search_value, ' || ')))
        {
            $all_search_values = explode(' || ', $search_value);
            foreach ($all_search_values as $search_value_datum)
            {
                $check_status = $this->handleCollectionOperator($row, $column_index, $search_value_datum, $operator, $all_values);
                if($check_status)
                {
                    return $check_status;
                }
            }
            return false;
        }

        if(is_array($row)){
	        $operand = $row[$this->getAliasName($column_index)];
        }else{
	        $operand = $row->{$this->getAliasName($column_index)};
        }


        if(is_array($operand)){
            $operand = implode(", ", $operand);
        }

        if ($this->existsDate($column_index)) {

            if((string)(int)$operand == $operand) { //If it's a timestamp make it a date
                $operand = date('Y-m-d', $operand);
            }else{
                $operand = date('Y-m-d', strtotime($operand)); //Make the date in Y-m-d format
            }
        }

        if ($operator == '=') {
            return Str::lower($operand) == Str::lower($search_value) ? true : false;
        }

        if ($operator == '<>') {
            return Str::lower($operand) != Str::lower($search_value) ? true : false;
        }

        if ($operator == 'LIKE') {
            return Str::contains(Str::lower(' '. $operand . ' '), Str::lower($search_value)) ? true : false;
        }

        if ($operator == 'NOT LIKE') {
            return Str::contains(Str::lower($operand), Str::lower($search_value)) ? false : true;
        }

        if ($operator == 'Starts With') {
            return Str::startsWith(Str::lower($operand), Str::lower($search_value)) ? true : false;
        }

        if ($operator == 'Ends With') {
            return Str::endsWith(Str::lower($operand), Str::lower($search_value)) ? true : false;
        }

        if ($operator == 'Contains (multiple)') {
            $status = true;
            $search_value_array = explode("\n", $search_value);
            foreach ($search_value_array as $search_value)
            {
                $search_value = trim($search_value);
                if($all_values == 'all'){
                    if(!Str::contains(Str::lower(' ' . $operand . ' '), Str::lower($search_value)))
                        return false;
                }else{
                    $status = false;

                    if(Str::contains(Str::lower(' ' . $operand . ' '), Str::lower($search_value)))
                        return true;
                }
            }

            return $status;
        }

        if ($operator == 'Does not contain (multiple)') {
            $search_value_array = explode("\n", $search_value);
            foreach ($search_value_array as $search_value)
            {
                $search_value = trim($search_value);
                if(Str::contains(Str::lower(' ' . $operand . ' '), Str::lower($search_value)))
                    return false;
            }

            return true;
        }

        if (empty($operand)) {
            return false;
        }

        /* For Rest type of Operators */
        if ($this->existsDate($column_index)) {
            $left = new \DateTime($operand);
            $right = new \DateTime($search_value);
        } else {
            $left = doubleval($operand);
            $right = doubleval($search_value);
        }

        //print_data('$left: ' . $left. " $operator " . ' $right: '. $right);

        switch ($operator) {
            case '>':
                return $left > $right;
            case '>=':
                return $left >= $right;
            case '<':
                return $left < $right;
            case '<=':
                return $left <= $right;
        }

        return false;
    }


    /**
     * @param $column_index
     * @param $search_results
     * @param $operator
     */
    private function processCollectionOperation($column_index, $search_results, $operator)
    {
        $collections = [];

        $operator = $operator == 'Is Empty' ? '=' : $operator;
        $operator = $operator == 'Is Not Empty' ? '<>' : $operator;

        foreach ($search_results as $search_concat) {

            if (empty($search_concat)) {
                continue;
            }

            $search_value = $search_concat['search_value'];
            $search_value = $operator == 'Is Empty' || $operator == 'Is Not Empty' ? '' : $search_value;
            $all_values = (isset($search_concat['all_values'])) ? $search_concat['all_values'] : '';

            $collection_temp = $this->collection->filter(function ($row) use ($search_value, $column_index, $operator, $all_values) {
                return $this->handleCollectionOperator($row, $column_index, $search_value, $operator, $all_values);
            })->all();

            $collections[] = $collection_temp;
        }

        $this->collection = $this->mergeAllCollections($collections, false);
    }


    /**
     * @param array $base_array
     * @param array $default_array
     * @return array
     */
    private function merge_array_extend(array $base_array, array $default_array)
    {
        $merged_array = $base_array;
        foreach ($default_array as $key => $item) {
            if (!isset($merged_array[$key])) {
                $merged_array[$key] = $item;
            }
        }
        return $merged_array;
    }

    /**
     * @param $collections
     * @param $do_array_shift
     * @return Collection
     */
    private function mergeAllCollections($collections, $do_array_shift = true)
    {
        //Added By Nikhil
        //$collections[0] is always the source array, we don't need that inside the result to get merged with -- only for global search
        if ($do_array_shift && $this->existsGlobalSearchValue())
            array_shift($collections);

        $merge_collections = [];
        foreach ($collections as $collection) {
            if (empty($collection)) {
                continue;
            }
            $merge_collections = $this->merge_array_extend($merge_collections, $collection);
        }
        return new Collection($merge_collections);
    }

    /**
     * @param $column_index
     * @param $operators
     */
    private function processOperatorsInFilter($column_index, $operators)
    {
        /**
         * Between Operator will be allowed only once. For same column, you can add only one Between Condition. Even if you add more, only first will prevail.
         * In the Below ForEach Loop: A between Operator is Dismantled appropriately to '>=' and '<=' conditions.
         *
        */
        foreach ($operators as $operator => $search_results)
        {
            //If the operator is Between
            if ($operator == 'Between')
            {
                $new_operator = $this->dismantleBetweenOperator($search_results);
                unset($operators['Between']);

                foreach ($new_operator as $opr => $opr_details)
                {
                    foreach ($opr_details as $opr_details_item)
                    {
                        $operators[$opr][] = $opr_details_item;
                    }
                }
                break;
            }
            else
            {
                continue;
            }
        }

        foreach ($operators as $operator => $search_results) {

            if (empty($search_results)) {
                continue;
            }

            $operator = $this->validateOperator($operator);

            if (is_bool($operator)) {
                continue;
            }

            if ($this->isCollection() && !$this->isSpecialFilterOperator($operator)) {
                $this->processCollectionOperation($column_index, $search_results, $operator);
                continue;
            }

            if (!$this->isSpecialFilterOperator($operator)) {
                $this->query->where(function ($query) use ($search_results, $operator, $column_index) {

                    $this->prepareQuery($search_results, $operator, $column_index, $query);

                });
            }

            /* Handled Raw Columns*/
            if (!empty($this->or_raw_columns_names)) {
                $this->query->whereRaw(implode(' AND ', $this->or_raw_columns_names), $this->raw_columns_values);
            }

            /* Handled Having Columns And*/
            if (!empty($this->or_having_columns_names)) {
                $this->query->havingRaw(implode(' AND ', $this->or_having_columns_names), $this->having_columns_values);
            }

            if ($this->isSpecialFilterOperator($operator)) {
                $this->prepareQueryForSpecialFilterOperator($search_results, $column_index);
            }
        }
    }

    public function dismantleBetweenOperator($search_results)
    {
        $return_array = [];
        $final_operators = [];

        foreach ($search_results as $search_concat)
        {
            if (empty($search_concat))
            {
                continue;
            }

            //$search_concat['search_value'] = [ 0 => '25', 1 => '35' ];
            $search_value = $search_concat['search_value'];

            //Something went wrong. If its Between -  then it should be an array
            if (!is_array($search_value))
            {
                continue;
            }

            $concat_columns = $search_concat['concat_columns'];
            $from = $search_value[0];
            $to = $search_value[1];
            //if it's a pre-defined filter, then the values will come comma-separated and so which we should explode and take the second number as vale
            if(substr_count($from,",")>0){
                $both_values = explode(",",$from);
                $from = $both_values[0];
                $to = $both_values[1];
            }

            if ($from == '' && $to == '')
            {
                continue;
            }

            if ($from == '')
            {
                $final_operators[] = [ 'operator' => '<=', 'value' => $to, 'concat_columns' => $concat_columns ];
            }
            elseif ($to == '')
            {
                $final_operators[] = [ 'operator' => '>=', 'value' => $from, 'concat_columns' => $concat_columns ];
            }
            else
            {
                $final_operators[] = [ 'operator' => '>=', 'value' => $from, 'concat_columns' => $concat_columns ];
                $final_operators[] = [ 'operator' => '<=', 'value' => $to, 'concat_columns' => $concat_columns ];
            }
        }

        foreach ($final_operators as $operator_conditional_array)
        {
            $search_value = $operator_conditional_array['value'];
            $operator = $operator_conditional_array['operator'];
            $concat_columns = $operator_conditional_array['concat_columns'];
            $return_array[$operator][] = [ 'search_value' => $search_value, 'concat_columns' => $concat_columns ];
        }

        return $return_array;
    }

    /* ToDo
     * Page join Query not working
     * tag_id => tag_campaign_id [need to update dynamically]
     * When Changing page join query update the DataTable query for managers
    */

    private function processSpecialFilterOperator($search_value, $column_index, $type, $source_type, $exclude)
    {
        $query_column_name = $this->getColumnName($column_index);

        if($query_column_name == 'tbc.tag_id')
        {
            $query_column_name = 'tbc.tag_campaign_id';
        }

        if($this->query) {
            /* 1: TagCampaignIds
               2: KeywordIds
               3: PageIds
            */
            switch ($type) {
                case 1:
                case 5:
                    if ($source_type == 2) {
                        if($exclude == 'yes')
                            $this->query->whereNotIn('ktc.tag_campaign_id', $search_value);
                        else
                            $this->query->whereIn('ktc.tag_campaign_id', $search_value);

                        if($this->isJoined($this->query, 'keywords_for_campaign_summary') === false){
                            $this->query->join('keywords_for_campaign_summary AS kfcs', 'kfcs.keyword_id', '=', $query_column_name);
                        }
                        $this->query->join('keyword_tags_for_campaign AS ktc', 'ktc.keyword_id', '=', $query_column_name)
                            ->join('tags_by_campaign AS tbc', function ($join){
                            $join->on('tbc.tag_campaign_id', '=', 'ktc.tag_campaign_id')
                                ->on('tbc.campaign_id', '=', 'kfcs.campaign_id');
                            })
                            ->groupBy('k.keyword_id');
                    } else if ($source_type == 3) {

                        if($exclude == 'yes')
                            $this->query->whereNotIn('ptc.tag_campaign_id', $search_value);
                        else
                            $this->query->whereIn('ptc.tag_campaign_id', $search_value);

                        $this->query->join('page_tags_for_campaign AS ptc', 'ptc.page_id', '=', $query_column_name)
                            ->join('tags_by_campaign AS tbc', function ($join){
                                $join->on('tbc.tag_campaign_id', '=', 'ptc.tag_campaign_id')
                                    ->on('tbc.campaign_id', '=', 'pfcs.campaign_id');
                            });
                    }
                    break;

                case 2:
                case 6:
                    if ($source_type == 1) {
                        if($exclude == 'yes')
                            $this->query->whereNotIn('ktc.keyword_id', $search_value);
                        else
                            $this->query->whereIn('ktc.keyword_id', $search_value);

                        $this->query->join('keyword_tags_for_campaign AS ktc', 'ktc.tag_campaign_id', '=', $query_column_name)
                            ->groupBy('k.keyword_id');

                    } else if ($source_type == 3) {
                        if($exclude == 'yes')
                            $this->query->whereNotIn('pkc.keyword_id', $search_value);
                        else
                            $this->query->whereIn('pkc.keyword_id', $search_value);

                        $this->query->join('page_keywords_for_campaign AS pkc', 'pkc.page_id', '=', $query_column_name)
                            ->groupBy('wp.page_id');
                    }
                    break;

                case 3:
                case 7:
                    if($exclude == 'yes')
                        $this->query->whereNotIn('wp.page_id', $search_value);
                    else
                        $this->query->whereIn('wp.page_id', $search_value);

                    if ($source_type == 1) {
                        $this->query->join('page_tags_for_campaign AS ptc', 'ptc.tag_campaign_id', '=', $query_column_name)
                            ->join('website_pages AS wp', 'ptc.page_id', '=', 'wp.page_id')
                            ->groupBy('k.keyword_id');
                    } else if ($source_type == 2) {
                        $this->query->join('page_keywords_for_campaign AS pkc', 'pkc.keyword_id', '=', $query_column_name)
                            ->groupBy('k.keyword_id');
                    }
                    break;

                case 4:
                    /* For Keyword Attributes Condition */
                    $this->query->join('keyword_common_attributes AS kca', 'kca.keyword_id', '=', $query_column_name);
                    foreach ($search_value as $key => $value) {
                        if ($value == 'q') {
                            $this->query->where('kca.question', 1);
                        }

                        if ($value == 'p') {
                            $this->query->where('kca.product', 1);
                        }

                        if ($value == 'c') {
                            $this->query->where('kca.commercial', 1);
                        }

                        if ($value == 's') {
                            $this->query->where('kca.sentiment', 1);
                        }
                    }
                    break;
            }
        }else{
            $this->processSpecialFilterOperatorForCollection($search_value, $column_index, $type, $source_type, $exclude);
        }
    }

    private function isJoined($query, $table)
    {
        $query = $query->toSql();
        return strpos($query, $table);
    }

    private function processSpecialFilterOperatorForCollection($search_value, $column_index, $type, $source_type, $exclude)
    {
        if ($this->isEnabledDrawCheckbox())
            $column_index = $column_index - 1;

        $values_to_be_filtered = [];
        $filtered_values = [];
        $filtered_data = [];
        $key = '';

        $data = array_values($this->collection->all());

        if(!empty($data))
        {
            $keys = array_keys($data[0]);
            $key = $keys[$column_index];
            foreach ($data as $values)
            {
                $values_to_be_filtered[] = $values[$key];
            }
        }

        if(!empty($values_to_be_filtered))
        {
            //Handling only tag manager because page manager and keyword manager are not in use now
            switch ($type) {
                case 1:
                case 5:
                    if ($source_type == 2) {
                        $table_name = "keyword_tags_for_campaign";
                        $column_name = "keyword_id";
                    }
                    if ($source_type == 3) {
                        $table_name = "page_tags_for_campaign";
                        $column_name = "page_id";
                    }
                    break;
            }

            $filtered_obj = $this->filterCollectionValues($table_name, $column_name, $search_value, $values_to_be_filtered)->get();
            if($filtered_obj->isNotEmpty())
            {
                foreach ($filtered_obj as $filtered_value)
                {
                    $filtered_values[] = $filtered_value->$column_name;
                }
            }

        }

        if(!empty($filtered_values))
        {
            foreach ($data as $values)
            {
                if($exclude == 'yes')
                {
                    if(!in_array($values[$key], $filtered_values))
                        $filtered_data[] = $values;
                }else{
                    if(in_array($values[$key], $filtered_values))
                        $filtered_data[] = $values;
                }
            }
        }

        $filtered_data = new Collection($filtered_data);
        $this->collection = new Collection($filtered_data);
    }

    private function filterCollectionValues($table, $column, $tag_campaign_id, $values_to_be_filtered)
    {
        return \DB::table($table)
            ->whereIn('tag_campaign_id', $tag_campaign_id)
            ->whereIn($column, $values_to_be_filtered)
            ->select([$column]);
    }

    private function prepareQueryForSpecialFilterOperator($search_results, $column_index)
    {
        foreach ($search_results as $index => $search_type) {
            if (empty($search_type)) {
                continue;
            }

            $search_value = $search_type['search_value'];
            $type = $search_type['type'];
            $exclude = $search_type['exclude'];
            $source_type = 0;
            if (!empty($search_type['source_type'])) {
                $source_type = $search_type['source_type'];
            }


            $this->processSpecialFilterOperator($search_value, $column_index, $type, $source_type, $exclude);
        }
    }

    private function isSpecialFilterOperator($operator)
    {
        return $operator == 'In Tags' || $operator == 'In Keywords' || $operator == 'In Pages' || $operator == 'k_attr' || $operator == 'Not In Tags' || $operator == 'Not In Keywords' || $operator == 'Not In Pages';
    }

    /**
     * @param $operator
     * @return bool|string
     */
    private function validateOperator($operator)
    {
        $operator = urldecode(trim($operator));

        if (empty($operator))
            return false;

        switch ($operator) {
            case '=':
                return '=';
            case 'Not Equals':
                return '<>';
            case '<':
                return '<';
            case '>':
                return '>';
            case '<=':
                return '<=';
            case '>=':
                return '>=';
            case 'Contains':
                return 'LIKE';
            case 'Does not contain':
                return 'NOT LIKE';
            case 'Starts With':
            case 'Ends With':
            case 'Is Empty':
            case 'Is Not Empty':
            case 'In Tags':
            case 'In Keywords':
            case 'In Pages':
            case 'k_attr':
            case 'Not In Tags':
            case 'Not In Keywords':
            case 'Not In Pages':
            case 'Contains (multiple)':
            case 'Does not contain (multiple)':
                return $operator;
            default:
                return false;
        }
    }

    /**
     * @param $search_value
     * @param $operator
     * @return bool
     */
    private function equivalentToNullCondition($search_value, $operator)
    {
        return $operator == 'Is Empty' || ($search_value == '[!empty!]' && $operator == '=');
    }

    /**
     * @param $search_value
     * @param $operator
     * @return bool
     */
    private function equivalentToNotNullCondition($search_value, $operator)
    {
        return $operator == 'Is Not Empty' || ($search_value == '[!empty!]' && $operator == '<>');
    }

    /* Any case Yajra should not handle having columns */
    private function disableSearchableForAllHavingColumns()
    {
        $having_columns = $this->having_columns_indexes;
        foreach ($having_columns as $column_index) {
            $this->disableSearchable($column_index);
        }
    }

    /* Any case Yajra should not handle raw columns */
    private function disableSearchableForAllRawColumns()
    {
        $raw_columns = $this->raw_columns_indexes;
        foreach ($raw_columns as $column_index) {
            $this->disableSearchable($column_index);
        }
    }
}