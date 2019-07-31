@if($column_filters != "")
    <div class="mv-table-custom-filter text-right">
        <a class="btn secondary-btn mv-margin-bottom-md add-filter" tableId="{{ $table_id }}">
            <i class="fa fa-filter"></i>
            <span>Apply Filters</span>
        </a>
        <div class="table-overlay" id="{{ $table_id }}_filter_overlay">
            <div class="filter-container">
                <div class="filter-text text-left">
                    <div class="overlay-heading">Filter</div>
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

                        {!! $column_filters !!}
                        {{--<div class='panel panel-default filtered'>
                            <a role='button' data-toggle='collapse' data-parent='#accordion' href='#collapseOne' aria-expanded='true' aria-controls='collapseOne'>
                                <div class='panel-heading' role='tab' id='headingOne'>
                                    <h4 class="panel-title">
                                        <svg>
                                            <use class='filter-line' xlink:href='/img//svg-sprites/manage-alerts.svg#filter-line'></use>
                                            <use class='filter-solid' style='display: none' xlink:href='/img//svg-sprites/manage-alerts.svg#filter-solid'></use>
                                        </svg>
                                        Alert Content
                                    </h4>
                                    <div class='alerts-tag'>
                                        <div class='input-text'>Contains : <strong>aut</strong></div>
                                        <div class='input-text'>Contains : <strong>Quod</strong></div>
                                        <div class='input-text'>Contains : <strong>Enim</strong></div>
                                    </div>
                                </div>
                            </a>
                            <div id='collapseOne' class='panel-collapse collapse' role='tabpanel' aria-labelledby='headingOne'>
                                <div class='panel-body'>
                                    <div class='form-group margin-bottom-0'>
                                        <form>
                                            <select class='form-control margin-top-bottom-sm'>
                                                <option value='0'>=</option>
                                            </select>
                                            <input type='text' class='form-control' name='fname'>
                                            <div>
                                                <button type='button' class='close_filter btn default btn-med' onclick='hideTableColumnFilters(this)'>Close</button>
                                                <button type='button' class='btn blue btn-med global-filter-btn' onclick='addTableColumnFilter(this)'>Add Filter</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                       <div class="panel panel-default">
                           <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                               <div class="panel-heading" role="tab" id="headingTwo">
                                   <h4 class="panel-title">
                                       <svg>
                                           <use class="filter-line" xlink:href="/img//svg-sprites/manage-alerts.svg#filter-line"></use>
                                           <use class="filter-solid" style="display: none" xlink:href="/img//svg-sprites/manage-alerts.svg#filter-solid"></use>
                                       </svg>
                                       Module Name
                                   </h4>
                                   <div class='alerts-tag'>
                                   </div>
                               </div>
                           </a>
                           <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                               <div class="panel-body">
                                   <div class="form-group margin-bottom-0">
                                       <form>
                                           <select class="form-control margin-top-bottom-sm">
                                               <option value="0">=</option>
                                               <option value="1">Not Equals</option>
                                               <option value="2">&lt;</option>
                                               <option value="3">&gt;</option>
                                               <option value="4">&lt;=</option>
                                               <option value="5">&gt;=</option>
                                               <option value="6">Between</option>
                                               <option value="7">Is Empty</option>
                                               <option value="8">Is Not Empty</option>
                                           </select>
                                           <input type="text" class="form-control" name="fname">
                                           <div>
                                               <button type="button" class="btn blue btn-med global-filter-btn" onclick="addTableColumnFilter(this)">Add Filter</button>
                                           </div>
                                       </form>
                                   </div>
                               </div>
                           </div>
                       </div>--}}

                    </div>

                    <button type="button" class="btn mv-btn-primary apply-filter" id="emailSchedulerTable_filter_now_5" tableId="{{ $table_id }}">Apply Filter</button>
                </div>
            </div>
            <button class="close-new mv-animate-close hide-table-filter" type="button" tableId="{{ $table_id }}">×</button>
        </div>

    </div>
@endif

<?php if($toolbar_visibility) { ?>
<div class="dataTable-toolbar" data-name="{{$table_id}}_toolbar">
    <?php if(!empty($predefined_filters_in_toolbar) || $toolbar_contents != "" ) { ?>
    <?php if( $toolbar_contents != "") { ?>
        <?= $toolbar_contents ?>
        <?php } ?>
    <ul class="nav navbar-nav dtblToolbarLeftNav pull-left">
        {{--<li><a href="javascript:void(0);" class="datatableSearcShowBtn"><i class="fa fa-search-plus"></i></a>--}}
        {{--</li>--}}
        {{--Toolbar Pre Filter section--}}
        <?php if(!empty($predefined_filters_in_toolbar)) { ?>
        <li class="dropdown">
            <a href="#" data-name="{{$table_id}}_predifined_filters" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                Quick Filters
                <span class="datatable-toolbar-selected-text-container hidden">
                        <span class="fa fa-angle-right"></span>
                        <span class="datatable-toolbar-selected-text"></span>
                    </span>
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                @foreach($predefined_filters_in_toolbar as $filters)
                    @if(is_array($filters['pre_filter_condition']))
                        <li overwrite-existing-filter="{{$filters['overwrite_existing_filter']}}">

                            <a href="javascript:void(0)" data-table-id='{{$filters['table_id']}}'
                               @foreach($filters['pre_filter_condition'] as $attribute_name => $attribute_value)
                               {{$attribute_name . "=" . $attribute_value . " "}}
                               @endforeach
                               @if(!empty($filters['filter_id']))
                               data-filter-id="{{$filters['filter_id']}}"
                                    @endif
                            >{!! $filters['filter_title'] !!}</a>

                        </li>
                    @else
                        <li class='pre-filter-link-toolbar' table-id='{{$filters['table_id']}}'
                            pre-filter-condition='{{$filters['pre_filter_condition']}}'
                            overwrite-existing-filter="{{$filters['overwrite_existing_filter']}}"

                            @if(!empty($filters['filter_id']))
                            data-filter-id="{{$filters['filter_id']}}"
                                @endif
                        >
                            <a href="javascript:void(0)">{!! $filters['filter_title'] !!}</a></li>
                    @endif
                @endforeach
            </ul>
        </li>
        <?php } ?>


    </ul>
    <?php } ?>

    <?php if($export_button_visibility) { ?>
    {{--Toolbar Right Navigation item section--}}
    <ul class="nav navbar-nav dtblToolbarRightNav pull-right">
        @if($operations)
            <li class="dropdown" id="{{$table_id}}_table_operations">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" table-id="{{$table_id}}" title="Copy column values to clipboard" onclick="choose_column(this);">
                    <i class="fa fa-clipboard"></i>
                </a>
            </li>
        @endif
        <li class="dropdown"><a href="#" data-name="{{$table_id}}_export_options" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                aria-expanded="false"><i class="fa fa-copy"></i> Export <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">

                {{--<li><a class="export-datatable" file-type="pdf" table-id="{{$table_id}}" report-name="{{$export_report_name}}" report-date="{{$export_report_date}}" strip-columns="{{$export_strip_columns}}" delimiter="{{$excel_column_delimiter}}" token="{!! csrf_token() !!}">Download as PDF</a></li>--}}

                <li><a class="export-datatable" file-type="csv" table-id="{{$table_id}}" report-name="{{$export_report_name}}" report-date="{{$export_report_date}}" strip-columns="{{$export_strip_columns}}" delimiter="{{$excel_column_delimiter}}" token="{!! csrf_token() !!}">Export as CSV</a></li>

                <li><a class="export-datatable" file-type="xlsx" table-id="{{$table_id}}" report-name="{{$export_report_name}}" report-date="{{$export_report_date}}" strip-columns="{{$export_strip_columns}}" delimiter="{{$excel_column_delimiter}}" token="{!! csrf_token() !!}">Export as EXCEL</a></li>

                <!--<li class="divider"></li>
                <li><a href="#">Print preview</a></li>-->
            </ul>
        </li>
        @if(!is_bool($keyword_group_filter))
            <li class="dropdown keyword-groups-outer keyword-group-filter-dropper" table-id="{{$table_id}}" id="{{$table_id}}_keyword_groups" col-name="{{$keyword_group_filter['column_name']}}" col-index="{{$keyword_group_filter['column_index']}}">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" table-id="{{$table_id}}">
                    <i class="fa fa-list-ul"></i> Keyword Groups <span class="caret"></span>
                </a>
                <progress id="{{$table_id}}_keyword_groups_progress" value="0" max="100" min="0" class="keyword-group-progress"></progress>
            </li>
        @endif
    </ul>
    <?php } ?>


    <div class="clearfix"></div>

    {{--Toolbar Filter item section--}}
    <div id='{{$table_id}}_filter' style="display:none;">
        <ul class="nav navbar-nav dtblToolbarLeftNav pull-left">
            <div class="filter_conditions_container"></div>
            <li class="filter_conditions_tools">
                <a href="javascript:void(0);" class="clear_all_filters pull-left" tableId="{{$table_id}}" data-filter-clear-id="{{$table_id}}__clear_all">
                    <i class="fa fa-minus-circle"></i> Clear All
                </a>
            </li>
        </ul>
    </div>
</div>
<?php } ?>
