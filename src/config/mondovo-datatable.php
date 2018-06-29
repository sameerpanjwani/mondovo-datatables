<?php
/**
 * Created by PhpStorm.
 * User: JESTIN
 * Date: 11-05-2018
 * Time: 01:10 PM
 */

$stop_words = [
	file_get_contents('https://mondovo-cdn.s3.amazonaws.com/stop_words.txt'),
	file_get_contents('https://mondovo-cdn.s3.amazonaws.com/stop_words_first.txt')
];

return [
	/*
	 * In order to integrate the datatable excel export,
	 * you'll need to enter the logo url for white labeling
	 * By default it uses mondovo logo.
	 */

	'default_logo_url' => 'img/mondovo-logo.png',
	'demo_user' => false,
	'access_level' => 1,
	'manager_url' => '',
	'kd_modal_url' => '',
	'stop_words_list' => $stop_words
];
