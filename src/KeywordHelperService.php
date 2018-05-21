<?php
/**
 * Created by PhpStorm.
 * User: JESTIN
 * Date: 11-05-2018
 * Time: 04:47 PM
 */

namespace Mondovo\DataTable;


use Mondovo\DataTable\Contracts\KeywordHelperServiceInterface;
use NlpTools\Stemmers\PorterStemmer;

class KeywordHelperService implements KeywordHelperServiceInterface {

	protected $english_stop_words = [
		'i', 'me', 'my', 'myself', 'we', 'us',
		'our', 'ours', 'ourselves', 'you', 'your', 'yours', 'yourself', 'yourselves',
		'he', 'him', 'his', 'himself', 'she', 'her', 'hers', 'herself',
		'it', 'its', 'itself', 'they', 'them', 'their', 'theirs', 'themselves',
		'what', 'which', 'who', 'whom', 'this', 'that', 'these', 'those',
		'am', 'is', 'are', 'was', 'were', 'be', 'been', 'being',
		'have', 'has', 'had', 'having', 'do', 'does', 'did', 'doing',
		'will', 'would', 'shall', 'can', 'could', 'may', 'might', 'must', 'ought',

		"i\'m","you\'re","he\'s","she\'s","it\'s","we\'re","they\'re","i\'ve","you\'ve","we\'ve","they\'ve","i\'d","you\'d",
		"he\'d","she\'d","we\'d","they\'d","i\'ll","you\'ll","he\'ll","she\'ll","we\'ll","they\'ll",

		"isn\'t","aren\'t","wasn\'t","weren\'t","hasn\'t","haven\'t","hadn\'t","doesn\'t","don\'t","didn\'t","won\'t","wouldn\'t","shan\'t","shouldn\'t","can\'t","cannot",
		"couldn\'t","mustn\'t","let\'s","that\'s","who\'s","what\'s","here\'s","there\'s","when\'s","where\'s","why\'s","how\'s",
		"a","an","the","and","but","if","or","because","as","until","while","of","at","by","for","with","about","against",
		"between","into","through","during","before","after","above","below","to","from","up","down","in","out",
		"on","off","over","under","again","further","then","once","here","there","when","where","why",
		"how","all","any","both","each","few","more","most","other","some","such",
		"no","nor","not","only","own","same","so","than","too","very",
		"one","every","least","less","many","now","ever","never","say","says","said","also","get","go",
		"goes","just","made","make","put","see","seen","whether","like","well","back",
		"even","still","way","take","since","another","however","two","three","four",
		"five","first","second","new","old","high","long",

		"reply", "replies", "ago", "day","hey"
	];
	/**
	 * @var PorterStemmer
	 */
	protected $porter_stem_observer;

	function __construct(PorterStemmer $porter_stem_observer)
	{

		$this->porter_stem_observer = $porter_stem_observer;
	}

	public function getStopWords()
	{
		return $this->english_stop_words;
	}

	public function getStemmedPhrase($phrase)
	{
		try
		{
			$phrase = trim($phrase);
			if (empty($phrase))
			{
				return '';
			}

			$exploded_phrase = explode(' ', $this->singularize($phrase));

			$stemmed_phrase_parts = $this->porter_stem_observer->stemAll($exploded_phrase);

			foreach ($stemmed_phrase_parts as $index => $stem_word)
			{
				$str_len_diff = strlen($exploded_phrase[$index]) - strlen($stem_word);
				$stem_verify_status = $this->verifyStemmedWord($exploded_phrase[$index], $stem_word);

				if ($str_len_diff > 3 || (is_bool($stem_verify_status) && !$stem_verify_status) || is_string($stem_verify_status))
				{
					if (is_string($stem_verify_status))
					{
						$stemmed_phrase_parts[$index] = $stem_verify_status;
						continue;
					}

					$stemmed_phrase_parts[$index] = $exploded_phrase[$index];
				}
			}

			$stemmed_phrase = implode(' ', $stemmed_phrase_parts);

			if (is_bool(strpos($phrase, $stemmed_phrase)))
			{
				return $phrase;
			}

			return $stemmed_phrase;
		}
		catch (\Exception $e)
		{
			return $phrase;
		}

	}


	public function verifyStemmedWord($actual_word, $stemmed_word)
	{
		//EG: if 'e' is removed after stemming and last letter of stemmed_word is 'r', then its wrong.
		//    Eg: 'signature' => 'signatur' is wrong
		//         'circus' => 'circu'
		//         'lotus' => 'lotu'
		$non_ending_of_word = [
			'e' => [ 'r', 'c', 'l', 'g', 'k', 's' ],
			's' => [ 'u', 'i', 'a'],
			'es' => [ 'r', 'g' ],
			'er' => [ 'm', 'y', ],
			'ic' => [ 'r', ],
			'ed' => [ 'l', ],
		];

		$change_apply = [

			'es' => [
				'r' => 'e',
				'g' => 'e',
			],
			'ed' => [
				'l' => 'e'
			],

		];

		$str_len_diff = strlen($actual_word) - strlen($stemmed_word);

		if ($str_len_diff == 0)
		{
			return true;
		}

		$temp_actual = str_replace($stemmed_word, '', $actual_word);
		$last_letter_of_stemmed_word = $stemmed_word[strlen($stemmed_word)-1];


		if (isset($non_ending_of_word[$temp_actual]))
		{
			//This kind of words are stemmed wrong
			if (in_array($last_letter_of_stemmed_word, $non_ending_of_word[$temp_actual]))
			{
				if (isset($change_apply[$temp_actual][$last_letter_of_stemmed_word]))
				{
					return $stemmed_word . $change_apply[$temp_actual][$last_letter_of_stemmed_word];
				}

				return false;
			}
		}

		return true;
	}

	static $plural = [
		'/(quiz)$/i'               => "$1zes",
		'/^(ox)$/i'                => "$1en",
		'/([m|l])ouse$/i'          => "$1ice",
		'/(matr|vert|ind)ix|ex$/i' => "$1ices",
		'/(x|ch|ss|sh)$/i'         => "$1es",
		'/([^aeiouy]|qu)y$/i'      => "$1ies",
		'/(hive)$/i'               => "$1s",
		'/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
		'/(shea|lea|loa|thie)f$/i' => "$1ves",
		'/sis$/i'                  => "ses",
		'/([ti])um$/i'             => "$1a",
		'/(tomat|potat|ech|her|vet)o$/i'=> "$1oes",
		'/(bu)s$/i'                => "$1ses",
		'/(alias)$/i'              => "$1es",
		'/(octop)us$/i'            => "$1i",
		'/(ax|test)is$/i'          => "$1es",
		'/(us)$/i'                 => "$1es",
		'/s$/i'                    => "s",
		'/$/'                      => "s"
	];

	static $singular = [
		'/(quiz)zes$/i'             => "$1",
		'/(matr)ices$/i'            => "$1ix",
		'/(vert|ind)ices$/i'        => "$1ex",
		'/^(ox)en$/i'               => "$1",
		'/(alias)es$/i'             => "$1",
		'/(octop|vir)i$/i'          => "$1us",
		'/(cris|ax|test)es$/i'      => "$1is",
		'/(shoe)s$/i'               => "$1",
		'/(o)es$/i'                 => "$1",
		'/(bus)es$/i'               => "$1",
		'/([m|l])ice$/i'            => "$1ouse",
		'/(x|ch|ss|sh)es$/i'        => "$1",
		'/(m)ovies$/i'              => "$1ovie",
		'/(s)eries$/i'              => "$1eries",
		'/([^aeiouy]|qu)ies$/i'     => "$1y",
		'/([lr])ves$/i'             => "$1f",
		'/(tive)s$/i'               => "$1",
		'/(hive)s$/i'               => "$1",
		'/(li|wi|kni)ves$/i'        => "$1fe",
		'/(shea|loa|lea|thie)ves$/i'=> "$1f",
		'/(^analy)ses$/i'           => "$1sis",
		'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i'  => "$1$2sis",
		'/([ti])a$/i'               => "$1um",
		'/(n)ews$/i'                => "$1ews",
		'/(h|bl)ouses$/i'           => "$1ouse",
		'/(corpse)s$/i'             => "$1",
		'/(us)es$/i'                => "$1",
		'/(n|c)ess$/i'              => "$1ess",
		'/(e|a)ss$/i'               => "$1ss",
		'/s$/i'                     => "",
	];

	static $irregular = [
		'move'   => 'moves',
		'foot'   => 'feet',
		'goose'  => 'geese',
		'sex'    => 'sexes',
		'child'  => 'children',
		'man'    => 'men',
		'tooth'  => 'teeth',
		'person' => 'people',
		'valve'  => 'valves'
	];

	static $uncountable = [
		'sheep',
		'fish',
		'deer',
		'series',
		'species',
		'money',
		'rice',
		'information',
		'equipment',
		'angularjs',
		'wordpress'
	];

	public function singularize( $string )
	{
		// save some time in the case that singular and plural are the same
		if ( in_array( strtolower( $string ), self::$uncountable ) )
			return $string;

		// check for irregular plural forms
		foreach ( self::$irregular as $result => $pattern )
		{
			$pattern = '/' . $pattern . '$/i';

			if ( preg_match( $pattern, $string ) )
			{
				return $this->verifySingular($string, preg_replace( $pattern, $result, $string));
			}
		}

		// check for matches using regular expressions
		foreach ( self::$singular as $pattern => $result )
		{
			if ( preg_match( $pattern, $string ) )
				return $this->verifySingular($string, preg_replace( $pattern, $result, $string ));
		}

		return $string;
	}


	public function verifySingular($actual_word, $singular_word)
	{
		$str_len_diff = strlen($actual_word) - strlen($singular_word);
		$stem_verify_status = $this->verifyStemmedWord($actual_word, $singular_word);

		if ($str_len_diff > 3 || (is_bool($stem_verify_status) && !$stem_verify_status) || is_string($stem_verify_status))
		{
			if (is_string($stem_verify_status))
			{
				return $stem_verify_status;
			}

			return $actual_word;
		}

		return $singular_word;
	}

	public function getDistinctPhrasesInKeywords($phrase, array $base_keywords)
	{
		$result = (Object) NULL;
		$result->count = 0;
		$result->matches = [];
		$result->distinct_matches = [];
		$result->distinct_singular_matches = [];

		foreach ($base_keywords as $keyword)
		{
			$keyword = ' ' . $keyword . ' ';
			$pos = strpos($keyword, $phrase);
			if (!is_bool($pos))
			{
				$word = substr($keyword, $pos, strlen($phrase))  . explode(' ', substr($keyword, $pos + strlen($phrase)))[0];
				$word_length_diff = strlen($word) - strlen($phrase);
				if($word_length_diff > 3)
				{
					continue;
				}

				if($word_length_diff > 0)
				{
					$word_clean_stem_temp = $this->getStemmedPhrase($this->trimSpecialCharacters($word));
					$phrase_stem_temp = $this->getStemmedPhrase($phrase);
					if ($word_clean_stem_temp != $phrase_stem_temp && (strlen($word_clean_stem_temp) - strlen($phrase_stem_temp)) > 3)
					{
						continue;
					}
				}

				$result->count++;
				$result->matches[] = $keyword;


				if (!in_array($word, $result->distinct_matches))
				{
					$result->distinct_matches[] = $word;
				}

				$singular = $this->singularize($word);

				if (!in_array($singular, $result->distinct_singular_matches))
				{
					$result->distinct_singular_matches[] = $singular;
				}

			}
		}

		return $result;
	}

	public function trimSpecialCharacters($word, $remove_numbers = true)
	{
		$word = array_values(str_split($word));
		$special_characters = [ '`', '~', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_', '+', '=', '{', '[', ']', '}', '|', '\\', '/', '<', '>', '?', "'", "\"", ';', ':' ];
		$numbers = [ '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ];

		$word_count = count($word);

		if ($word_count == 0)
		{
			return implode('', $word);
		}

		if( in_array($word[0], $special_characters) )
		{
			unset($word[0]);
			return $this->trimSpecialCharacters(implode('', $word), $remove_numbers);
		}

		if(in_array($word[$word_count-1], $special_characters) )
		{
			unset($word[$word_count-1]);

			return $this->trimSpecialCharacters(implode('', $word), $remove_numbers);
		}

		if ($remove_numbers)
		{
			if( in_array($word[0], $numbers) )
			{
				unset($word[0]);
				return $this->trimSpecialCharacters(implode('', $word), $remove_numbers);
			}

			if(in_array($word[$word_count-1], $numbers) )
			{
				unset($word[$word_count-1]);
				return $this->trimSpecialCharacters(implode('', $word), $remove_numbers);
			}
		}

		return implode('', $word);
	}
}