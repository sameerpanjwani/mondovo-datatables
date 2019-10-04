<?php

use Illuminate\Support\Str;

/**
 * Created by PhpStorm.
 * User: Sameer
 * Date: 10-08-2016
 * Time: 13:35
 *
 * The purpose of this test is to make sure that data-col is drawn properly when filters are enabled + a test to ensure that table also contains just one instance of data-col for each column instead of multiple - that was an issue some time ago
 * Test written on 11th August, 2016 by Sameer Panjwani
 */
class DrawTableFilterTest extends BrowserKitTestCase
{

    protected $table;

    public function setUp()
    {
        parent::setUp();
        $this->table = App::make(\Mondovo\DataTable\DrawTable::class);
    }

    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();

    }


    public function test_data_cols_output_from_array_format()
    {

        $profile_ranking_insights_table_columns = $this->singleSearchEngineArrayFormat();

        $output = $this->table->setDataColToColumnDefinition($profile_ranking_insights_table_columns);
        $output = array_dot($output);
        $i=0;

        foreach($output as $key=>$value) {
            $this->assertContains("data-col:$i", $key);
            $i++;
        }

    }

    public function test_table_for_data_cols_from_array_format()
    {

        $profile_ranking_insights_table_columns = $this->singleSearchEngineArrayFormat();

        $output = $this->table->setTableId("Random")->setColumnDefinitions($profile_ranking_insights_table_columns)->enableFilter()->draw();

        $this->assertEquals(1,substr_count($output,"data-col='1'"));
        $this->assertEquals(1,substr_count($output,"data-col='8'"));
        $this->assertEquals(1,substr_count($output,"data-col='15'"));
    }

    public function test_data_cols_output_from_flat_format()
    {

        $profile_ranking_insights_table_columns = $this->arrayForSingleSearchEngineFlatFormat();

        $output = $this->table->setDataColToColumnDefinition($profile_ranking_insights_table_columns);


        $output = array_dot($output);

        $i=0;
        foreach($output as $key=>$value) {
            if(!Str::contains($key,"data-col:$i") &&  !Str::contains($value,"data-col:$i")){
                echo $key." OR ".$value." do not contain data-col:$i";
                $this->assertFalse(true);
            }
            $i++;
        }
    }

    public function test_table_for_data_cols_from_flat_format()
    {

        $profile_ranking_insights_table_columns = $this->arrayForSingleSearchEngineFlatFormat();

        $output = $this->table->setTableId("Random")->setColumnDefinitions($profile_ranking_insights_table_columns)->enableFilter()->draw();

        $this->assertEquals(1,substr_count($output,"data-col='1'"));
        $this->assertEquals(1,substr_count($output,"data-col='8'"));
        $this->assertEquals(1,substr_count($output,"data-col='15'"));
    }

    public function test_data_cols_multiple_nesting_array_format()
    {

        $profile_ranking_insights_table_columns_final = $this->drawMultiNestedTableArrayFormat();


        $output = $this->table->setDataColToColumnDefinition($profile_ranking_insights_table_columns_final);
       // dd($output);
        $j=0;
        $output = array_dot($output);
        foreach($output as $key=>$value) {
            $this->assertContains("data-col:$j", $key);
            $j++;
        }

    }

    public function test_table_for_data_cols_from_mulit_nested_array_format()
    {

        $profile_ranking_insights_table_columns = $this->drawMultiNestedTableArrayFormat();

        $output = $this->table->setTableId("Random")->setColumnDefinitions($profile_ranking_insights_table_columns)->enableFilter()->draw();
       // dd($output);
        $this->assertEquals(1,substr_count($output,"data-col='1'"));
        $this->assertEquals(1,substr_count($output,"data-col='8'"));
        $this->assertEquals(1,substr_count($output,"data-col='15'"));
    }

    /**
     * @expectedException Mondovo\DataTable\Exceptions\UnsupportedTypeException
     */
    public function test_for_exception_with_mixed_one_level_array()
    {
        $table = ["col_1","col_2","col_3"=>["col_4","I_AM_ODD"=>[]],"I_AM_ODD"=>[]];

        $this->table->setColumnDefinitions($table)->enableFilter()->draw();
    }

    /**
     * @expectedException Mondovo\DataTable\Exceptions\UnsupportedTypeException
     */
    public function test_for_exception_with_mixed_multi_nested_array()
    {
        //

        $table = ["col_1"=>["col_sdf"=>["colasdfas"=>[],"colas"=>[]]],"col_2"=>["I_AM_ODD","safasd"=>[]],"col_3"=>["col_4"=>[],"col_5"=>[]],"col_6"=>[]];

        $this->table->setColumnDefinitions($table)->enableFilter()->draw();
    }

    /**
     * @expectedException Mondovo\DataTable\Exceptions\UnsupportedTypeException
     */
    public function test_for_exception_with_mixed_multi_nested_array_last_one_flat()
    {

        //"col_6" at the end cannot be flat, goes against the type
        $table = ["col_1"=>["col_sdf"=>["colasdfas"=>[],"colas"=>[]]],"col_2"=>["asdfds"=>[],"safasd"=>[]],"col_3"=>["col_4"=>[],"col_5"=>[]],"I_AM_ODD"];

        $this->table->setColumnDefinitions($table)->enableFilter()->draw();
    }



    /**
     * @expectedException Mondovo\DataTable\Exceptions\UnsupportedTypeException
     */
    public function test_for_exception_with_mixed_multi_nested_array_another_variation()
    {
       $table=$this->drawMultiNestedTableArrayFormatMalformed();

        $this->table->setColumnDefinitions($table)->enableFilter()->draw();
    }



    /**
     * @return array
     */
    public function singleSearchEngineArrayFormat()
    {
        $search_engine_id = 1;
        $from_date = "16th May";
        $comparison_date = "16th June";
        $column_group_class = "odd";
        $search_engine_name = "Google";
        $from_date_for_data_table_filter = "16th May";
        $comparison_date_for_data_table_filter = "16th July";
        $ranking_Section = [
            "profile_ranking_details" => [
                "$from_date|data-filter-type:number|data-custom-title:<i> Ranking on - </i>" => [],
                "$comparison_date|class:default-hidden comparison-date-box |data-filter-type:number|data-custom-title:<i> Ranking on - </i>" => [],
                "profile_change^$search_engine_id|class:change-box $column_group_class|data-filter-type:number|data-custom-title:<i> - </i>Ranking Change" => []
            ]
        ];

        $visibility_section = [
            "profile_visibility_details^$search_engine_id|class:default-hidden $column_group_class" => [
                "visibility_$from_date^$search_engine_id|data-filter-type:number|class:default-hidden visibility-box $column_group_class|data-custom-title:<i>$search_engine_name Visibility on - </i>$from_date_for_data_table_filter" => [],
                "visibility_$comparison_date^$search_engine_id|data-filter-type:number|class:default-hidden comparison-visibility-box $column_group_class|data-custom-title:<i>$search_engine_name Visibility on - </i>$comparison_date_for_data_table_filter" => [],
                "profile_visibility_change^$search_engine_id|data-filter-type:number|class:default-hidden change-visibility-box $column_group_class|data-custom-title:<i>$search_engine_name - </i>Visibility Change" => []
            ]
        ];

        $traffic_flow_section = [
            "profile_traffic_flow_details^$search_engine_id|class:default-hidden $column_group_class" => [
                "traffic_flow_$from_date^$search_engine_id|data-filter-type:number|class:default-hidden traffic-flow-box $column_group_class|data-custom-title:<i>$search_engine_name Traffic Flow on - </i>$from_date_for_data_table_filter" => [],
                "traffic_flow_$comparison_date^$search_engine_id|data-filter-type:number|class:default-hidden comparison-traffic-flow-box $column_group_class|data-custom-title:<i>$search_engine_name Traffic Flow on - </i>$comparison_date_for_data_table_filter" => [],
                "profile_traffic_flow_change^$search_engine_id|data-filter-type:number|class:default-hidden change-traffic-flow-box $column_group_class|data-custom-title:<i>$search_engine_name - </i>Traffic Flow Change" => []
            ]
        ];


        $profile_search_volume_column = [
            "profile_search_volume^$search_engine_id|class:search-volume-box $column_group_class|data-filter-type:number|data-custom-title:<i>$search_engine_name - </i>Search Volume" => [],
        ];

        $profile_other_columns = [
            "profile_ranking_domain^$search_engine_id|class:default-hidden ranking-domain-box $column_group_class|data-filter-type:text|data-custom-title:<i>$search_engine_name - </i>Ranking Domain" => [],
            "profile_ranking_url^$search_engine_id|class:default-hidden ranking-url-box $column_group_class|data-filter-type:text|data-custom-title:<i>$search_engine_name - </i>Ranking URL" => [],
            "profile_cpc^$search_engine_id|class:default-hidden cpc-box $column_group_class|data-filter-type:number|data-custom-title:<i>$search_engine_name - </i>CPC" => [],
            "profile_result_type^$search_engine_id|class:default-hidden result-type-box $column_group_class|data-filter-type:select|data-select-id:result_type_$search_engine_id|data-custom-title:<i>$search_engine_name - </i>Result Type" => []
        ];

        // $profile_ranking_insights_table_columns = array_merge($profile_search_volume_column, $status_specific_columns, $profile_other_columns);

        // $this->table->setColumnDefinitions($profile_ranking_insights_table_columns);

        $keyword_related_columns = [
            'profile_keyword_id|class:hidden' => [],
            'profile_keyword_ranking|data-filter-type:text|min-width:300px' => []
        ];

        //$extra_column["keyword_processing_status$search_engine_id|class:hidden"] = [];
        //$extra_column["page_id$search_engine_id|class:hidden"] = [];

        $extra_column = [
            "keyword_processing_status$search_engine_id|class:hidden" => [],
            "page_id$search_engine_id|class:hidden" => []
        ];

        $profile_ranking_insights_table_columns = array_merge($keyword_related_columns, $extra_column, $profile_search_volume_column, $ranking_Section, $visibility_section, $traffic_flow_section, $profile_other_columns);
        return $profile_ranking_insights_table_columns;
    }

    /**
     * @return array
     */
    public function arrayForSingleSearchEngineFlatFormat()
    {
        $search_engine_id = 1;
        $from_date = "16th May";
        $comparison_date = "16th June";
        $column_group_class = "odd";
        $search_engine_name = "Google";
        $from_date_for_data_table_filter = "16th May";
        $comparison_date_for_data_table_filter = "16th July";
        $ranking_Section = [
            "profile_ranking_details" => [
                "$from_date|data-filter-type:number|data-custom-title:<i> Ranking on - </i>",
                "$comparison_date|class:default-hidden comparison-date-box |data-filter-type:number|data-custom-title:<i> Ranking on - </i>",
                "profile_change^$search_engine_id|class:change-box $column_group_class|data-filter-type:number|data-custom-title:<i> - </i>Ranking Change"
            ]
        ];

        $visibility_section = [
            "profile_visibility_details^$search_engine_id|class:default-hidden $column_group_class" => [
                "visibility_$from_date^$search_engine_id|data-filter-type:number|class:default-hidden visibility-box $column_group_class|data-custom-title:<i>$search_engine_name Visibility on - </i>$from_date_for_data_table_filter",
                "visibility_$comparison_date^$search_engine_id|data-filter-type:number|class:default-hidden comparison-visibility-box $column_group_class|data-custom-title:<i>$search_engine_name Visibility on - </i>$comparison_date_for_data_table_filter",
                "profile_visibility_change^$search_engine_id|data-filter-type:number|class:default-hidden change-visibility-box $column_group_class|data-custom-title:<i>$search_engine_name - </i>Visibility Change"
            ]
        ];

        $traffic_flow_section = [
            "profile_traffic_flow_details^$search_engine_id|class:default-hidden $column_group_class" => [
                "traffic_flow_$from_date^$search_engine_id|data-filter-type:number|class:default-hidden traffic-flow-box $column_group_class|data-custom-title:<i>$search_engine_name Traffic Flow on - </i>$from_date_for_data_table_filter",
                "traffic_flow_$comparison_date^$search_engine_id|data-filter-type:number|class:default-hidden comparison-traffic-flow-box $column_group_class|data-custom-title:<i>$search_engine_name Traffic Flow on - </i>$comparison_date_for_data_table_filter",
                "profile_traffic_flow_change^$search_engine_id|data-filter-type:number|class:default-hidden change-traffic-flow-box $column_group_class|data-custom-title:<i>$search_engine_name - </i>Traffic Flow Change"
            ]
        ];


        $profile_search_volume_column = [
            "profile_search_volume^$search_engine_id|class:search-volume-box $column_group_class|data-filter-type:number|data-custom-title:<i>$search_engine_name - </i>Search Volume",
        ];

        $profile_other_columns = [
            "profile_ranking_domain^$search_engine_id|class:default-hidden ranking-domain-box $column_group_class|data-filter-type:text|data-custom-title:<i>$search_engine_name - </i>Ranking Domain",
            "profile_ranking_url^$search_engine_id|class:default-hidden ranking-url-box $column_group_class|data-filter-type:text|data-custom-title:<i>$search_engine_name - </i>Ranking URL",
            "profile_cpc^$search_engine_id|class:default-hidden cpc-box $column_group_class|data-filter-type:number|data-custom-title:<i>$search_engine_name - </i>CPC",
            "profile_result_type^$search_engine_id|class:default-hidden result-type-box $column_group_class|data-filter-type:select|data-select-id:result_type_$search_engine_id|data-custom-title:<i>$search_engine_name - </i>Result Type"
        ];

        // $profile_ranking_insights_table_columns = array_merge($profile_search_volume_column, $status_specific_columns, $profile_other_columns);

        // $this->table->setColumnDefinitions($profile_ranking_insights_table_columns);

        $keyword_related_columns = [
            'profile_keyword_id|class:hidden',
            'profile_keyword_ranking|data-filter-type:text|min-width:300px'
        ];

        //$extra_column["keyword_processing_status$search_engine_id|class:hidden"] = [];
        //$extra_column["page_id$search_engine_id|class:hidden"] = [];

        $extra_column = [
            "keyword_processing_status$search_engine_id|class:hidden",
            "page_id$search_engine_id|class:hidden"
        ];

        $profile_ranking_insights_table_columns = array_merge($keyword_related_columns, $extra_column, $profile_search_volume_column, $ranking_Section, $visibility_section, $traffic_flow_section, $profile_other_columns);
        return $profile_ranking_insights_table_columns;
    }


    public function drawMultiNestedTableArrayFormat()
    {
        $from_date = "16th May";
        $comparison_date = "16th June";

        $from_date_for_data_table_filter = "16th May";
        $comparison_date_for_data_table_filter = "16th July";

        $search_engine_id_name_pair = [1 => "Google", 2 => "Yahoo", 3 => "Bing", 4 => "Baidu"];
        $search_engine_counter = 1;
        $no_of_fields_per_search_engine_id = 14;

        $starting_index_for_odd_even = 2;
        $col_classes = [];

        $extra_column = [];
        //$all_result_types = $this->getAllSerpResultTypes();
        $result_type_custom_filter = [];
        $default_sorting_columns = [];
        $profile_ranking_insights_table_columns_final = [];


        $keyword_related_columns = [
            'profile_keyword_id|class:hidden' => [],
            'profile_keyword_ranking|data-filter-type:text|min-width:300px' => []
        ];

        foreach ($search_engine_id_name_pair as $search_engine_id => $search_engine_name) {
            if ($search_engine_counter % 2 == 1) {
                $column_group_class = 'odd-column';
                if (!isset($col_classes[$column_group_class])) {
                    $col_classes[$column_group_class] = [];
                }
            } else {
                $column_group_class = 'even-column';
                if (!isset($col_classes[$column_group_class])) {
                    $col_classes[$column_group_class] = [];
                }
            }

            for ($i = $starting_index_for_odd_even; $i < $starting_index_for_odd_even + $no_of_fields_per_search_engine_id; $i++) {
                array_push($col_classes[$column_group_class], $i);
            }

            $starting_index_for_odd_even = $i;

            $ranking_section = $visibility_section = $traffic_flow_section = $status_specific_columns = [];


            $ranking_section = [
                "profile_ranking_details^$search_engine_id|class:$column_group_class" => [
                    "$from_date^$search_engine_id|class:$column_group_class|data-filter-type:number|data-custom-title:<i>$search_engine_name</i>Ranking as on $from_date_for_data_table_filter" => [],
                    "$comparison_date^$search_engine_id|class:default-hidden comparison-date-box $column_group_class|data-filter-type:number|data-custom-title:<i>$search_engine_name</i>Ranking as on $comparison_date_for_data_table_filter" => [],
                    "profile_change^$search_engine_id|class:change-box $column_group_class|data-filter-type:number|data-custom-title:<i>$search_engine_name</i>Ranking Change" => []
                ]
            ];

            $visibility_section = [
                "profile_visibility_details^$search_engine_id|class:default-hidden $column_group_class" => [
                    "$from_date^profile_visibility_score$search_engine_id|data-filter-type:number|class:default-hidden visibility-box $column_group_class|data-custom-title:<i>$search_engine_name</i>Visibility as on $from_date_for_data_table_filter" => [],
                    "$comparison_date^profile_visibility_score$search_engine_id|data-filter-type:number|class:default-hidden comparison-visibility-box $column_group_class|data-custom-title:<i>$search_engine_name</i>Visibility on $comparison_date_for_data_table_filter" => [],
                    "profile_visibility_change^$search_engine_id|data-filter-type:number|class:default-hidden change-visibility-box $column_group_class|data-custom-title:<i>$search_engine_name</i>Visibility Change" => []
                ]
            ];

            $traffic_flow_section = [
                "profile_traffic_flow_details^$search_engine_id|class:default-hidden $column_group_class" => [
                    "$from_date^profile_traffic_flow_score$search_engine_id|data-filter-type:number|class:default-hidden traffic-flow-box $column_group_class|data-custom-title:<i>$search_engine_name</i>Traffic Flow as on $from_date_for_data_table_filter" => [],
                    "$comparison_date^profile_traffic_flow_score$search_engine_id|data-filter-type:number|class:default-hidden comparison-traffic-flow-box $column_group_class|data-custom-title:<i>$search_engine_name</i>Traffic Flow as on $comparison_date_for_data_table_filter" => [],
                    "profile_traffic_flow_change^$search_engine_id|data-filter-type:number|class:default-hidden change-traffic-flow-box $column_group_class|data-custom-title:<i>$search_engine_name</i>Traffic Flow Change" => []
                ]
            ];

            $status_specific_columns = array_merge($ranking_section, $visibility_section, $traffic_flow_section);


            $profile_search_volume_column = [
                "profile_search_volume^$search_engine_id|class:search-volume-box $column_group_class|data-filter-type:number|data-custom-title:<i>$search_engine_name</i>Search Volume" => [],
            ];

            $profile_other_columns = [
                "profile_ranking_domain^$search_engine_id|class:default-hidden ranking-domain-box $column_group_class|data-filter-type:text|data-custom-title:<i>$search_engine_name</i>Ranking Domain" => [],
                "profile_ranking_url^$search_engine_id|class:default-hidden ranking-url-box $column_group_class|data-filter-type:text|data-custom-title:<i>$search_engine_name</i>Ranking URL" => [],
                "profile_cpc^$search_engine_id|class:default-hidden cpc-box $column_group_class|data-filter-type:number|data-custom-title:<i>$search_engine_name</i>CPC" => [],
                "profile_result_type^$search_engine_id|class:default-hidden result-type-box $column_group_class|data-filter-type:select|data-select-id:result_type_$search_engine_id|data-custom-title:<i>$search_engine_name</i>Result Type" => []
            ];

            $profile_ranking_insights_table_columns = array_merge($profile_search_volume_column, $status_specific_columns, $profile_other_columns);

            $default_sorting_columns["profile_traffic_flow^$search_engine_id"] = 'desc';


            $profile_ranking_insights_table_columns_final[strtolower($search_engine_name) . '|class:' . $column_group_class] = $profile_ranking_insights_table_columns;

            $extra_column["keyword_processing_status$search_engine_id|class:hidden"] = [];
            $extra_column["page_id$search_engine_id|class:hidden"] = [];


            $search_engine_counter++;
        }

        $profile_ranking_insights_table_columns_final = array_merge($keyword_related_columns, $profile_ranking_insights_table_columns_final, $extra_column);
        return $profile_ranking_insights_table_columns_final;
    }

    public function drawMultiNestedTableArrayFormatMalformed()
    {
        //There is an "I_AM_ODD" column nested in a flat format towards the end of the array

        $from_date = "16th May";
        $comparison_date = "16th June";

        $from_date_for_data_table_filter = "16th May";
        $comparison_date_for_data_table_filter = "16th July";

        $search_engine_id_name_pair = [1 => "Google", 2 => "Yahoo", 3 => "Bing", 4 => "Baidu"];
        $search_engine_counter = 1;
        $no_of_fields_per_search_engine_id = 14;

        $starting_index_for_odd_even = 2;
        $col_classes = [];

        $extra_column = [];
        //$all_result_types = $this->getAllSerpResultTypes();
        $result_type_custom_filter = [];
        $default_sorting_columns = [];
        $profile_ranking_insights_table_columns_final = [];


        $keyword_related_columns = [
            'profile_keyword_id|class:hidden' => [],
            'profile_keyword_ranking|data-filter-type:text|min-width:300px' => []
        ];

        foreach ($search_engine_id_name_pair as $search_engine_id => $search_engine_name) {
            if ($search_engine_counter % 2 == 1) {
                $column_group_class = 'odd-column';
                if (!isset($col_classes[$column_group_class])) {
                    $col_classes[$column_group_class] = [];
                }
            } else {
                $column_group_class = 'even-column';
                if (!isset($col_classes[$column_group_class])) {
                    $col_classes[$column_group_class] = [];
                }
            }

            for ($i = $starting_index_for_odd_even; $i < $starting_index_for_odd_even + $no_of_fields_per_search_engine_id; $i++) {
                array_push($col_classes[$column_group_class], $i);
            }

            $starting_index_for_odd_even = $i;

            $ranking_section = $visibility_section = $traffic_flow_section = $status_specific_columns = [];


            $ranking_section = [
                "profile_ranking_details^$search_engine_id|class:$column_group_class" => [
                    "$from_date^$search_engine_id|class:$column_group_class|data-filter-type:number|data-custom-title:<i>$search_engine_name</i>Ranking as on $from_date_for_data_table_filter" => [],
                    "$comparison_date^$search_engine_id|class:default-hidden comparison-date-box $column_group_class|data-filter-type:number|data-custom-title:<i>$search_engine_name</i>Ranking as on $comparison_date_for_data_table_filter" => [],
                    "profile_change^$search_engine_id|class:change-box $column_group_class|data-filter-type:number|data-custom-title:<i>$search_engine_name</i>Ranking Change" => []
                ]
            ];

            $visibility_section = [
                "profile_visibility_details^$search_engine_id|class:default-hidden $column_group_class" => [
                    "$from_date^profile_visibility_score$search_engine_id|data-filter-type:number|class:default-hidden visibility-box $column_group_class|data-custom-title:<i>$search_engine_name</i>Visibility as on $from_date_for_data_table_filter" => [],
                    "$comparison_date^profile_visibility_score$search_engine_id|data-filter-type:number|class:default-hidden comparison-visibility-box $column_group_class|data-custom-title:<i>$search_engine_name</i>Visibility on $comparison_date_for_data_table_filter" => [],
                    "profile_visibility_change^$search_engine_id|data-filter-type:number|class:default-hidden change-visibility-box $column_group_class|data-custom-title:<i>$search_engine_name</i>Visibility Change" => []
                ]
            ];

            $traffic_flow_section = [
                "profile_traffic_flow_details^$search_engine_id|class:default-hidden $column_group_class" => [
                    "$from_date^profile_traffic_flow_score$search_engine_id|data-filter-type:number|class:default-hidden traffic-flow-box $column_group_class|data-custom-title:<i>$search_engine_name</i>Traffic Flow as on $from_date_for_data_table_filter" => [],
                    "$comparison_date^profile_traffic_flow_score$search_engine_id|data-filter-type:number|class:default-hidden comparison-traffic-flow-box $column_group_class|data-custom-title:<i>$search_engine_name</i>Traffic Flow as on $comparison_date_for_data_table_filter" => [],
                    "profile_traffic_flow_change^$search_engine_id|data-filter-type:number|class:default-hidden change-traffic-flow-box $column_group_class|data-custom-title:<i>$search_engine_name</i>Traffic Flow Change" => []
                ]
            ];

            $status_specific_columns = array_merge($ranking_section, $visibility_section, $traffic_flow_section);


            $profile_search_volume_column = [
                "profile_search_volume^$search_engine_id|class:search-volume-box $column_group_class|data-filter-type:number|data-custom-title:<i>$search_engine_name</i>Search Volume" => [],
            ];

            $profile_other_columns = [
                "profile_ranking_domain^$search_engine_id|class:default-hidden ranking-domain-box $column_group_class|data-filter-type:text|data-custom-title:<i>$search_engine_name</i>Ranking Domain" => [],
                "profile_ranking_url^$search_engine_id|class:default-hidden ranking-url-box $column_group_class|data-filter-type:text|data-custom-title:<i>$search_engine_name</i>Ranking URL" => [],
                "profile_cpc^$search_engine_id|class:default-hidden cpc-box $column_group_class|data-filter-type:number|data-custom-title:<i>$search_engine_name</i>CPC" => [],
                "profile_result_type^$search_engine_id|class:default-hidden result-type-box $column_group_class|data-filter-type:select|data-select-id:result_type_$search_engine_id|data-custom-title:<i>$search_engine_name</i>Result Type" => [],
                "I_AM_ODD"
            ];

            $profile_ranking_insights_table_columns = array_merge($profile_search_volume_column, $status_specific_columns, $profile_other_columns);


            $default_sorting_columns["profile_traffic_flow^$search_engine_id"] = 'desc';


            $profile_ranking_insights_table_columns_final[strtolower($search_engine_name) . '|class:' . $column_group_class] = $profile_ranking_insights_table_columns;

            $extra_column["keyword_processing_status$search_engine_id|class:hidden"] = [];
            $extra_column["page_id$search_engine_id|class:hidden"] = [];


            $search_engine_counter++;
        }

        $profile_ranking_insights_table_columns_final = array_merge($keyword_related_columns, $profile_ranking_insights_table_columns_final, $extra_column);
        return $profile_ranking_insights_table_columns_final;
    }




}
