<?php

function recursive_array_extend(&$base_array, &$default_array)
{
    foreach ($default_array as $key => $item) {
        if (!isset($base_array[$key])) {
            $base_array[$key] = $default_array[$key];
        }
        if (is_array($item)) {
            recursive_array_extend($base_array[$key], $item);
        }
    }
}

/*
 * Sample Input:
 * $a = ['Fruits'=>'Apple','Animal'=>'Dog']
 *
 * array_move("Animal","0",$a)
 *
 * Expected Output:
 *
 * ['Animal'=>'Dog','Fruits'=>'Apple']
 * */
function array_move($which, $where, $array)
{
    $tmpWhich = $which;
    $j = 0;
    $keys = array_keys($array);

    for ($i = 0; $i < count($array); $i++) {
        if ($keys[$i] == $tmpWhich)
            $tmpWhich = $j;
        else
            $j++;
    }
    $tmp = array_splice($array, $tmpWhich, 1);
    array_splice_assoc($array, $where, 0, $tmp);
    return $array;
}

/**
 * @param $which_pos
 * @param $where_pos
 * @param $array
 * @return mixed
 */
function array_move_for_non_assoc($which_pos, $where_pos, &$array)
{
    $tmp = $array[$which_pos];
    $array[$which_pos] = $array[$where_pos];
    $array[$where_pos] = $tmp;
}

/**
 * Supporting function for array_move, refer to comment in array_move
 * @param $input
 * @param $offset
 * @param $length
 * @param $replacement
 */
function array_splice_assoc(&$input, $offset, $length, $replacement)
{
    $replacement = (array)$replacement;
    $key_indices = array_flip(array_keys($input));
    if (isset($input[$offset]) && is_string($offset)) {
        $offset = $key_indices[$offset];
    }
    if (isset($input[$length]) && is_string($length)) {
        $length = $key_indices[$length] - $offset;
    }

    $input = array_slice($input, 0, $offset, TRUE)
        + $replacement
        + array_slice($input, $offset + $length, NULL, TRUE);
}

/*
 * Sample Input:
 * $a = ['Fruits'=>['Apple', 'orange'],'Animal'=>'Dog']
 *
 * array_depth($a)
 *
 * Expected Output: 1
 * */
function array_depth(array $array)
{
    $max_depth = 1;
    foreach ($array as $value) {
        if (is_array($value) && !empty($value)) {
            $depth = array_depth($value) + 1;
            if ($depth > $max_depth) {
                $max_depth = $depth;
            }
        }
    }
    return $max_depth;
}

/*
 * Sample Input:
 * $a = ['Fruits'=>['Apple', 'orange'],'Animal'=>'Dog']
 *
 * array_count_recursive($a)
 *
 * Expected Output: 3
 * */
function array_count_recursive(array $array)
{
    $count = 0;
    support_for_array_count_recursive($array, $count);
    return $count;
}

/**
 *  Supporting function for array_count_recursive
 * @param array $array
 * @param $count
 */
function support_for_array_count_recursive(array $array, &$count)
{
    foreach ($array as $item) {
        if (is_array($item) && !empty($item)) {
            support_for_array_count_recursive($item, $count);
        } else {
            $count++;
        }

    }
}

/**
 * @param array $main_array
 * @param array $new_array_to_be_merged
 * @return array
 */
function array_merge_if_already_exist(array $main_array, array $new_array_to_be_merged)
{
    if (empty($main_array)) {
        return $new_array_to_be_merged;
    }

    return array_merge($main_array, $new_array_to_be_merged);
}

/**
 * @param array $sorted_array_of_numeric_data
 * @param $include_minimum_number
 * @param $exclude_maximum_number
 * @return array
 */
function array_of_elements_fall_into_given_range(array $sorted_array_of_numeric_data, $include_minimum_number, $exclude_maximum_number = false)
{
    $prepared_array_numeric_data = [];
    $count_of_data_array = count($sorted_array_of_numeric_data);
    $left_index = 0;
    $right_index = $count_of_data_array - 1;
    while ($left_index <= $right_index) {
        $mid_index = intval(($left_index + $right_index) / 2);
        if ($include_minimum_number > $sorted_array_of_numeric_data[$mid_index]) {
            $left_index = $mid_index + 1;
            continue;
        }

        if ($include_minimum_number > $sorted_array_of_numeric_data[$left_index]) {
            $left_index++;
            continue;
        }

        if (!is_bool($exclude_maximum_number) && $exclude_maximum_number <= $sorted_array_of_numeric_data[$left_index]) {
            break;
        }

        if( is_numeric( $sorted_array_of_numeric_data[$left_index]) )
        {
            $prepared_array_numeric_data[] = $sorted_array_of_numeric_data[$left_index];
        }
        $left_index++;
    }
    return $prepared_array_numeric_data;
}

/**
 * @param $common_array
 * @param $main_array
 * @return array
 */
function merge_common_array_to_all_elements_of_main_array($common_array, $main_array)
{
    return array_map(function ($element) use ($common_array, $main_array) {
        return array_merge($element, $common_array);
    }, $main_array);
}

function convert_redis_value_to_assoc_array($redis_variable)
{
    $assoc_array = [];
    $redis_key_value_pair = explode('|', $redis_variable);
    foreach($redis_key_value_pair as $variable)
    {
        $key_value = explode('^', $variable);
        $assoc_array[ $key_value[0] ] = $key_value[1];
    }

    return $assoc_array;
}

function convert_assoc_array_to_redis_value($assoc_array)
{
    $redis_key_value_pair = [];
    foreach($assoc_array as $key=>$value)
    {
        $redis_key_value_pair[] = $key.'^'.$value;
    }

    return implode('|', $redis_key_value_pair);
}

function remove_utf8_bom($text)
{
    $bom = pack('H*','EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);
    return $text;
}

function objectToArray($object_item) {
    if (is_object($object_item)) {
        // Gets the properties of the given object
        // with get_object_vars function
        $object_item = get_object_vars($object_item);
    }

    if (is_array($object_item)) {
        /*
        * Return array converted to object
        * Using __FUNCTION__ (Magic constant)
        * for recursive call
        */
        return array_map(__FUNCTION__, $object_item);
    }
    else {
        return $object_item;
    }
}
function print_data($data, $name='Unknown Data Name')
{
    $name = ucwords($name);

    echo "$name:<pre>";
    print_r($data);
    echo "</pre>";
}
function unset_indices(array &$data_array, array $index_array)
{
    $keys = array_keys($data_array);

    foreach ($index_array as $index_number_or_name)
    {
        if (!isset($data_array[$index_number_or_name]))
        {
            $index_number_or_name = $keys[$index_number_or_name];
        }

        unset($data_array[$index_number_or_name]);
    }
}
function get_common_words(array $array_of_statement)
{
    $array_of_statement_with_word_length = [];
    foreach($array_of_statement as $statement)
    {
        $word_count = str_word_count($statement);
        $array_of_statement_with_word_length[ ] = $statement;
        $word_count--;
        while($word_count > 0)
        {
            $array_of_statement_with_word_length = array_merge( $array_of_statement_with_word_length, break_words_by_given_length($statement, $word_count) );
            $word_count--;
        }
    }

    $new_keywords = array_count_values( $array_of_statement_with_word_length );

    $latest_new_keywords = [];
    foreach($new_keywords as $key=>$value)
    {
        if($value > 1)
        {
            $latest_new_keywords[$key] = $value;
        }
    }
    return $latest_new_keywords;
}

function break_words_by_given_length($from_array, $word_length)
{
    $new_from_array = [];
    $keyword_array = explode(' ',$from_array);
    $count_of_keyword_array = count($keyword_array);
    $index=0;
    while($count_of_keyword_array >= $word_length)
    {
        $new_keyword_array = array_slice($keyword_array, $index, $word_length);
        $new_from_array[] = implode(' ', $new_keyword_array);
        $count_of_keyword_array--;
        $index++;
    }
    return $new_from_array;
}

/**
 * Input:
 *  $haystack : array to search in
 *  $needle : The strig to search for
 *
 * Output:
 *  array of [ 'item' => <the_matched_haystack_item>,
 *              'pos' => <position_of_haystack_item_in_haystack_array>,
 *              'sub_pos' => 'position_of_needle_in_matched_haystack_item' ],

 *          $matched_haystack_items: all_matched_haystack_items
 *
 * @param array $haystack
 * @param $needle
 * @return array
 *
 */
function array_str_pos(array $haystack, $needle)
{
    $positions = [];
    $matched_haystack_items = [];
    $matched_words = [];
    $needle_length = strlen($needle);
    foreach ($haystack as $haystack_index => $haystack_datum)
    {
        $pos = strpos($haystack_datum, $needle);
        if (!is_bool($pos))
        {

            $tail = explode(' ', substr($haystack_datum, $pos + $needle_length))[0];
            $matched_word = substr($haystack_datum, $pos, $needle_length) . $tail;
            $positions[] = [ 'item' => $haystack_datum, 'pos' => $haystack_index, 'sub_pos' => $pos, 'matched_word' => $matched_word ];
            if(!in_array($matched_word, $matched_words))
            {
                $matched_words[] = $matched_word;
            }

            if(!in_array($haystack_datum, $matched_haystack_items))
            {
                $matched_haystack_items[] = $haystack_datum;
            }
        }
    }

    return [$positions, $matched_haystack_items, $matched_words ];
}

function array_average($data)
{
    //Single value - avg is same value
    if(!is_array($data) && !empty($data) && is_int($data))
    {
        return $data;
    }

    if(!is_array($data))
    {
        return 0;
    }

    $sum = array_sum(array_values($data));
    $avg = $sum/count($data);

    return round($avg, 2);
}

function array_merge_append(array $array_1, array $array_2)
{
    $append_array = [];
    foreach ($array_1 as $key => $value)
    {
        if(isset($array_2[$key]))
        {
            $result = [];
            if (is_array($value) && is_array($array_2[$key]))
            {
                $result = array_merge($value, $array_2[$key]);
            }
            else
            {
                $result[] = $value;
                $result[] = $array_2[$key];
            }

            $append_array[$key] = $result;
        }
        else
        {
            $append_array[$key] = $value;
        }
    }

    foreach ($array_2 as $key => $value)
    {
        if(!isset($append_array[$key]))
        {
            $append_array[$key] = $value;
        }
    }

    return $append_array;
}

function array_unshift_assoc(&$arr, $key, $val)
{
	$arr = array_reverse($arr, true);
	$arr[$key] = $val;
	$arr = array_reverse($arr, true);
	return $arr;
}