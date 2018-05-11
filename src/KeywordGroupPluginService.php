<?php
/**
 * Created by PhpStorm.
 * User: Nikhil
 * Date: 23-03-2017
 * Time: 03:49 PM
 */

namespace Mondovo\Datatable;

use Illuminate\Support\Facades\Cache;
use Mondovo\Datatable\Contracts\KeywordGroupPluginServiceInterface;
use Mondovo\Datatable\Contracts\KeywordHelperServiceInterface;

class KeywordGroupPluginService implements KeywordGroupPluginServiceInterface
{

    /**
     * @var KeywordHelperServiceInterface
    */
    protected $keyword_service;

    protected $stop_words = [];

    protected $matches = [];

    public function __construct(KeywordHelperServiceInterface $keyword_service)
    {
        $this->keyword_service = $keyword_service;

    }

    public function keywordGroupingExtended(array $keywords)
    {
        $prepared_data = [];
        list($stage_1_split_count, $stage_1_word_distribution) = $this->findWordRepeatCount($keywords);

        $data = [];
        foreach ($stage_1_split_count as $split_word => $split_count)
        {
            if (!isset($stage_1_word_distribution[$split_word]))
            {
                continue;
            }

            $data['parent'] = [
                'keyword' => $split_word,
                'count' => $split_count,
                'distinct_words' => $stage_1_word_distribution[$split_word]['words'],
            ];

            $pre_keyword_data_stage_2 = $stage_1_word_distribution[$split_word]['keywords'];
            $data['child'] = $this->findWordRepeatCountChild($pre_keyword_data_stage_2, $split_word);

            $prepared_data[] = $data;
        }

        return $prepared_data;
    }

    public function findWordRepeatCount(array $keywords)
    {
        $split_words = $this->findSplitWordsMain($keywords);

        $split_count = [];
        $split_haystack_distribution = [];
        foreach ($split_words  as $index => $split_word)
        {

            if (strlen($split_word) <= 2 )
            {
                continue;
            }

            list($data_count_detail, $matched_keywords, $matched_words) = $this->array_str_pos($keywords, $split_word);
            $split_word_occurrence_count = count($data_count_detail);

            if($split_word_occurrence_count < 3)
            {
                continue;
            }

            $split_count[$split_word] = $split_word_occurrence_count;
            $split_haystack_distribution[$split_word]['keywords'] = $matched_keywords;
            $split_haystack_distribution[$split_word]['words'] = $matched_words;
        }

        arsort($split_count, SORT_NUMERIC);
        $split_count = array_slice($split_count, 0, 30);

        return [$split_count, $split_haystack_distribution];
    }

    public function findInCompleteInOccurrence($check_string, $in_string)
    {
        $check_words = explode(' ', $check_string);
        $in_words = explode(' ', $in_string);

        foreach ($check_words as $check_word)
        {
            if(!in_array($check_word, $in_words))
            {
                return false;
            }
        }

        return true;
    }

    public function findWordRepeatCountChild(array $keywords, $parent_word)
    {
        $split_words = $this->findSplitWordsMain($keywords);

        $prepared_child_data = $pre_prepared_child_data = [];
        foreach ($split_words  as $index => $split_word)
        {
            $complete_occurrence_status = $this->findInCompleteInOccurrence($split_word, $parent_word);
            if (strlen($split_word) <= 2 || $complete_occurrence_status)
            {
                continue;
            }

            list($data_count_detail, $matched_keywords, $matched_words) = $this->array_str_pos($keywords, $split_word);

            $child_split_count = count($data_count_detail);

            if ($child_split_count < 3)
            {
                continue;
            }

            $sort_array[$split_word] = $child_split_count;

            $pre_prepared_child_data[$split_word] = [
                'keyword' => $split_word,
                'count' => $child_split_count,
                'distinct_words' => $matched_words,
            ];
        }

        if (!empty($sort_array))
        {
            arsort($sort_array, SORT_NUMERIC);
            $sort_array = array_slice($sort_array, 0, 10);
            foreach ($sort_array as $word => $count)
            {
                if(!isset($pre_prepared_child_data[$word]))
                {
                    continue;
                }

                $prepared_child_data[] = $pre_prepared_child_data[$word];
            }
        }

        return $prepared_child_data;
    }

    public function removeStopWordsTraces(array &$keywords)
    {
        $this->updateStopWordsVar();
        $basic_stop_words = $this->keyword_service->getStopWords();

        foreach ($keywords as $keyword_index => $keyword)
        {
            $words = explode(' ', $keyword);
            $word_count = count($words);

            //Removing Stop words: Based on word-count criteria.
            if($word_count >= 2)
            {
                $words = array_diff($words, $this->stop_words[1]);
                $words = array_diff($words, $this->stop_words[0]);
            }
            else
            {
                $words = array_diff($words, $this->stop_words[0]);
            }

            $words = array_diff($words, $basic_stop_words);

            if (empty($words))
            {
                unset($keywords[$keyword_index]);
            }
            else
            {
                $keywords[$keyword_index] = implode(' ', $words);
            }

        }

    }


    public function findSplitWordsMain(array $keywords, $remove_stop_words = true)
    {
        $split_words = [];
        foreach ($keywords as $keyword)
        {
            $split_words = array_merge($split_words, $this->findSplitWordsProcedure($keyword, 2));
        }

        if ($remove_stop_words)
        {
            $this->removeStopWordsTraces($split_words);
        }

        $prepared_split_words = [];
        foreach ($split_words as $split_word)
        {
            if(!in_array($split_word, $prepared_split_words))
            {
                $prepared_split_words[] = $split_word;
            }
        }

        return $prepared_split_words;
    }

    public function findSplitWordsProcedure($keyword, $max_phrase_word_length = 2)
    {
        $words = explode(' ', $keyword);
        if (empty($words))
        {
            return [];
        }

        $split_words = [];
        for ($word_length = 2; $word_length <= $max_phrase_word_length; $word_length++)
        {
            $split_words += $this->wordBlender($words, $word_length, true);
        }

        $filtered_split_words = [];
        foreach ($split_words as $split_word)
        {
            $words = explode(' ', $split_word);
            $count = count($words);
            if($count == 0)
            {
                continue;
            }
            $first_word = $words[0];
            $last_word = $words[$count-1];

            if(strlen($first_word) < 3 || strlen($last_word) < 3)
            {
                continue;
            }

            $filtered_split_words[] = $split_word;
        }


        return $filtered_split_words;

    }

    public function wordBlender(array $words, $phrase_word_length = 2, $enable_stemming = true)
    {
        $prepared_words = [];
        $words_length = count($words);
        for ($i = 0; $i < $words_length; $i++)
        {
            $prepared_words[] = implode(' ', array_slice($words, $i, $phrase_word_length));
        }

        return $prepared_words;
    }

    public function updateStopWordsVar()
    {
        $this->stop_words = config('mondovo-datatable.stop_words_list');
    }

    public function getKeywordCommonGroups(array $keywords)
    {
        $this->matches = [];
        $this->updateStopWordsVar();

        if (count($keywords) < 3)
        {
            return [];
        }

        $common_keywords = $this->findCommonKeywords($keywords, 2, 2);

        $filter_1 = [];
        $filter_out = [];
        $finger_print = [];

        foreach ($common_keywords as $main_outer_keyword => $keyword_sub_array)
        {
            //Here we are merging the common data, based on the outer keyword stem.
            //So if there are outer keywords like: 'words' & 'word', then the data for 'words' will be merged along with 'word';
            $common_keywords = $this->mergeCommonWords($common_keywords, $main_outer_keyword);

            if (in_array($main_outer_keyword, $filter_out) || !isset($common_keywords[$main_outer_keyword]))
            {
                continue;
            }

            list($common_keywords, $filter_1[$main_outer_keyword], $finger_print) = $this->mergeInnerCommonWords($common_keywords, $main_outer_keyword, $finger_print);
        }

        arsort($filter_1, SORT_NUMERIC);
        $filter_1 = array_slice($filter_1, 0, 50);

        $final_checked_keywords = [];
        foreach ($filter_1 as $filter_accepted_keyword => $filter_help_count)
        {
            $skip_word = $this->getMeaningfulPhrase($filter_accepted_keyword);

            if(empty($skip_word))
            {
                continue;
            }

            if (!isset($prepared_data[$skip_word]) || (isset($prepared_data[$skip_word]) && empty($prepared_data[$skip_word])) )
            {
                $this->matches[$skip_word] = $this->matches[$filter_accepted_keyword];
                $final_checked_keywords[$filter_accepted_keyword] = $this->prepareFilteredSubArrays($common_keywords, $filter_accepted_keyword);
            }
        }

        foreach ($this->matches as $word => $keyword_list)
        {
            $this->matches[$word] = array_unique($keyword_list);
        }

        $detail_data = [];
        foreach ($final_checked_keywords as $keyword => $details)
        {
            $detail_data[$keyword] = $details['parent']['count'];
        }

        arsort($detail_data, SORT_NUMERIC);
        $final_list = [];

        foreach ($detail_data as $keyword => $counts)
        {
            $final_list[] = $final_checked_keywords[$keyword];
        }

        return $final_list;
    }

    /**
     * Here we are merging the common data, based on the outer keyword stem.
     * So if there are outer keywords like: 'words' & 'word', then the data for 'words' will be merged along with 'word';
     *
     * @param array $common_keywords
     * @param $main_outer_keyword
     * @return array
     */
    public function mergeCommonWords(array $common_keywords, $main_outer_keyword)
    {
        $stemmed_main_outer_keyword = $this->keyword_service->getStemmedPhrase($main_outer_keyword);

        if (strlen($main_outer_keyword) - strlen($stemmed_main_outer_keyword) > 2)
        {
            $stemmed_main_outer_keyword = $main_outer_keyword;
        }

        if ($stemmed_main_outer_keyword != $main_outer_keyword && isset($common_keywords[$stemmed_main_outer_keyword]))
        {
            $this->matches[$stemmed_main_outer_keyword] = $this->matches[$main_outer_keyword];
            $common_keywords[$stemmed_main_outer_keyword] = array_merge($common_keywords[$stemmed_main_outer_keyword], $common_keywords[$main_outer_keyword]);
            unset($common_keywords[$main_outer_keyword]);
        }

        return $common_keywords;
    }

    public function mergeInnerCommonWords(array $common_keywords, $main_outer_keyword, array $finger_print)
    {
        $finger_print = [];
        $score = 0;
        $flag = 0;
        $keyword_sub_array = $common_keywords[$main_outer_keyword];
        $filter_score = count($keyword_sub_array);

        foreach ($keyword_sub_array as $sub_keyword => $org_keywords_array)
        {
            if (is_int($sub_keyword) || count($org_keywords_array) < 2)
            {
                unset($common_keywords[$main_outer_keyword][$sub_keyword]);
                continue;
            }

            //Removing duplicate arrays, under same outer_keyword - inner_keyword combination.
            $md5 = md5(implode(':', $org_keywords_array));
            if (!isset($finger_print[$md5][$sub_keyword]) || !in_array($main_outer_keyword, $finger_print[$md5][$sub_keyword]))
            {
                //Only keep one array under each md5 fingerprint of each outer_word
                if (isset($finger_print[$md5][$sub_keyword]) && count($finger_print[$md5][$sub_keyword]) == 1)
                {
                    unset($common_keywords[$main_outer_keyword][$sub_keyword]);
                }
                else
                {
                    $finger_print[$md5][$sub_keyword][$main_outer_keyword] = 1;
                }
            }

            $temp_word = "$main_outer_keyword $sub_keyword";
            $temp_word_stemmed = $this->keyword_service->getStemmedPhrase($temp_word);

            //Removing "outer_word $inner_word" combination from main outer list, if it exists.
            if (isset($common_keywords[$temp_word]) || isset($common_keywords[$temp_word_stemmed]))
            {
                unset($common_keywords[$temp_word]);
                unset($common_keywords[$temp_word_stemmed]);
                $filter_out[] = $temp_word;
                $filter_out[] = $temp_word_stemmed;
                $flag = 1;
                continue;
            }

            $score += 1;
        }

        if ($flag == 0)
        {
            $filter_score += $score;
        }

        return [$common_keywords, $filter_score, $finger_print];
    }

    public function prepareFilteredSubArrays(array $common_keywords, $main_common_keyword)
    {
        $filter_2 = [];
        $final_checked_keywords = [];
        foreach ($common_keywords[$main_common_keyword] as $sub_keyword => $child_keywords)
        {
            if (count($child_keywords) < 2)
            {
                continue;
            }

            $level_score = 0;
            foreach ($child_keywords as $keyword)
            {
                $level_score += count(explode(' ', $keyword));
            }

            $filter_2[$sub_keyword] = ( count(explode(' ', $sub_keyword)) * count($child_keywords) ) + $level_score;
        }

        arsort($filter_2, SORT_NUMERIC);
        //2nd tree child node limit to 10
        $filter_2 = array_slice(array_keys($filter_2), 0, 10);

        $data_detail_1 = $this->keyword_service->getDistinctPhrasesInKeywords($main_common_keyword, $this->matches[$main_common_keyword]);
        $final_checked_keywords['parent']['keyword'] = $main_common_keyword;
        $final_checked_keywords['parent']['count'] = $data_detail_1->count;
        $final_checked_keywords['parent']['distinct_words'] = $data_detail_1->distinct_matches;

        foreach ($filter_2 as $keyword)
        {
            $data_detail = $this->keyword_service->getDistinctPhrasesInKeywords($keyword, $data_detail_1->matches);

            if ($data_detail->count == 0)
            {
                continue;
            }

            $final_checked_keywords['child'][] = [
                'keyword' => $keyword,
                'count' => $data_detail->count,
                'distinct_words' => $data_detail->distinct_matches,
            ];
        }

        return $final_checked_keywords;
    }

    //Stop words at either end of the phrase will be removed.
    public function getMeaningfulPhrase($phrase)
    {
        $phrase = trim($phrase);

        if (empty($phrase))
            return '';

        $phrase_parts = explode(' ', $phrase);
        $phrase_parts_length = count($phrase_parts);


        if ($phrase_parts_length >= 2)
        {
            $phrase_parts = array_diff($phrase_parts, $this->stop_words[0]);
            $phrase_parts = array_diff($phrase_parts, $this->stop_words[1]);
        }
        else
        {
            $phrase_parts = array_diff($phrase_parts, $this->stop_words[0]);
        }

        $phrase_parts = array_diff($phrase_parts, $this->keyword_service->getStopWords());

        $temp_meaningful_word = trim(implode(' ', $phrase_parts));


        if (count($phrase_parts) == 1)
        {
            $temp_meaningful_word = $this->keyword_service->trimSpecialCharacters($temp_meaningful_word);
            $stemmed_word = $this->keyword_service->getStemmedPhrase($temp_meaningful_word);
            if (strlen($temp_meaningful_word) - strlen($stemmed_word) < 3 && strlen($stemmed_word) > 2)
            {
                $temp_meaningful_word = $stemmed_word;
            }
        }
        else
        {
            $temp_meaningful_word = trim($this->keyword_service->getStemmedPhrase($temp_meaningful_word));
        }

        return $temp_meaningful_word;
    }

    public function findCommonKeywords(array $input_keywords, $max_common_phrase_word_count = 3, $tree_level = 2)
    {
        $common_words = [];
        $word_separators = [ ' ',/* '-'*/ ];
        $tree_level_var = 1;

        foreach ($input_keywords as $keyword)
        {
            $words = [];
            foreach ($word_separators as $separators)
            {
                $words = array_merge($words, $this->getWordCombinations($keyword, $max_common_phrase_word_count, $separators));
            }

            foreach ($words as $word)
            {
                $word = trim($this->getMeaningfulPhrase($word));

                if (count(explode(' ', $word)) == 1)
                {
                    $word = $this->keyword_service->trimSpecialCharacters($word);
                }

                if (in_array($word, $this->keyword_service->getStopWords()) || strlen($word) <= 2 || is_numeric($word))
                {
                    continue;
                }

                if (!isset($common_words[0][$word]) || !in_array($keyword, $common_words[0][$word]))
                {
                    $singular_word = $this->keyword_service->singularize($word);

                    $length_diff = strlen($word) - strlen($singular_word);
                    if ($length_diff > 0 && $length_diff < 3 && !is_bool(strpos($word, $singular_word)) && strlen($singular_word) > 2)
                    {
                        $this->matches[$singular_word][] = $keyword;
                        $common_words[0][$singular_word][] = $keyword;
                        continue;
                    }

                    $this->matches[$word][] = $keyword;
                    $common_words[0][$word][] = $keyword;
                }
            }

        }



        if (!isset($common_words[0]) || empty($common_words[0]))
        {
            return [];
        }

        foreach ($common_words[0] as $main_common_word => $keywords_list)
        {
            if (count($keywords_list) < 3)
            {
                unset($common_words[0][$main_common_word]);
                continue;
            }

            $stemmed_word = $this->keyword_service->getStemmedPhrase($main_common_word);

            if (strlen($main_common_word) - strlen($stemmed_word) > 2)
            {
                $stemmed_word = $main_common_word;
            }

            if (isset($common_words[0][$stemmed_word]) && $stemmed_word != $main_common_word && !is_bool(strpos($main_common_word, $stemmed_word)))
            {
                $this->matches[$stemmed_word] = $this->matches[$main_common_word];
                $common_words[0][$stemmed_word] = array_merge($common_words[0][$stemmed_word], $common_words[0][$main_common_word]);
                unset($common_words[0][$main_common_word]);
                continue;
            }
        }

        foreach ($common_words[0] as $temp_common_word => $keyword_list_array)
        {
            if (count($keyword_list_array) < 2)
            {
                unset($common_words[0][$temp_common_word]);
            }
        }

        if (!isset($common_words[0]) || empty($common_words[0]))
        {
            return [];
        }

        foreach ($common_words[0] as $outer_common_word => $keyword_array)
        {

            if (count($keyword_array) < 3)
            {
                $common_words[1][$outer_common_word] = [];
                continue;
            }

            $temp_common_words = $this->getStagedCommonWords($keyword_array, $outer_common_word, $max_common_phrase_word_count);
            if(!empty($temp_common_words))
            {
                $common_words[1][$outer_common_word] = $temp_common_words;
            }
            elseif(count($keyword_array) > 2)
            {
                $common_words[1][$outer_common_word] = [];
            }

        }

        if (!isset($common_words[1]) && isset($common_words[0]))
        {
            return $common_words[0];
        }


        $filter_out_list_1 = [];
        foreach ($common_words[1] as $outer_common_word => $inner_word_list)
        {
            if (in_array($outer_common_word, $filter_out_list_1))
            {
                continue;
            }

            $stemmed_outer_common_word = $this->keyword_service->getStemmedPhrase($outer_common_word);

            if (isset($common_words[1][$stemmed_outer_common_word]) && $outer_common_word != $stemmed_outer_common_word)
            {
                $filter_out_list_1[] = $outer_common_word;
                unset($common_words[1][$outer_common_word]);
                continue;
            }
        }

        if (empty($common_words[1]) && isset($common_words[0]))
        {
            return $common_words[0];
        }

        foreach ($common_words[1] as $outer_common_word => $inner_word_list)
        {
            $exploded_outer_common_word = explode(' ', $outer_common_word);
            $num = 0;
            foreach ($exploded_outer_common_word as $word)
            {
                if (strlen($word) <= 3 && in_array($word, $this->keyword_service->getStopWords()))
                {
                    $num++;
                }
            }

            if ($num == count($exploded_outer_common_word))
            {
                unset($common_words[1][$outer_common_word]);
                continue;
            }

            foreach ($inner_word_list as $inner_word => $list_of_keywords)
            {
                if (count($list_of_keywords) < 3)
                {
                    unset($common_words[1][$outer_common_word][$inner_word]);
                    continue;
                }

                $exploded_inner_common_word = explode(' ', $inner_word);
                $num = 0;
                foreach ($exploded_inner_common_word as $word)
                {
                    if (strlen($word) <= 3 && in_array($word, $this->keyword_service->getStopWords()))
                    {
                        $num++;
                    }
                }

                if ($num == count($exploded_inner_common_word) || !is_bool(strpos($outer_common_word, $inner_word)))
                {
                    unset($common_words[1][$outer_common_word][$inner_word]);
                    continue;
                }

            }

        }

        return $common_words[1];
    }

    public function getStagedCommonWords(array $keyword_list, $given_outer_common_word = '', $max_common_phrase_word_count = 3)
    {
        $common_words = [];
        
        foreach ($keyword_list as $list_item_keyword)
        {
            $words = $this->getWordCombinations($list_item_keyword, $max_common_phrase_word_count);

            if (empty($given_outer_common_word))
            {
                $array_index = array_search($given_outer_common_word, $words, false);
                if (!is_bool($array_index))
                {
                    unset($words[$array_index]);
                }
            }

            foreach ($words as $word)
            {
                $word = $this->getMeaningfulPhrase($word);

                if (in_array($word, $this->keyword_service->getStopWords()) || $word == $given_outer_common_word || strlen($word) <= 2 || (!empty($given_outer_common_word) && !is_bool(strpos($word, $given_outer_common_word)) ) )
                {
                    continue;
                }

                $exploded_word = explode(' ', $word);
                $exploded_keyword = explode(' ', $list_item_keyword);

                if (count($exploded_keyword) > count( array_diff($exploded_keyword, $exploded_word) ) )
                {
                    continue;
                }

                if ((!isset($common_words[$word]) || !in_array($list_item_keyword, $common_words[$word])) && $given_outer_common_word != $list_item_keyword)
                {
                    $this->matches[$word][] = $list_item_keyword;
                    $common_words[$word][] = $list_item_keyword;
                }
            }
        }

        return $common_words;
    }

    public function getWordCombinations($keyword, $max_word_count = 3, $separator = ' ')
    {
        if(empty($keyword) || is_bool(strpos($keyword, $separator)))
        {
            return [];
        }

        $combinations = [];
        $keyword = preg_replace("/\s\s+/", " ", $keyword);

        $words = explode($separator, $keyword);
        foreach ($words as $index => $word)
        {
            $words[$index] = trim($word);
        }

        if($separator != ' ' && count($words) == 1)
        {
            return [];
        }
        elseif ($separator != ' ')
        {
            $combinations = $words;

            foreach ($words as $sub_word)
            {
                $sub_words = explode(' ', $sub_word);
                //I know the $max_word_count will make some result changes. No need to do a research to find it out. - Nikhil
                $sub_combinator = $this->combinator($sub_words, $max_word_count, ' ');
                $combinations = array_merge($combinations, $sub_combinator);
            }

            return $combinations;
        }

        $combinations = array_merge($combinations, $words, $this->combinator($words, $max_word_count, $separator));

        return array_unique(array_filter($combinations));

    }

    public function combinator($words, $max_word_count = 3, $separator = ' ')
    {
        $combinations = [];

        $keyword_word_count = count($words);
        $word_counter = 2;

        if($max_word_count > $keyword_word_count)
        {
            $max_word_count = $keyword_word_count;
        }

        $org_words = $words;

        while ($word_counter <= $max_word_count)
        {
            //for each works on a static array. The initial state of array will used.
            foreach ($org_words as $index => $word)
            {
                if(($index + $word_counter) <= $keyword_word_count )
                {
                    $combinations[] = implode($separator, array_slice($org_words, $index, $word_counter, true));
                }
                else
                {
                    break;
                }
            }

            $word_counter++;
        }


        foreach ($combinations as $index => $word)
        {
            $combinations[$index] = trim($word);
        }
        $combinations = array_unique(array_filter($combinations));

        return $combinations;
    }

    public function getMultiWords(array $keywords, $parent_word_count = 1)
    {
        $all_stop_words = array_merge($this->keyword_service->getStopWords(), $this->stop_words[0], $this->stop_words[1]);

        $key_string = implode(' ', $keywords );

        //One
        $input = $key_string;
        $one_word = explode('-|-', preg_replace('/(\S+)\s*/', '$1-|-', $input));
        $multi_word_keywords[1] = $one_word;

        //Two
        $input = $key_string;
        $two_words = explode('-|-', preg_replace('/(\S+\s+\S+)\s*/', '$1-|-', $input));
        $input = 'a ' . $key_string;
        $two_words = array_merge($two_words, explode('-|-', preg_replace('/(\S+\s+\S+)\s*/', '$1-|-', $input)));
        $multi_word_keywords[2] = $two_words;

        //Three
        $input = $key_string;
        $three_words = explode('-|-', preg_replace('/(\S+\s+\S+\s+\S+)\s*/', '$1-|-', $input));
        $input = 'a ' . $key_string;
        $three_words = array_merge($three_words, explode('-|-', preg_replace('/(\S+\s+\S+\s+\S+)\s*/', '$1-|-', $input)));
        $input = 'a a ' . $key_string;
        $three_words = array_merge($three_words, explode('-|-', preg_replace('/(\S+\s+\S+\s+\S+)\s*/', '$1-|-', $input)));
        $multi_word_keywords[3] = $three_words;

        //Four
        $input = $key_string;
        $four_words = explode('-|-', preg_replace('/(\S+\s+\S+\s+\S+\s+\S+)\s*/', '$1-|-', $input));
        $input = 'a ' . $key_string;
        $four_words = array_merge($four_words, explode('-|-', preg_replace('/(\S+\s+\S+\s+\S+\s+\S+)\s*/', '$1-|-', $input)));
        $input = 'a a ' . $key_string;
        $four_words = array_merge($four_words, explode('-|-', preg_replace('/(\S+\s+\S+\s+\S+\s+\S+)\s*/', '$1-|-', $input)));
        $input = 'a a a ' . $key_string;
        $four_words = array_merge($four_words, explode('-|-', preg_replace('/(\S+\s+\S+\s+\S+\s+\S+)\s*/', '$1-|-', $input)));
        $multi_word_keywords[4] = $four_words;

        foreach ($multi_word_keywords as $word_count => $words_array)
        {
            $multi_word_keywords[$word_count] = array_diff($words_array, $all_stop_words);

            $temp = array_count_values($multi_word_keywords[$word_count]);
            arsort($temp, SORT_NUMERIC);
            $multi_word_keywords[$word_count] = array_slice($temp, 0, 25, true);
        }

        return $multi_word_keywords;
    }

    public function keywordGroupingType3(array $keywords)
    {
        ini_set('memory_limit', '1024M');
        $this->updateStopWordsVar();

        $multi_word_keywords = $this->getMultiWords($keywords);
        $stage_1_data = [];

        foreach ($multi_word_keywords as $word_count => $words_count_array)
        {
            foreach ($words_count_array as $keyword => $count)
            {
                //if the keyword repetition occupancy is less than 5 times, skip it.
                if ($count < 5)
                {
                    continue;
                }

                $temp = [];
                if($word_count < 4)
                {
                    foreach ($keywords as $keyword_item)
                    {
                        if(!is_bool(strpos($keyword_item, $keyword)))
                        {
                            $temp[] = $keyword_item;
                        }
                    }
                }

                $stage_1_data[$keyword]['parent'] = [
                    'keyword' => $keyword,
                    'count' => count($temp) > 0 ? count($temp) : $count,
                    'distinct_words' => [$keyword],
                ];

                //Until the word count is less than 4, we will find the child.
                if($word_count < 4)
                {
                    //Stage_2 starting
                    $multi_word_keywords_stage_2 = $this->getMultiWords($temp, $word_count);

                    foreach ($multi_word_keywords_stage_2 as $word_count_stage_2 => $words_count_array_stage_2)
                    {
                        foreach ($words_count_array_stage_2 as $keyword_stage_2_2 => $count_2_2)
                        {
                            if ($keyword == $keyword_stage_2_2 || $count_2_2 < 5 || !is_bool(strpos($keyword, $keyword_stage_2_2)))
                            {
                                continue;
                            }

                            $stage_1_data[$keyword]['child'][$keyword_stage_2_2] = [
                                'keyword' => $keyword_stage_2_2,
                                'count' => $count_2_2,
                                'distinct_words' => [$keyword_stage_2_2],
                            ];
                        }
                    }
                }
            }
        }

        return array_values($stage_1_data);

    }

    public function waitForMe(array $master_keys)
    {
        foreach ($master_keys as $key => $cache_key)
        {
            if (Cache::has($cache_key))
            {
                $this->waitForMe($master_keys);
                sleep(1);
            }
            else
            {
                unset($master_keys[$key]);
            }
        }
    }

	public function array_str_pos(array $haystack, $needle)
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


}