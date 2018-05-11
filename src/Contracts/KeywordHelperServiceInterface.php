<?php
/**
 * Created by PhpStorm.
 * User: JESTIN
 * Date: 11-05-2018
 * Time: 04:48 PM
 */

namespace Mondovo\Datatable\Contracts;


interface KeywordHelperServiceInterface {

	public function getStopWords();

	public function getStemmedPhrase($phrase);

	public function getDistinctPhrasesInKeywords($phrase, array $base_keywords);

	public function trimSpecialCharacters($word, $remove_numbers = true);

	public function singularize( $string );


}