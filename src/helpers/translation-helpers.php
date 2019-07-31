<?php

/**
 * Will return true if the path exists, else false
 * E.g of usage: if(trans_exists("validation.required")) { //do whatever if it exists } //trans_exists("validation.required") will return true since it exists
 * trans_exists("invalid-path.invalid-field") will return false.
 * @param $path
 * @return bool
 */
function trans_exists($path)
{
    return trans($path) == $path ? false : true;
}

/**
 * This will translate the keys of a two-dimensional array by getting the translated version of the defined key and returning a new array with the translated keys.
 * Will typically be used in DisplayService files when defining arrays.
 * Example:
 * $meta_issues_array = [
 * 'missing_meta_titles' => '1',
 * 'missing_meta_descriptions' => '9',
 * 'duplicate_titles' => '2',
 * 'duplicate_meta_descriptions' => '5',
 * 'long_titles' => '3',
 * 'Long_meta_descriptions' => '0',
 * ];
 * $return_data = [
 * 'meta_issues' => translate_array($meta_issues_array,'app/website-audit') //will return trans('app/website-audit.missing_meta_titles')=>'1', etc.
 * ]
 * @param $array Two-dimensional array
 * @param $lang_path The path of the language file, e.g. app/module-name
 * @return array
 * @throws Exception An exception will be thrown if an invalid trans path is passed
 * */
function translate_array(array $array, $lang_path)
{
    $new_array = [];
    foreach ($array as $key => $value) {
        $trans_path = $lang_path . "." . $key;
        if (trans_exists($trans_path)) {
            $new_key = trans($trans_path);
        }else{
            //throw new Exception("Invalid trans path $trans_path");
            $new_key = ucwords(str_replace("_"," ",$key));
            if(str_contains($new_key, "^" )){
                $new_key = str_before_first("^", $new_key);
            }
        }

        $new_array[$new_key] = $value;
    }
    return $new_array;
}


/**
 * This is used to translate a multi-dimensional array will assume that a pipe symbol may be passed as being done by the DataTable class. It will translate the "keys" and "values" by taking the first part of the string after exploding by the delimiter and return two arrays: 1) array converted with translations, 2) a new array of just the aliases (translation keys passed)
 *
 * E.g.  $Meta_Issues_columns = [
 * 'page_url|data-filter-type:text|150px' => [],
 * 'parent_title_text|class:text-center' => ['title_text|data-filter-type:text|100px', 'title_length|data-filter-type:text|100px', 'title_type|data-filter-type:text|100px'],
 * 'parent_description_text|class:text-center' => ['description_text|data-filter-type:text|100px', 'description_length|data-filter-type:text|100px', 'description_type|data-filter-type:text|100px']
 * ];
 * $alias_array= [];
 *
 * @param array $col_defs_array
 * @param string $lang_path
 * @param string $delimiter
 * @return array
 */
function translate_multi_d_array_and_separate_alias(array $col_defs_array, $lang_path, $delimiter = "|")
{
    $alias_array = [];
    support_translate_multi_d_array_and_separate_alias($col_defs_array, $alias_array, $lang_path, $delimiter);
    return [$col_defs_array, $alias_array];
}

/**
 * This is just a supporting helper function for the translate_multi_d_array_and_separate_alias function above
 *
 * @param array $col_defs_array
 * @param array $alias_array
 * @param $lang_path
 * @param $delimiter
 * @throws Exception
 */
function support_translate_multi_d_array_and_separate_alias(array &$col_defs_array, array &$alias_array, $lang_path, $delimiter)
{
    $column_index = 0;
    $pre_key = [];
    foreach($col_defs_array as $key=>&$value)
    {
        if(in_array($key, $pre_key) )
        {
            continue;
        }

        $check_alias = true;
        $value_to_translate = $value;
        if( is_array( $value ))
        {
            if( ! empty( $value ))
            {
                $check_alias = false;
            }
            $value_to_translate = $key;
        }

        list($new_value, $alias_name) = get_translations_from_string($lang_path, $value_to_translate, $check_alias, $delimiter);
        if( $check_alias )
        {
            if( is_array( $value ) )
            {
                $col_defs_array[ $new_value ] = $value;
                unset($col_defs_array[$key]);
                $pre_key[] = $new_value;
                $col_defs_array = array_move($new_value, $column_index, $col_defs_array);
            }
            else
            {
                $value = $new_value;
            }

            $alias_array[] = $alias_name;
        }
        else
        {
            support_translate_multi_d_array_and_separate_alias($value, $alias_array, $lang_path, $delimiter);
            $col_defs_array[ $new_value ] = $value;
            unset( $col_defs_array[$key] );
            $pre_key[] = $new_value;
            $col_defs_array = array_move($new_value, $column_index, $col_defs_array);
        }
        $column_index++;
    }
}

/**
 * This is just a supporting helper function for the translate_table_array function above
 *
 * @param $lang_path
 * @param $value_to_translate
 * @param $alias_array
 * @return array
 * @throws Exception
 */
function get_translations_from_string($lang_path, $value_to_translate, $check_alias, $delimiter = "|")
{
    $value_parts = explode($delimiter, $value_to_translate);
    $new_value = $value_parts[0];

    $alias_name = '';
    if ( $check_alias ) {
        $alias_name = $new_value;
    }

    $trans_path_value = $lang_path . "." . $new_value;
    if(str_contains($trans_path_value,"^" )){
        $trans_path_value = str_before_first("^",$trans_path_value);
    }


    if (!trans_exists($trans_path_value) && $lang_path!="auto") {
        //throw new Exception("Invalid trans path: $trans_path_value; Lang path: $lang_path New value: $new_value;");
        //debug_error("Invalid trans path: $trans_path_value");
        $lang_path = "auto";
    }
    if($lang_path=="auto"){
        $new_value = ucwords(str_replace("_"," ",$new_value));
        //debug_info($new_value);
        //debug_info(str_before_first("^",$new_value ));
        if(str_contains($new_value,"^" )){
            $new_value = str_before_first("^", $new_value);
        }
        //$new_value = str_before_first("^", $new_value);
    } else {

        $new_value = trans($trans_path_value);
    }

    $new_value_after_translation = str_replace($value_parts[0], $new_value, $value_to_translate);
    return array( $new_value_after_translation, $alias_name );
}

if(!function_exists('get_string_between')) {
    function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}


