<?php
/**
 * Created by PhpStorm.
 * User: Sameer
 * Date: 22-05-2015
 * Time: 13:59
 */
//Given below are some useful string helper functions that I've developed for common string operations I've had to deal with

/**
 * Extracts the string before the last mention of the character you specify. E.g. str_before_last(",","My,name,is,John") will return "My,name,is"
 * @param $character
 * @param $string
 * @return string
 */
function str_before_last($character,$string){
	return substr($string, 0, strrpos($string, "$character"));
}

/**
 * Extracts the string after the last mention of the character you specify. E.g. str_after_last(",","My,name,is,John") will return "John"
 * @param $character
 * @param $string
 * @return string
 */
function str_after_last($character,$string){
	return substr($string, strrpos($string, "$character")+1, strlen($string) );
}

/**
 * Extracts the string before the first mention of the character you specify. E.g. str_before_first(",","My,name,is,John") will return "My"
 * @param $character
 * @param $string
 * @return string
 */
function str_before_first($character,$string){
	return substr($string, 0, strpos($string, "$character"));
}

/**
 * Extracts the string after the first mention of the character you specify. E.g. str_after_first(",","My,name,is,John") will return "name,is,John"
 * @param $character
 * @param $string
 * @return string
 */
function str_after_first($character,$string){
	return substr($string, strpos($string, "$character")+1, strlen($string) );
}

/**
 * @param $strtotime_value
 * @return string
 */
function custom_date_format_with_month_day_year_using_strtotime_value( $strtotime_value )
{
	if( empty($strtotime_value) )
	{
		return '-';
	}

	return date("M d, Y", $strtotime_value);
}

/**
 * @param $strtotime_value
 * @return string
 */
function custom_date_format_with_year_month_day_using_strtotime_value( $strtotime_value )
{
	if( empty($strtotime_value) )
	{
		return '-';
	}

	return date("Y-m-d", $strtotime_value);
}

/**
 * @param $date
 * @return string
 */
function custom_date_format_with_month_day_year( $date )
{
	if( empty($date) || $date == "0000-00-00 00:00:00")
	{
		return '-';
	}

	return date("M d, Y", strtotime($date) );
}

/**
 * @param $date
 * @return string
 */
function custom_date_format_with_day_month_year( $date )
{
	if( empty($date) || $date == "0000-00-00 00:00:00")
	{
		return '-';
	}

	return date("d M, Y", strtotime($date) );
}

function custom_date_format_with_day_suffix_month( $date )
{
	if( empty($date) || $date == "0000-00-00 00:00:00")
	{
		return '-';
	}

	return date("jS M", strtotime($date) );

}

function custom_date_format_with_day_suffix_month_and_year( $date )
{
	if( empty($date) || $date == "0000-00-00 00:00:00")
	{
		return '-';
	}

	return date("jS M Y", strtotime($date) );
}

/**
 * @param $date
 * @return string
 */
function custom_date_format_with_month_day_year_hour_minute_second( $date )
{
	if( empty($date) || $date == "0000-00-00 00:00:00")
	{
		return '-';
	}

	return date("M d, Y H:i:s", strtotime($date) );
}

/**
 * @param $date
 * @return bool|string
 */
function custom_date_format_with_day_month_year_hour_minute_second( $date )
{
	if( empty($date) || $date == "0000-00-00 00:00:00")
	{
		return '-';
	}

	return date("d-m-Y H:i:s", strtotime($date) );
}

function custom_date_format_with_day_month_year_hour_minute_second_comma_separated( $date )
{
	if( empty($date) || $date == "0000-00-00 00:00:00")
	{
		return '-';
	}

	return date("d M, Y H:i:s", strtotime($date) );
}

/**
 * @param $date
 * @return bool|string
 */
function custom_date_format_with_month_day_year_hour_minute_second_in_letter( $date )
{
	if( empty($date) || $date == "0000-00-00 00:00:00")
	{
		return '-';
	}

	return date("M-d-Y || H:i:s", strtotime($date) );
}

/**
 * @param $date
 * @param $duration
 * @return string
 */
function custom_change_in_date_format_with_month_day_year( $date, $duration )
{
	if( empty($date) || $date == "0000-00-00 00:00:00")
	{
		return '-';
	}

	return date("M d, Y", strtotime($duration, strtotime($date)));
}


/**
 * @param $date
 * @return string
 */
function custom_date_format_with_year_month_day( $date )
{
	if( empty($date) || $date == "0000-00-00 00:00:00")
	{
		return '-';
	}

	return date("Y-m-d", strtotime($date) );
}

/**
 * @param $date
 * @return bool|string
 */
function custom_date_format_with_year_month_day_for_baf( $date )
{
	if( empty($date) || $date == "0000-00-00 00:00:00")
	{
		return '-';
	}

	return [ date('Y', strtotime($date) ),
		date('m', strtotime($date) ),
		date('d', strtotime($date) )
	];
}

/**
 * @return bool|string
 */
function custom_date_format_with_year_month_day_plus_twenty_for_hours()
{
	return date("Y-m-d H:i:s", strtotime('+24 hours'));
}

/**
 * @param $date
 * @return bool|string
 */
function custom_date_format_with_year_month_day_hour_min_sec($date)
{
	if(is_int($date))
		return date("Y-m-d H:i:s", $date);

	return date("Y-m-d H:i:s", strtotime($date));
}

/**
 * @param $no_of_days
 * @return string
 */
function convert_days_into_years( $no_of_days )
{
	if( empty($no_of_days))
	{
		return '';
	}
	return $no_of_days / 365 ;
}

/**
 * Function to calculate the same day one month in the future.
 *
 * This is necessary because some months don't have 29, 30, or 31 days. If the
 * next month doesn't have as many days as this month, the anniversary will be
 * moved up to the last day of the next month.
 *
 * @param $start_date (optional)
 *   UNIX timestamp of the date from which you'd like to start. If not given,
 *   will default to current time.
 *
 * @return $timestamp
 *   UNIX timestamp of the same day or last day of next month.
 */
function calculate_same_date_next_month($start_date = FALSE) {
	if ($start_date) {
		$now = strtotime($start_date); // Use supplied start date.
	} else {
		$now = time(); // Use current time.
	}

	// Get the current month (as integer).
	$current_month = date('n', $now);

	// If the we're in Dec (12), set current month to Jan (1), add 1 to year.
	if ($current_month == 12) {
		$next_month = 1;
		$plus_one_month = mktime(0, 0, 0, 1, date('d', $now), date('Y', $now) + 1);
	}
	// Otherwise, add a month to the next month and calculate the date.
	else {
		$next_month = $current_month + 1;
		$plus_one_month = mktime(0, 0, 0, date('m', $now) + 1, date('d', $now), date('Y', $now));
	}

	$i = 1;
	// Go back a day at a time until we get the last day next month.
	while (date('n', $plus_one_month) != $next_month) {
		$plus_one_month = mktime(0, 0, 0, date('m', $now) + 1, date('d', $now) - $i, date('Y', $now));
		$i++;
	}

	return date("Y-m-d", $plus_one_month);
}

/**
 * @param $no_of_days
 * @return string
 */
function convert_days_into_years_months_and_days( $no_of_days )
{
	if( empty($no_of_days))
	{
		return '';
	}

	$no_of_years = intval( $no_of_days / 365) ;
	$remaining_no_of_days = $no_of_days % 365 ;
	$no_of_months = intval( $remaining_no_of_days / 30) ;
	$no_of_days = $remaining_no_of_days % 30 ;

	$final_statement = '';
	if( $no_of_years > 0)
	{
		$final_statement.= $no_of_years. ' Yr(s) ';
	}

	if( $no_of_months > 0)
	{
		$final_statement.= $no_of_months. ' month(s) ';
	}

	if( $no_of_days > 0)
	{
		$final_statement.= $no_of_days. ' day(s)';
	}

	return $final_statement;
}

function get_day_of_date($date)
{
	$timestamp = strtotime($date);
	$day = intval(date('d', $timestamp));
	return $day;
}

function get_month_of_date($date)
{
	$timestamp = strtotime($date);
	$month = intval(date('m', $timestamp));
	return $month;
}

function getChangePercentage($current_value, $previous_value)
{
	if($previous_value == 0)
	{
		return 0;
	}

	return round((($current_value - $previous_value) / $previous_value)*100, 2);
}

function getBrowserIcon($browser_name)
{
	return str_replace(' ','-',strtolower($browser_name));
}

function convertToTimeFormat($time_in_seconds, $with_arrow = false)
{
	$abs_time_in_seconds = abs($time_in_seconds);
	$sec_num = (int) round($abs_time_in_seconds);
	$hours   = floor($sec_num / 3600);
	$minutes = floor(($sec_num - ($hours * 3600)) / 60);
	$seconds = $sec_num - ($hours * 3600) - ($minutes * 60);

	if ($hours   < 10) {$hours   = "0".$hours;}
	if ($minutes < 10) {$minutes = "0".$minutes;}
	if ($seconds < 10) {$seconds = "0".$seconds;}

	if(!$with_arrow)
	{
		return $hours.':'.$minutes.':'.$seconds;
	}

	if($time_in_seconds == 0 || $time_in_seconds == 0.0)
	{
		return '00:00:00';
	}
	else if($time_in_seconds < 0)
	{
		return '<span class="negative-change"><i class="fa fa-sort-desc"></i>&nbsp;'.$hours.':'.$minutes.':'.$seconds.'</span>';
	}
	else if($time_in_seconds > 0)
	{
		return '<span class="positive-change"><i class="fa fa-sort-asc"></i>&nbsp;'.$hours.':'.$minutes.':'.$seconds.'</span>';
	}
}

function getChangePercentageWithArrow($current_value, $previous_value)
{
	if($previous_value == 0)
	{
		return '0%';
	}

	$change = round((($current_value - $previous_value) / $previous_value)*100, 2);

	if($change == 0)
	{
		return '0%';
	}
	else if($change < 0)
	{
		$change = '<span class="negative-change"><i class="fa fa-sort-desc"></i>&nbsp;'.abs($change).'%</span>';
	}
	else if($change > 0)
	{
		$change = '<span class="positive-change"><i class="fa fa-sort-asc"></i>&nbsp;'.abs($change).'%</span>';
	}

	return $change;
}

function getChangeWithArrow($current_value, $previous_value, $reverse_status = false)
{
	if($previous_value == 0)
	{
		return 0;
	}

	$change = round(($current_value - $previous_value), 2);

	if($change == 0)
	{
		return 0;
	}

	if($reverse_status)
	{
		if($change > 0)
		{
			$change = '<span class="negative-change"><i class="fa fa-sort-desc"></i>&nbsp;'.abs($change).'%</span>';
		}
		else if($change < 0)
		{
			$change = '<span class="positive-change"><i class="fa fa-sort-asc"></i>&nbsp;'.abs($change).'%</span>';
		}
	}
	else
	{
		if($change < 0)
		{
			$change = '<span class="negative-change"><i class="fa fa-sort-desc"></i>&nbsp;'.abs($change).'%</span>';
		}
		else if($change > 0)
		{
			$change = '<span class="positive-change"><i class="fa fa-sort-asc"></i>&nbsp;'.abs($change).'%</span>';
		}
	}


	return $change;
}

function greenRedHighlight($data,$decimal_points=0) {
	//This function will read any string, extract the number from it and return a custom green / red color span element based on whether it's positive or negative
	//You can pass a string with extra characters, e.g 5%, Rs.68 and this should still work
	//If you want to round off your number to a decimal with certain number of points, pass the second argument BUT make sure you have at least one decimal in your number before passing it else the function will treat it as an int
	//Max decimals allowed will be 12
	//Limtation: only one number in a string is compulsory. It will FAIL with more than one number
	//Limitation: You cannot have any more than one dot. Example, Rs.32.54 is NOT GOING TO WORK. Replace the dot from Rs. before sending in and you can append it later.

	$extracted_data = preg_replace('/[^0-9.\-]+/','',$data);


	$comparison_number = (float) $extracted_data;

	$extra_string = "";
	$arrow_html = '';

	if ($comparison_number < 0) {
		$color_class = 'checkFailed markImportant';
		$extra_string = "<span class='hidden'>-</span>";
		$data = str_replace("-","",$data);
		$extracted_data = str_replace("-","", $extracted_data);
		$comparison_number = str_replace("-","", $comparison_number);
		$arrow_html = '<i class="fa fa-long-arrow-up"></i>' . $extra_string;
	} elseif ($comparison_number > 0) {
		$arrow_html = '<i class="fa fa-long-arrow-up"></i>';
		$color_class = 'checkPassed markImportant';
	} else {
		$color_class = 'checkNeutral markImportant';

	}

	$new_number = number_format(round($comparison_number,$decimal_points),$decimal_points);

	$data = str_replace($extracted_data,$new_number,$data);

	return "<span class='$color_class'>$arrow_html $data</span>";
}

function greenRedHighlightGA($data, $decimal_points=0, $absolute = true, $reverse = false, $percentage_symbol = true)
{

	if(!$reverse)
	{
		if ($data < 0)
		{
			$color_class = 'checkFailed markImportant';
		}
		else if ($data > 0)
		{
			$color_class = 'checkPassed markImportant';
		}
		else
		{
			$color_class = 'checkNeutral markImportant';
		}
	}
	else
	{
		if ($data > 0)
		{
			$color_class = 'checkFailed markImportant';
		}
		else if ($data < 0)
		{
			$color_class = 'checkPassed markImportant';
		}
		else
		{
			$color_class = 'checkNeutral markImportant';
		}
	}

	if($absolute)
	{
		$data = abs($data);
	}

	/*if($absolute)
	{
		if($data < 0){
			$data = abs($data);
			$data = round($data, $decimal_points);
			$data = '<i class="fa fa-sort-desc"></i> '. $data;
		}else{
			$data = abs($data);
			$data = round($data, $decimal_points);
			$data = '<i class="fa fa-sort-asc"></i> '. $data;
		}
	}*/

	$data = round($data, $decimal_points);

	if($percentage_symbol)
	{
		$data = $data.'%';
	}

	return "<span class='$color_class'>$data</span>";
}

function greenRedHighlightGATwo($data_to_check, $data_to_apply, $reverse = false)
{
	if(!$reverse)
	{
		if ($data_to_check < 0)
		{
			$color_class = 'checkFailed markImportant';
		}
		else if ($data_to_check > 0)
		{
			$color_class = 'checkPassed markImportant';
		}
		else
		{
			$color_class = 'checkNeutral markImportant';
		}
	}
	else
	{
		if ($data_to_check > 0)
		{
			$color_class = 'checkFailed markImportant';
		}
		else if ($data_to_check < 0)
		{
			$color_class = 'checkPassed markImportant';
		}
		else
		{
			$color_class = 'checkNeutral markImportant';
		}
	}

	return "<span class='$color_class'>$data_to_apply</span>";
}

function mondovoNumberFormatter($data,$decimal_points=2) {
	//This function will read any string, extract the number from it and return a number with the decimals you specify
	//You can pass a string with extra characters, e.g 5%, Rs.68 and this should still work
	//If you want to round off your number to a decimal with certain number of points, pass the second argument BUT make sure you have at least one decimal in your number before passing it else the function will treat it as an int
	//Max decimals allowed will be 12
	//Limtation: only one number in a string is compulsory. It will FAIL with more than one number
	//Limitation: You cannot have any more than one dot. Example, Rs.32.54 is NOT GOING TO WORK. Replace the dot from Rs. before sending in and you can append it later.

	$extracted_data = preg_replace('/[^0-9.\-]+/','',$data);
	$comparison_number = (float) $extracted_data;

	$new_number = number_format(round($comparison_number,$decimal_points),$decimal_points);
	$data = str_replace($extracted_data,$new_number,$data);
	if($data == "")
		$data = number_format(0, $decimal_points);

	return $data;
}

function readableNumber($n, $form = 'short')
{
	$n = str_replace(",", "", $n);
	//Invalid number should not be processed. It will return the same value if it's an invalid number
	if(!(is_numeric($n)))
	{
		return $n;
	}
	// Reference https://secure.php.net/manual/en/function.number-format.php#89888
	// now filter it;
	if($form == 'long')
	{
		if ($n > 1000000000000) return round(($n/1000000000000), 2).'<span>Trillion</span>';
		elseif ($n > 1000000000) return round(($n/1000000000), 2).'<span>Billion</span>';
		elseif ($n > 1000000) return round(($n/1000000), 2).'<span>Million</span>';
		elseif ($n > 1000) return round(($n/1000), 2).'<span>Thousand</span>';
	}
	else
	{
		if ($n > 1000000000000) return round(($n/1000000000000), 2).'<span>T</span>';
		elseif ($n > 1000000000) return round(($n/1000000000), 2).'<span>B</span>';
		elseif ($n > 1000000) return round(($n/1000000), 2).'<span>M</span>';
		elseif ($n > 1000) return round(($n/1000), 2).'<span>K</span>';
	}

	return number_format($n);
}

function readableNumberGA($n, $form = 'short', $as_it_is = false)
{
	// Reference https://secure.php.net/manual/en/function.number-format.php#89888

	if($n == 'NA')
	{
		return $n;
	}

	if($as_it_is)
	{
		// is this a number?
		if (!is_numeric($n)) return $n;
	}
	else
	{
		// first strip any formatting;
		$n = (0+str_replace(",", "", $n));

		// is this a number?
		if (!is_numeric($n)) return false;

	}

	// now filter it;
	if($form == 'long')
	{
		if ($n > 1000000000000) return round(($n/1000000000000), 2).'<span>Trillion</span>';
		elseif ($n > 1000000000) return round(($n/1000000000), 2).'<span>Billion</span>';
		elseif ($n > 1000000) return round(($n/1000000), 2).'<span>Million</span>';
		elseif ($n > 1000) return round(($n/1000), 2).'<span>Thousand</span>';
	}
	else
	{
		if ($n > 1000000000000) return round(($n/1000000000000), 2).'<span>T</span>';
		elseif ($n > 1000000000) return round(($n/1000000000), 2).'<span>B</span>';
		elseif ($n > 1000000) return round(($n/1000000), 2).'<span>M</span>';
		elseif ($n > 1000) return round(($n/1000), 2).'<span>K</span>';
	}

	return number_format($n);
}


function twitter_style_dates($tweet_date, $php_timezone)
{
	$tweet_time = strtotime($tweet_date);
	$date_format = 'g:i A M jS';
	$current_time = time();
	$time_diff = abs($current_time - $tweet_time);
	switch ($time_diff)
	{
		case ($time_diff < 60):
			$display_time = $time_diff.' seconds ago';
			break;
		case ($time_diff >= 60 && $time_diff < 3600):
			$min = floor($time_diff/60);
			$display_time = $min.' minutes ago';
			break;
		case ($time_diff >= 3600 && $time_diff < 86400):
			$hour = floor($time_diff/3600);
			$display_time = 'about '.$hour.' hour';
			if ($hour > 1){ $display_time .= 's'; }
			$display_time .= ' ago';
			break;
		default:
			$display_time = \Carbon\Carbon::createFromTimestamp($tweet_time, $php_timezone)->format($date_format); //changed by aditya
			break;
	}

	return $display_time;
}

//coverts the url's in text to link, opens links in new tab
function twitter_linkify($str)
{
	$str = preg_replace('/(https?:\/\/[^\s"<>]+)/','<a href="$1" target="_blank">$1</a>', $str);
	$str = preg_replace('/(^|[\n\s])@([^\s"\t\n\r<:]*)/is', '$1<a href="http://twitter.com/$2" target="_blank">@$2</a>', $str);
	$str = preg_replace('/(^|[\n\s])#([^\s"\t\n\r<:]*)/is', '$1<a href="http://twitter.com/search?q=%23$2" target="_blank">#$2</a>', $str);

	return $str;
}

function handlify($str)
{
	return preg_replace('/(@?:\/\/[^\s"<>]+)/','<a href="$1" target="_blank">https://www.twitter.com/$1</a>',$str);
}

function is_between($value, $from, $to)
{
	if ($value >= $from && $value < $to)
	{
		return true;
	}

	return false;
}

function purify_content( $text ) {
	$text = str_replace('’', "'", $text);
	//Removing AngularJs Variables
	$text = preg_replace( '/(\{{).*(\}})/', '', $text );

	//Remove anything that is not alphanumeric regardless of lowercase or uppercase and replace with ' '
	$text = preg_replace( '/[^\da-z !\' ?.]/i', ' ', $text );

	//Remove any stand alone numbers & replace it with ' '. Eg:' 2016 '
	$text = ' ' . preg_replace( '/(\s+\d+\s+)/', ' ', $text ) . ' ';

	//Replace consecutive occurrences of more than one space with single space.
	$text = preg_replace( "/\s\s+/", " ", $text );

	//In case, any stand alone numbers are still present, replace with '' - remove it.
	$text = preg_replace( '/(\s+\d+\s+)/', ' ', $text );

	//remove single quotes from a string, leaving only the words(Apostrophe)
	$text = preg_replace("/\B'([a-z-]+)'\B/i",'$1', $text);

	$text = strtolower(trim($text));

	return $text;
}

// Does string contain letters?
function _s_has_letters( $string ) {
	return preg_match( '/[a-zA-Z]/', $string );
}
// Does string contain numbers?
function _s_has_numbers( $string ) {
	return preg_match( '/\d/', $string );
}
// Does string contain special characters?
function _s_has_special_chars( $string ) {
	return preg_match('/[^a-zA-Z\d]/', $string);
}

function generate_num_hash()
{
	$num_1 = rand(1, 4);
	$num_2 = rand(1, 5);
	$hash_string = rand(10, 19) . ($num_1 + $num_2) . rand(99999, 9999999);
	return array($num_1, $num_2, $hash_string);
}

function get_string_between($string, $start, $end){
	$string = ' ' . $string;
	$ini = strpos($string, $start);
	if ($ini == 0) return '';
	$ini += strlen($start);
	$len = strpos($string, $end, $ini) - $ini;
	return substr($string, $ini, $len);
}

function editColumnForCustomDate($data, $varname, $timezone){
	if( empty($data[$varname]) || $data[$varname] == "0000-00-00 00:00:00")
	{
		return '-';
	}
	if($timezone != ''){
		$local_time_zone = new DateTimeZone($timezone);
		$datetime = new DateTime($data[$varname]);
		$datetime->setTimezone($local_time_zone);
		return $datetime->format('M d, Y | H:i:s');
	}

	return date("M d, Y | H:i:s", strtotime($data[$varname]) );
}

function convert_to_utf8($text){

	$encoding = mb_detect_encoding($text, mb_detect_order(), false);

	if($encoding == "UTF-8")
	{
		$text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
	}


	$out = iconv(mb_detect_encoding($text, mb_detect_order(), false), "UTF-8//IGNORE", $text);


	return $out;
}

function greater_than_two($word) {
	return count(explode(' ', $word)) > 2;
}
