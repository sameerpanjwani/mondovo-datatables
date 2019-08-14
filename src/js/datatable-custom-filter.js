var MvDataTableFilterDesign = {

    CssClassCollection: {
        filter_conditions_container: 'filter_conditions_container',
        filter_conditions_tools: 'filter_conditions_tools',
        clear_all_filters: 'clear_all_filters',
        and_column_input: 'and_column_input',
        and_column_value: 'and_column_value',
        or_column_value: 'or_column_value',
        clear_filter: 'clear_filter',
        column_filter_btn: 'column_filter_btn',
        column_filter_container: 'column_filter_container',
        column_filter_condition: 'column_filter_condition',
        close_filter: 'close_filter',
        operator_select: 'operator_select',
        operand_text: 'operand_text',
        filter_now: 'filter_now',
        filter_now_from_contains_modal: 'filter_now_from_contains_modal',
        open_contains_multiple_modal: 'open_contains_multiple_modal',
        open_does_not_contain_multiple_modal: 'open_does_not_contain_multiple_modal',
        contains_multiple_close: 'contains-multiple-close',
        table_header_column_title: 'table-header-column-title',
        pre_filter_link: 'pre-filter-link',
        pre_filter_link_toolbar: 'pre-filter-link-toolbar',
        special_pre_filter_link_toolbar: 'special-pre-filter-link-toolbar',
        keyword_group_filter_dropper: 'keyword-group-filter-dropper',
        keyword_group_special_pre_filter_link_toolbar: 'keyword-group-special-pre-filter-link-toolbar',
        reset_all_filter: 'reset-all-filter',
        datatable_toolbar_selected_text_container: "datatable-toolbar-selected-text-container",
        datatable_toolbar_selected_text: "datatable-toolbar-selected-text",
        add_to_tag_manager_class: "add-to-tag-manager",
        add_to_keyword_manager_class: "add-to-keyword-manager",
        add_to_page_manager_class: "add-to-page-manager",
        copy_to_clipboard_class: "copy-to-clipboard",
        copy_to_filter_class: "copy-to-filter",
        bulk_generate_kd_class: "bulk-kd-process",
        add_to_tag_manager_modal_id: 'add-to-tag-manager-modal',
        add_to_keyword_manager_modal_id: 'add-to-keyword-manager-modal',
        add_to_page_manager_modal_id: 'add-to-page-manager-modal',
        selected_attr_kd_id: 'selected_attr_kd',
        selected_attr_tm_id: 'selected_attr_tm',
        selected_attr_km_id: 'selected_attr_km',
        selected_attr_pm_id: 'selected_attr_pm',
        select_manager_class: 'select-manager',
        add_from_manager_class: 'add-from-manager',
        filter_from_manager_class: 'filter-from-manager',
        filter_from_manager_in_built_class: 'filter-from-manager-in-built',
        keyword_attribute_class: 'keyword-attribute',
        filter_modal_class: 'filter-modal',
        custom_table_options: 'custom-table-options'
    },
    sameConditionMsg: 'You have already added this condition',
    filterConditionMsg: 'Are you sure you want to clear the selected filters? ',
    blankOperatorConditionMsg: 'You have not selected any filtering criteria.',
    overWriteExistingFilterMsg: 'Are you sure you want to overwrite your existing filter? ',
    maxSourceAddedMsg: "You cannot add more than <max> <type>",
    create_new_tag: "Create New",
    exportReportMessage: "Your report is being generated",
    exportReportFailureMessage: "Sorry, unable to export the report",
    managerGuestUserErrorMessage: "Please login/register to access this feature",
    clearAllFilterCallback: false,
    shouldRefreshKgData: false,

    ColumnFilter: function (tableId, column_index) {
        return '<div  class="column_filter_container dropdown">' +
            '<button type="button" class="column_filter_btn dropdown-toggle  selectpicker green" data-toggle="dropdown" title="FilterBy">' +
            '<i class="fa fa-filter"></i>' +
            '</button>' +
            '<ul class="column_filter_condition dropdown-menu dropdown-menu-right" role="menu">' +
            '<li></li>' +
            '<li></li>' +
            '<li>' +
            '<button type="button" class="close_filter btn default btn-med">Close</button>' +
            '<button type="button" class="filter_now btn blue btn-med" tableId="' + tableId + '" id="' + tableId + '_filter_now_' + column_index +'">Filter</button>' +
            '</li>' +
            '</ul>' +
            '<div class="clearfix"></div>' +
            '</div>';
    },
    DropDown: function (refId, optionArray, table_id, column_index) {
        var contains_modal_id_part = '';
        if(typeof table_id != 'undefined')
            contains_modal_id_part = table_id + 'FilterTextArea' + column_index;

        var filterSelectTag = '<select id="' + refId + '" class="operator_select form-control" table-id="' + table_id + '" column-index="' + column_index + '" data-contains-modal-id-part="' + contains_modal_id_part + '">';
        for (var i = 0; i < optionArray.length; i++) {
            filterSelectTag += '<option value="' + i + '">' + optionArray[i] + '</option>'
        }
        filterSelectTag += '</select>';
        return filterSelectTag;
    },
    SecondDropDown: function (refId, selectId) {
        selectHtml = '<select id="' + refId + '" class="form-control">';
        var dropdown = MVMondovo[selectId];
        selectHtml += '<option value="">' + dropdown.blankLabel + '</option>';
        for (var i = 0; i < dropdown.list.length; i++) {
            selectHtml += '<option value="' + dropdown.list[i].value + '">' + dropdown.list[i].text + '</option>';
        }
        selectHtml += '</select>';
        return selectHtml;
    },
    TextField: function (refId) {
        return '<input type="text" id="' + refId + '"  class="form-control operand_text mx_dt_text" >';
    },
    TextArea: function (refId) {
        return '<div class="mx_dt_text_area_container" style="display: none;" ><textarea id="' + refId + '"  class="form-control operand_text mx_dt_text_area" ></textarea> <label class="mx_dt_radio_label"><input name="' + refId + '_radio" type="radio" id="' + refId + '_radio_all" value="all" checked > All</label>&nbsp;&nbsp;<label class="mx_dt_radio_label"><input name="' + refId + '_radio" type="radio" id="' + refId + '_radio_any_one" value="any" > Any one</label></div>';
    },
    ContainsMultipleModal: function(table_id, column_index){
        var refId = table_id + "FilterTextArea" + column_index;
        return '<div id="contains_multiple_modal_for_' + refId + '" data-keyboard="true" class="modal fade in new-modal-styles" aria-hidden="true">' +
            '<div class="modal-dialog modal-lg width-70">' +
            '<div class="modal-content">' +
            '<div class="modal-header">' +
            '<button aria-hidden="true" data-dismiss="modal" class="close contains-multiple-close" type="button" id="close_button" table-id="' +table_id + '" column-index="' + column_index + '"></button>' +
            '<h4 class="modal-title">Contains Multiple</h4>' +
            '</div>' +
            '<div class="modal-body nopadding">' +
            '<div class="form">' +
            '<div class="form_content"> <div class="form-horizontal form-bordered">' +
            '<div class="form-group form-md-line-input" style="padding-bottom: 0px !important;">' +
            '<div class="control-label col-xs-12 col-sm-4 col-md-4 col-lg-3">' +
            '<label for="select_tags">Enter Keywords:<div style="font-size:11px;text-align:center;">(one keyword/phrase per line)</div></label>' +
            '</div>' +
            '<div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">' +
            '<div class="input-xlarge input-inline">' +
            '<textarea id="' + refId + '"  class="'+table_id+' form-control operand_text mx_dt_text_area"></textarea>' +
            '</div>' +
            '<div class="input-xlarge" style="margin-top: 5px;">' +
            '<span class="pull-right">' +
            '<a data-toggle="modal" href="#" class="btn mini add-from-manager" data-type="keywords" data-manager-type="tag-pages" data-source-selector="' + refId + '" data-max-attr-allowed="5000"><i class="fa fa-bitbucket"></i> Import Keywords </a>' +
            '</span>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="form-group form-md-line-input" style="padding-top: 5px !important;">' +
            '<div class="control-label col-xs-12 col-sm-4 col-md-4 col-lg-3">' +
            '<label for="select_tags">Contains:</label>' +
            '</div>' +
            '<div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">' +
            '<div class="md-radio-inline">' +
            '<div class="md-radio">' +
            '<input name="' + refId + '_radio" type="radio" id="' + refId + '_radio_any_one" value="any" checked="checked" radio_text="Any One"><label for="' + refId + '_radio_any_one"><span class="inc"></span><span class="check"></span><span class="box"></span>Any One</label>' +
            '</div>' +
            '<div class="md-radio">' +
            '<input name="' + refId + '_radio" type="radio" id="' + refId + '_radio_all" value="all" radio_text="All"><label for="' + refId + '_radio_all"><span class="inc"></span><span class="check"></span><span class="box"></span>All</label>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="form-actions clearfix">' +
            '<div class="col-md-offset-3 col-md-9 col-lg-offset-3 col-lg-9 margin-bottom-20">' +
            '<button type="button" data-dismiss="modal" class="filter_now_from_contains_modal btn blue btn-med" table-id="' +table_id + '" column-index="' + column_index + '">Filter</button>' +
            '<button type="button" data-dismiss="modal" class="btn default btn-med contains-multiple-close" table-id="' +table_id + '" column-index="' + column_index + '">Close</button>' +
            '</div>' +
            '</div>' +
            '</div></div>' +
            '</div>' +
            '</div>'
        '</div>' +
        '</div>' +
        '</div>';
    },
    DoesNotContainsMultipleModal: function(table_id, column_index){
        var refId = table_id + "FilterTextArea" + column_index;
        return '<div id="does_not_contain_multiple_modal_for_' + refId + '" data-keyboard="true" class="modal fade in new-modal-styles" aria-hidden="true">' +
            '<div class="modal-dialog modal-lg width-70">' +
            '<div class="modal-content">' +
            '<div class="modal-header">' +
            '<button aria-hidden="true" data-dismiss="modal" class="close contains-multiple-close" type="button" id="close_button" table-id="' +table_id + '" column-index="' + column_index + '"></button>' +
            '<h4 class="modal-title">Does Not Contains Multiple</h4>' +
            '</div>' +
            '<div class="modal-body nopadding">' +
            '<div class="form">' +
            '<div class="form_content"> <div class="form-horizontal form-bordered">' +
            '<div class="form-group form-md-line-input" style="padding-bottom: 0px !important;">' +
            '<div class="control-label col-xs-12 col-sm-4 col-md-4 col-lg-3">' +
            '<label for="select_tags">Enter Keywords:<div style="font-size:11px;text-align:center;">(one keyword/phrase per line)</div></label>' +
            '</div>' +
            '<div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">' +
            '<div class="input-xlarge input-inline">' +
            '<textarea id="' + refId + '_does_not"  class="'+table_id+' form-control operand_text mx_dt_text_area"></textarea>' +
            '</div>' +
            '<div class="input-xlarge" style="margin-top: 5px;">' +
            '<span class="pull-right">' +
            '<a data-toggle="modal" href="#" class="btn mini add-from-manager" data-type="keywords" data-manager-type="tag-pages" data-source-selector="' + refId + '_does_not" data-max-attr-allowed="5000"><i class="fa fa-bitbucket"></i> Import Keywords </a>' +
            '</span>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="form-actions clearfix">' +
            '<div class="col-md-offset-3 col-md-9 col-lg-offset-3 col-lg-9 margin-bottom-20">' +
            '<button type="button" data-dismiss="modal" class="filter_now_from_contains_modal btn blue btn-med" table-id="' +table_id + '" column-index="' + column_index + '">Filter</button>' +
            '<button type="button" data-dismiss="modal" class="btn default btn-med contains-multiple-close" table-id="' +table_id + '" column-index="' + column_index + '">Close</button>' +
            '</div>' +
            '</div>' +
            '</div></div>' +
            '</div>' +
            '</div>'
        '</div>' +
        '</div>' +
        '</div>';
    },
    TextFieldForNumber: function (refId, select2_id, tableId, column_index) {
        return '<input type="text" id="' + refId + '_1"  class="form-control operand_text" >' +
            '<div id="number_filter_to_div' + tableId + column_index + '" style="display: none">' +
            '<input type="text" id="' + refId + '_2"  class="form-control operand_text" placeholder="To">' +
            '</div>' +
            "<script type='text/javascript'> $('#" + select2_id + "').on('change', function () { var between_selected = 6; var selected_val = $('#" + select2_id + "').val(); if (between_selected == selected_val) { $('#number_filter_to_div" + tableId + column_index + "').show('slow'); $('#" + refId + "_1').attr('placeholder', 'From'); } else { $('#number_filter_to_div" + tableId + column_index + "').hide('slow'); $('#" + refId + "_1').removeAttr('placeholder'); } });  </script>";
    },
    OrField: function (or_column_value) {
        return ' &nbsp; <label class="or_column_input">OR</label> &nbsp; <label class="or_column_value">' + or_column_value + '</label>';
    },
    AndField: function (column_title, column_operator, and_column_value, column_index, table_id, operator_index_value, filter_clear_id, all_values) {
        if(filter_clear_id === undefined)
        {
            filter_clear_id = table_id + '__' + column_index + MvDataTableFilter.filterOperatorUniqueId(column_operator) + 'clear_filter';
        }
        else
        {
            return '<li class = "and_column_input' + column_index + operator_index_value + '">' +
                '<span>' +
                '<label class="column_title">' + uc_words(filter_clear_id.split('_').join(' ')) + '</label> &nbsp; ' +
                '</span>' +
                '<a class="clear_filter"' +
                'table_id="' + table_id + '" column_index="' + column_index + '" data-filter-clear-id="' + filter_clear_id + '" operator_value="' + column_operator + '"  operator_index_value="' + operator_index_value + '" >' +
                '<i class="fa fa-times"></i>' +
                '</a>' +
                '</li>';
        }

        var temp_column_operator = column_operator;
        if(column_operator == 'Contains (multiple)' || column_operator == 'Does not contain (multiple)') {

            var num_keywords = and_column_value.split('\n').length;
            if(column_operator == 'Contains (multiple)')
            {
                all_values = (all_values == 'all') ? all_values : 'any one of the';
                and_column_value = '<label class="open_contains_multiple_modal" table-id="' + table_id + '" column-index="' + column_index + '" style="cursor: pointer;">Contains ' + all_values + ' ' + num_keywords + ' keywords</label>';
            }else{
                and_column_value = '<label class="open_does_not_contain_multiple_modal" table-id="' + table_id + '" column-index="' + column_index + '" style="cursor: pointer;">Does not contain any of the ' + num_keywords + ' keywords</label>';
            }
            temp_column_operator = '';
        }

        var close = '<a class="clear_filter"' +
            'table_id="' + table_id + '" column_index="' + column_index + '" data-filter-clear-id="' + filter_clear_id + '" operator_value="' + column_operator + '"  operator_index_value="' + operator_index_value + '" >' +
            '<i class="fa fa-times"></i>' +
            '</a>';

        var column_operator_html = (temp_column_operator == '') ? '' : '<label class="column_operator">' + temp_column_operator + '</label> &nbsp; ';
        return '<li class = "and_column_input' + column_index + operator_index_value + '">' +
            '<span>' +
            '<label class="column_title">' + column_title + '</label> &nbsp; ' +
            column_operator_html +
            '<label class="and_column_value">' + and_column_value + '</label> &nbsp; ' +
            '</span>' + close +
            '</li>';
    }
};

var MvDataTableFilter = function () {
    var self_ref = this;

    var post_filter_clear_callback = [];

    var save_view_state_callback = {};

    var css_class = MvDataTableFilterDesign.CssClassCollection;

    var DataTableFilterSettings = {
        text: ["=", "Not Equals", "Contains", "Does not contain", "Contains (multiple)", "Does not contain (multiple)", "Starts With", "Ends With", "Is Empty", "Is Not Empty"],
        select: ["=", "Not Equals"],
        filter: ['In Tags', 'In Keywords', 'In Pages', 'Is of', 'Not In Tags', 'Not In Keywords', 'Not In Pages'],
        number: ['=', "Not Equals", "&lt;", "&gt;", "&lt;=", "&gt;=", "Between", "Is Empty", "Is Not Empty"],
        all: ["=", "Not Equals", "Contains", "Does not contain", "&lt;", "&gt;", "&lt;=", "&gt;=", "Is Empty", "Is Not Empty"]
    };

    var specialOperatorForFilter = {
        tags: 1,
        keywords: 2,
        pages: 3
    };

    var dataTableFilterRecord = [];

    self_ref.keyword_manager_with_tag = 0;
    self_ref.page_manager_with_tag = 0;
    self_ref.tag_manager_with_tag = 0;

    self_ref.keyword_manager_without_tag = 0;
    self_ref.tag_manager_without_tag = 0;
    self_ref.page_manager_without_tag = 0;
    self_ref.last_filter_applied_column_index = [];

    self_ref.enable_kd = 0;
    self_ref.kd_modal_url = '';

    self_ref.manager_url = '';

    var setLoadedManagersWithoutTag = function (keyword_manager, page_manager, tag_manager) {
        if (keyword_manager > -1) {
            self_ref.keyword_manager_without_tag = 1;
        }

        if (page_manager > -1) {
            self_ref.page_manager_without_tag = 1;
        }

        if (tag_manager > -1) {
            self_ref.tag_manager_without_tag = 1;
        }
    };

    var setLoadedManagersWithTag = function (keyword_manager, page_manager, tag_manager) {
        if (keyword_manager > -1) {
            self_ref.keyword_manager_with_tag = 1;
        }

        if (page_manager > -1) {
            self_ref.page_manager_with_tag = 1;
        }

        if (tag_manager > -1) {
            self_ref.tag_manager_with_tag = 1;
        }
    };

    var enableManagers = function (keyword_manager, page_manager, tag_manager, manager_url) {
        if (keyword_manager > -1) {
            self_ref.keyword_manager_with_tag = 1;
        }

        if (page_manager > -1) {
            self_ref.page_manager_with_tag = 1;
        }

        if (tag_manager > -1) {
            self_ref.tag_manager_with_tag = 1;
        }

        self_ref.manager_url = manager_url;
    };

    var enableKeywordDifficulty = function (enable_kd, kd_modal_url) {
        if(enable_kd > -1){
            self_ref.enable_kd = enable_kd;
        }
        self_ref.kd_modal_url = kd_modal_url;
    };

    var loadFilterKeywordAttribute = function (callback) {
        var ajax_url;
        if(typeof MondovoManagerUrls == 'undefined')
            ajax_url = MVMondovo.get_keyword_attributes_url;
        else
            ajax_url = MondovoManagerUrls.get_keyword_attributes_url;

        var input = {
            url: ajax_url,
            data: {},
            success: function (response) {
                this.mySuccess();
                $('body').append(response.text);

                callback();
            },
            type: 'GET',
            ajax_target_element: document.getElementById('page-content'),
            overwrite_container: false
        };

        Common.Ajax(input);
    };

    var loadSourceManagers = function (keyword_source_manager, page_source_manager, tag_source_manager, callback) {
        var ajax_url;
        if(typeof MondovoManagerUrls == 'undefined')
            ajax_url = MVMondovo.get_source_manager_url;
        else
            ajax_url = MondovoManagerUrls.get_source_manager_url;

        var input = {
            url: ajax_url,
            data: {
                keyword_source_manager: keyword_source_manager,
                page_source_manager: page_source_manager,
                tag_source_manager: tag_source_manager
            },
            success: function (response) {
                this.mySuccess();
                $('body').append(response.text);
                callback();
            },
            type: 'GET',
            ajax_target_element: document.getElementById('page-content'),
            overwrite_container: false
        };

        Common.Ajax(input);
    };

    var loadSourceManagersForFilters = function (keyword_source_manager, page_source_manager, tag_source_manager, callback, loader_container) {
        loader_container = (typeof loader_container == 'undefined') ? document.getElementById('page-content') : document.getElementById(loader_container);

        var ajax_url;
        if(typeof MondovoManagerUrls == 'undefined')
            ajax_url = MVMondovo.get_filter_source_manager_url;
        else
            ajax_url = MondovoManagerUrls.get_filter_source_manager_url;

        var input = {
            url: ajax_url,
            data: {
                keyword_source_manager: keyword_source_manager,
                page_source_manager: page_source_manager,
                tag_source_manager: tag_source_manager
            },
            success: function (response) {
                this.mySuccess();
                $('body').append(response.text);
                callback();
            },
            type: 'GET',
            ajax_target_element: loader_container,
            overwrite_container: false
        };

        Common.Ajax(input);
    };

    var ajaxCallManagers = function (callback) {
        if (self_ref.keyword_manager_with_tag < 1 && self_ref.page_manager_with_tag < 1 && self_ref.tag_manager_with_tag < 1) {
            return false;
        }

        var input = {
            url: self_ref.manager_url,
            data: {
                keyword_manager: self_ref.keyword_manager_with_tag,
                page_manager: self_ref.page_manager_with_tag,
                tag_manager: self_ref.tag_manager_with_tag
            },
            success: function (response) {
                self_ref.tag_manager_with_tag = self_ref.page_manager_with_tag = self_ref.keyword_manager_with_tag = -1;
                this.mySuccess();
                $('body').append(response.text);

                if (typeof callback != 'undefined')
                    callback();
            },
            type: 'GET',
            ajax_target_element: document.getElementById('ajax_overlay_for_managers'),
            overwrite_container: false
        };

        Common.Ajax(input);
    };

    var getKeywordDifficultyModal = function () {
        if (self_ref.enable_kd < 1 || $('#bulk_fetch_keyword_difficulty').length > 0) {
            return false;
        }

        var input = {
            url: self_ref.kd_modal_url,
            success: function (response) {
                this.mySuccess();
                $('body').append(response);
            },
            type: 'GET',
            ajax_target_element: document.getElementById('ajax_overlay_for_managers'),
            overwrite_container: false
        };

        Common.Ajax(input);
    };

    var drawIndividualColumnFilter = function (tableId) {

        if (typeof tableId === "undefined") {
            var attribute_to_traverse = "table";
        } else {
            var attribute_to_traverse = "#" + tableId;
        }


        $(attribute_to_traverse + " thead tr th").each(function () {
            var table_id = $(this).closest("table").attr("id");
            var filter_added_signal = $(this).data('filter_added');

            if (typeof filter_added_signal != 'undefined') {
                return true;
            }

            $(this).data('filter_added', true);
            if (checkFilterSignal(this)) {

                var column_index = checkColIndex(this);
                if (column_index < 0) {
                    return;
                }
                $(this).prepend(MvDataTableFilterDesign.ColumnFilter(table_id, column_index));
                var filter_type = checkFilterType(this);

                addFilterType(this, table_id, filter_type, column_index);
            }

        });
    };

    var addFilterType = function (self, tableId, filter_type, column_index) {

        try {

            var domObj = $(self).find("." + css_class.column_filter_condition).children();

            switch (filter_type) {

                case 'text':
                    domObj.eq(0).html(MvDataTableFilterDesign.DropDown(tableId + "Operator" + column_index, DataTableFilterSettings.text, tableId, column_index));
                    domObj.eq(1).html(MvDataTableFilterDesign.TextField(tableId + "FilterText" + column_index));
                    $("body").append(MvDataTableFilterDesign.ContainsMultipleModal(tableId, column_index));
                    $("body").append(MvDataTableFilterDesign.DoesNotContainsMultipleModal(tableId, column_index));
                    break;

                case 'number':
                    domObj.eq(0).html(MvDataTableFilterDesign.DropDown(tableId + "Operator" + column_index, DataTableFilterSettings.number));
                    domObj.eq(1).html(MvDataTableFilterDesign.TextFieldForNumber(tableId + "FilterNumber" + column_index, tableId + "Operator" + column_index, tableId, column_index));
                    break;

                case 'select':
                    var select_id = checkSelectId(self);
                    if (!select_id) {
                        return false;
                    }
                    domObj.eq(0).html(MvDataTableFilterDesign.DropDown(tableId + "Operator" + column_index, DataTableFilterSettings.select));
                    domObj.eq(1).html(MvDataTableFilterDesign.SecondDropDown(tableId + "FilterSelect" + column_index, select_id));
                    break;
                case 'date':
                    domObj.eq(0).html(MvDataTableFilterDesign.DropDown(tableId + "Operator" + column_index, DataTableFilterSettings.all));
                    var refId = tableId + "FilterDateTime" + column_index;
                    domObj.eq(1).html(MvDataTableFilterDesign.TextField(refId));
                    $('#' + refId).datepicker({autoclose: true, container: domObj.eq(1)});
                    break;
                default:
                    domObj.eq(0).html(MvDataTableFilterDesign.DropDown(tableId + "Operator" + column_index, DataTableFilterSettings.all));
                    domObj.eq(1).html(MvDataTableFilterDesign.TextField(tableId + "FilterText" + column_index));
            }
        }catch (e) {
            console.log(self, tableId, filter_type, column_index, e);
        }
    };

    var checkFilterSignal = function (self) {
        var filter = $(self).attr("data-filter");
        if (typeof filter != 'undefined' && filter == "on") {
            return true;
        }
        return false;
    };

    var checkColIndex = function (self) {
        var column_index = $(self).attr("data-col");
        if (typeof column_index != 'undefined') {
            return column_index;
        }
        return -1;
    };

    var checkFilterType = function (self) {
        var filter_type = $(self).attr("data-filter-type");
        if (typeof filter_type != 'undefined') {

            return filter_type;
        }
        return '';
    };

    var checkSelectId = function (self) {
        var select_id = $(self).attr("data-select-id");
        if (typeof select_id != 'undefined') {

            return select_id;
        }
        return false;
    };

    var checkTitle = function (self) {
        var title = $(self).attr("data-custom-title");
        if (typeof title != 'undefined') {

            return title;
        }
        return false;
    };

    var checkConcat = function (self) {
        var concat_cols = $(self).attr("data-concat-cols");
        if (typeof concat_cols != 'undefined') {
            var concatArr = concat_cols.split(',');
            return concatArr;
        }
        return [];
    };

    var checkConcatSeparator = function (self) {
        var concat_seperator = $(self).attr("data-concat-separator");
        if (typeof concat_seperator != 'undefined') {
            return concat_seperator;
        }
        return '-|-'; // By Default Seperator
    };

    var stopTableSorting = function (e) {
        if (!e) var e = window.event
        e.cancelBubble = true;
        if (e.stopPropagation) e.stopPropagation();
    };

    var processPreFilter = function (self) {
        debug.log("Inside process prefilter");
        debug.log(self);

        var table_id = $(self).attr("table-id");
        var callback = $(self).attr("callback");
        var overwrite_existing_filter = false;
        var overwrite_existing_filter_without_asking = false;
        var overwrite_existing_filter_temp = $(self).attr("overwrite-existing-filter");
        if (overwrite_existing_filter_temp == "true") {
            overwrite_existing_filter = true;
        }
        if (overwrite_existing_filter_temp == "yes") {
            overwrite_existing_filter_without_asking = true;
        }

        var pre_filter_condition = $(self).attr("pre-filter-condition");
        var data_filter_id = $(self).attr("data-filter-id");
        var filter_conditions = jQuery.parseJSON(pre_filter_condition);
        debug.log("Going to go to draw the filter");
        datatableColumnPreFilterNow(table_id, overwrite_existing_filter, overwrite_existing_filter_without_asking, filter_conditions, data_filter_id);

        if(typeof callback != 'undefined')
        {
            MvDataTableFilterDesign.clearAllFilterCallback = function(){ window[callback](self) };
        }
    };

    var filterFromManager = function (self) {

        var data_type = $(self).attr('data-type');
        var data_manager_type = $(self).attr('data-manager-type');
        var data_table_id = $(self).attr('data-table-id');
        var column_index = $(self).attr('data-column-index');
        var loader_container = $(self).attr('loader-container');

        if (data_type == 'keywords') {
            if ($(".filter-keyword-from-tag-manager-container").length > 0) {
                addKeywordSourceManagersForFilters(data_table_id, data_type, column_index, data_manager_type);
                return true;
            }

            loadSourceManagersForFilters(1, -1, -1, function () {
                addKeywordSourceManagersForFilters(data_table_id, data_type, column_index, data_manager_type);
            }, loader_container);
        }

        if (data_type == 'pages') {
            if ($(".filter-page-from-tag-manager-container").length > 0) {
                addPageSourceManagersForFilters(data_table_id, data_type, column_index, data_manager_type);
                return true;
            }

            loadSourceManagersForFilters(-1, 1, -1, function () {
                addPageSourceManagersForFilters(data_table_id, data_type, column_index, data_manager_type);
            }, loader_container);
        }

        if (data_type == 'tags') {
            if ($(".filter-tag-from-keyword-manager-container").length > 0) {
                addTagSourceManagersForFilters(data_table_id, data_type, column_index, data_manager_type);
                return true;
            }

            loadSourceManagersForFilters(-1, -1, 1, function () {
                addTagSourceManagersForFilters(data_table_id, data_type, column_index, data_manager_type);
            }, loader_container);
        }
    };

    var filterKeywordAttribute = function (self) {
        var data_table_id = $(self).attr('data-table-id');
        var column_index = $(self).attr('data-column-index');
        var column_name = $(self).attr('data-column-name');

        if ($("#keyword_attributes_form").length > 0) {
            doFilterKeywordAttribute(data_table_id, column_index, column_name);
            return true;
        }

        loadFilterKeywordAttribute(function () {
            doFilterKeywordAttribute(data_table_id, column_index, column_name);
        });
    };

    var doFilterKeywordAttribute = function (data_table_id, column_index, column_name) {
        $("#keyword-attributes-modal").modal('show');
        var form_ref = $("#keyword_attributes_form");
        form_ref.data('data-table-id', data_table_id);
        form_ref.data('data-column-index', column_index);
        form_ref.data('data-column-name', column_name);
        form_ref.data('data-ajax-before-validation', function () {
            var final_status_signal = 0;
            var status_signal = false;
            var form_ref = $("#keyword_attributes_form");
            var table_id = form_ref.data('data-table-id');
            var column_name = form_ref.data('data-column-name');
            var column_index = form_ref.data('data-column-index');
            var keywords_attr_ref = $("#keyword_attributes_type");
            var keywords_attr = keywords_attr_ref.select2("data");
            var keywords_attr_length = keywords_attr.length;
            for (var index = 0; index < keywords_attr_length; index++) {
                status_signal = datatableKeywordsAttributes(table_id, column_name, column_index, keywords_attr[index].text, keywords_attr[index].id, false);
                if (status_signal) {
                    final_status_signal = 1;
                }
            }

            if (final_status_signal) {
                $("#keyword_attributes_type").select2("val", "");
                $("#keyword-attributes-modal").modal('hide');
                redrawTable(table_id);
            }
            return false;
        });
    };

    var addFromManager = function (self) {
        var data_type = $(self).attr('data-type');
        var data_manager_type = $(self).attr('data-manager-type');
        var data_source_selector = $(self).attr('data-source-selector');
        var data_max_attr_allowed = $(self).attr('data-max-attr-allowed');

        var max_allowed = calculateMaxSourceAllowed(data_source_selector, data_max_attr_allowed);
        if (max_allowed == 0) {
            bootbox.alert(MvDataTableFilterDesign.maxSourceAddedMsg.replace('<max>', data_max_attr_allowed).replace('<type>', data_type));
            return false;
        }
        if (data_type == 'keywords') {
            if ($(".keyword-from-tag-manager-container").length > 0) {
                addKeywordSourceManagers(max_allowed, data_source_selector, data_manager_type);
                return true;
            }

            loadSourceManagers(1, -1, -1, function () {
                addKeywordSourceManagers(max_allowed, data_source_selector, data_manager_type);
            });
        }

        if (data_type == 'pages') {
            if ($(".page-from-tag-manager-container").length > 0) {
                addPageSourceManagers(max_allowed, data_source_selector, data_manager_type);
                return true;
            }

            loadSourceManagers(-1, 1, -1, function () {
                addPageSourceManagers(max_allowed, data_source_selector, data_manager_type);
            });
        }

    };

    var calculateMaxSourceAllowed = function (selector, max_allowed) {
        var results = $("#" + selector).val();
        var elements = results.split('\n');
        var element_length = elements.length;
        var actual_index = 0;
        var index = 0;
        while (index < element_length && actual_index <= max_allowed) {
            if ($.trim(elements[index]) != '') {
                actual_index++;
            }
            index++;
        }

        if (actual_index >= max_allowed) {
            return 0;
        }

        return (max_allowed - actual_index);
    };

    var showHideManagerType = function (data_type, data_manager_type) {
        if (typeof data_manager_type != 'undefined') {
            $(".add-from-source").hide();
            if (data_manager_type.indexOf('tag') >= 0) {
                $('.' + data_type + "-from-tag-manager-container").show();
            }

            if (data_manager_type.indexOf('page') >= 0) {
                $('.' + data_type + "-from-page-manager-container").show();
            }

            if (data_manager_type.indexOf('keyword') >= 0) {
                $('.' + data_type + "-from-keyword-manager-container").show();
            }
        }
        else {
            $(".add-from-source").show();
        }
    };

    var addKeywordSourceManagers = function (max_allowed, source_selector, data_manager_type) {
        $("#add-from-keyword-source-modal").modal('show');

        if(isNaN(max_allowed)){
            $('#limit_for_the_source_manager').hide();
            max_allowed = 10000;
        }
        if (max_allowed > -1) {
            $("#source_attr_km_container").show();
            $("#source_attr_km").html(max_allowed);
        } else {
            $("#source_attr_km_container").hide();
        }

        $("#add-from-keyword-source-modal").attr("data-source-selector", source_selector);
        showHideManagerType('keyword', data_manager_type);
        $("#add-from-keyword-source-modal").attr("data-max-allowed", max_allowed);
    };

    var addPageSourceManagers = function (max_allowed, source_selector, data_manager_type) {
        $("#add-from-page-source-modal").modal('show');

        if (max_allowed > -1) {
            $("#source_attr_pm_container").show();
            $("#source_attr_pm").html(max_allowed);
        } else {
            $("#source_attr_pm_container").hide();
        }

        $("#add-from-page-source-modal").attr("data-source-selector", source_selector);
        $("#add-from-page-source-modal").attr("data-manager-type", data_manager_type);
        showHideManagerType('page', data_manager_type);
        $("#add-from-page-source-modal").attr("data-max-allowed", max_allowed);
    };

    var sourceManagerForFilter = function (data_table_id, data_type, column_index, form_id, modal_id, data_manager_type) {
        $("#" + modal_id).modal('show');
        if (typeof data_manager_type != 'undefined') {
            $("." + css_class.filter_modal_class).hide();
            if (data_manager_type.indexOf('tag') >= 0) {
                $(".filter-" + data_type.slice(0, -1) + "-from-tag-manager-container").show();
            }

            if (data_manager_type.indexOf('page') >= 0) {
                $(".filter-" + data_type.slice(0, -1) + "-from-page-manager-container").show();
            }

            if (data_manager_type.indexOf('keyword') >= 0) {
                $(".filter-" + data_type.slice(0, -1) + "-from-keyword-manager-container").show();
            }
        }
        else {
            $("." + css_class.filter_modal_class).show();
        }

        var form_ref = $("#" + form_id);
        form_ref.data('data-modal-id', modal_id);
        form_ref.data('data-table-id', data_table_id);
        form_ref.data('data-type', data_type);
        form_ref.data('data-column-index', column_index);
        form_ref.data('data-ajax-before-validation', function (form_ref) {
            var tags_ref = form_ref.find(".tag-manager-without-tag");
            var keywords_ref = form_ref.find(".keyword-manager-without-tag");
            var pages_ref = form_ref.find(".page-manager-without-tag");
            var data_manager_type = form_ref.data('data-manager-type');
            var exclude_selected_ref = form_ref.find('.filter-exclude-selected');
            var tags = [];
            var keywords = [];
            var pages = [];
            var exclude_selected = 'no';
            var type_num = 0;

            if (tags_ref.length > 0) {
                tags = tags_ref.select2("data");
            }

            if (keywords_ref.length > 0) {
                keywords = keywords_ref.select2("data");
            }

            if (pages_ref.length > 0) {
                pages = pages_ref.select2("data");
            }

            if (exclude_selected_ref.length > 0 && $("input[name='exclude_selected_filter_type']:checked"). val() == 'no') {
                exclude_selected = 'yes';
                type_num = 4;
            }

            var tags_selected_length = tags.length;
            var keywords_selected_length = keywords.length;
            var pages_selected_length = pages.length;

            var index = 0;
            var table_id = form_ref.data('data-table-id');
            var data_type = form_ref.data('data-type');
            var column_index = form_ref.data('data-column-index');
            var status = false;
            var final_status_signal = 0;
            for (index = 0; index < tags_selected_length; index++) {
                status = datatableManagerFilter(table_id, data_type, column_index, 1 + type_num, tags[index].text, tags[index].id, exclude_selected, false);
                if (status == true) {
                    final_status_signal = 1;
                }
            }

            for (index = 0; index < keywords_selected_length; index++) {
                status = datatableManagerFilter(table_id, data_type, column_index, 2 + type_num, keywords[index].text, keywords[index].id, exclude_selected, false);
                if (status == true) {
                    final_status_signal = 1;
                }
            }

            for (index = 0; index < pages_selected_length; index++) {
                status = datatableManagerFilter(table_id, data_type, column_index, 3 + type_num, pages[index].text, pages[index].id, exclude_selected, false);
                if (status == true) {
                    final_status_signal = 1;
                }
            }

            if (final_status_signal) {
                redrawTable(table_id);
            }

            if (tags_ref.length > 0) {
                tags_ref.select2('val', '');
            }
            if (keywords_ref.length > 0) {
                keywords_ref.select2('val', '');
            }

            if (pages_ref.length > 0) {
                pages_ref.select2('val', '');
            }

            $("#" + form_ref.data('data-modal-id')).modal('hide');

            return false;
        });
    };

    var addPageSourceManagersForFilters = function (data_table_id, data_type, column_index, data_manager_type) {
        sourceManagerForFilter(data_table_id, data_type, column_index, 'filter_page_ids_form', 'filter-from-page-source-modal', data_manager_type);
    };

    var addKeywordSourceManagersForFilters = function (data_table_id, data_type, column_index, data_manager_type) {
        sourceManagerForFilter(data_table_id, data_type, column_index, 'filter_keyword_ids_form', 'filter-from-keyword-source-modal', data_manager_type);
    };

    var addTagSourceManagersForFilters = function (data_table_id, data_type, column_index, data_manager_type) {
        sourceManagerForFilter(data_table_id, data_type, column_index, 'filter_tag_ids_form', 'filter-from-tag-source-modal', data_manager_type);
    };

    var addToTagManager = function (self) {
        var modal_ref = $('#' + css_class.add_to_tag_manager_modal_id);
        if (modal_ref.length == 0) {
            self_ref.tag_manager_with_tag = 1;
            ajaxCallManagers(function () {
                addToTagManager(self)
            });
            return;
        }

        if(manger_is_demo_mode === 'yes') //This value is set in appropriate blade file
        {
            bootbox.alert(MvDataTableFilterDesign.managerGuestUserErrorMessage);
            return;
        }

        addToCommonProcess(self, css_class.add_to_tag_manager_modal_id, css_class.selected_attr_tm_id, 'add_to_tag_manager');
    };

    var copyToClipboard = function (self) {
        var table_id = $(self).attr('data-table-id');
        var column_index = $(self).attr('data-column-index');
        var callback_fn = $(self).attr('callback');

        $(self).find('div').find('.copy-loading').show();
        MvDataTableCheckbox.getValuesToCopy(table_id, column_index, function (records) {
            var values_to_be_copied = '';
            for (var i = 0; i < records.length; i++) {
                values_to_be_copied += records[i] + '\n';
            }
            var temp_textarea = $('<textarea>');
            var append_to = $(self).parent();
            append_to.append(temp_textarea);
            temp_textarea.html(values_to_be_copied);
            temp_textarea.focusin();
            temp_textarea.select();
            var copy_status = document.execCommand('copy', true);
            temp_textarea.remove();
            if (copy_status == false) {
                var row_count = records.length;
                var col_heading = $($("#" + table_id).DataTable().columns(column_index).header()[0]).find('.table-header-column-title').html();
                copyTextManual(values_to_be_copied, row_count, col_heading, callback_fn);
            }
            else
            {
                if(typeof callback_fn != 'undefined' && values_to_be_copied != '')
                {
                    var text_array = values_to_be_copied.split('\n').join('\\n');
                    eval('' + callback_fn + '("' + text_array + '")');
                }
                Common.growlMessage('Copied to clipboard', 'success', 5000);
            }
            MvDataTableCheckbox.clearOrSelectAllRecords(table_id, false);
            $(self).find('div').find('.copy-loading').hide();
            return true;
        });
        return true;
    };

    var copyToFilter = function (self) {
        var table_id = $(self).attr('data-table-id');
        var column_index = $(self).attr('data-column-index');
        var callback_fn = $(self).attr('callback');

        $(self).find('div').find('.copy-loading').show();
        MvDataTableCheckbox.getValuesToCopy(table_id, column_index, function (records) {
            var values_to_be_copied = '';
            for (var i = 0; i < records.length; i++) {
                values_to_be_copied += records[i] + '\n';
            }

            if(typeof callback_fn != 'undefined' && values_to_be_copied != '')
            {
                var text_array = values_to_be_copied.split('\n').join('\\n');
                eval('' + callback_fn + '("' + text_array + '")');
            }
            Common.growlMessage('Added to Filter', 'success', 5000);

            MvDataTableCheckbox.clearOrSelectAllRecords(table_id, false);
            $(self).find('div').find('.copy-loading').hide();
            return true;
        });
        return true;
    };

    var bulkKDProcess = function (self) {
        var table_id = $(self).attr('data-table-id');
        var columnNameOrIndex = [];
        columnNameOrIndex[0] = $(self).attr('keyword-id-column-index');
        columnNameOrIndex[1] = $(self).attr('location-id-column-index');
        columnNameOrIndex[2] = $(self).attr('difficulty-column-index');
        MvDataTableCheckbox.getColumnValues(table_id, columnNameOrIndex, function (records) {
            var difficulty_status = KeywordDifficulty.bulkKeywordDifficulty(records, columnNameOrIndex);
            if (difficulty_status === 'NO_KD_FOR_GLOBAL_LOCATION') {
                bootbox.alert('Sorry, Keyword Difficulty is not available for <code>Global</code> Location');
                $(self).attr('disabled', 'true');
                return 'NO_KD_FOR_GLOBAL_LOCATION';
            }
            var record_count = records.length;
            var modal_ref = $('#bulk_fetch_keyword_difficulty');
            modal_ref.modal('show');
            modal_ref.find('#' + css_class.selected_attr_kd_id).html(record_count);
        });
        return true;
    };

    var copyTextManual = function(text, row_count, col_heading, callback_fn)
    {
        if(!col_heading.endsWith("s"))
            col_heading += 's';
        var temp_textarea = $('<textarea style="border: 1px solid white; height: 0px; width: 0px; resize: none;">');
        temp_textarea.html(text);
        var extra_html = 'Click "Confirm" to copy all ' + row_count + ' \'' + col_heading.toUpperCase() + '\'';
        extra_html += $('<div>').append(temp_textarea).html();

        var copy_manually_boot_box =
            bootbox.dialog({
                title: " ",
                message: '<div class="col-md-12 copy-manually-container" style="text-align: center;">'+ extra_html +' </div>',
                buttons: {
                    success: {
                        label: '<i class="fa fa-check"></i> Confirm',
                        className: 'btn mon-btn-blue',
                        callback: function (result) {

                            var temp_textarea = $('.copy-manually-container>textarea');
                            temp_textarea.focusin();
                            temp_textarea.select();
                            var values_to_be_copied = temp_textarea.val();
                            var copy_status = document.execCommand('copy', true);

                            if (copy_status == false) {
                                Common.growlMessage('Unable to copy Automatically', 'danger', 2000);
                            }
                            else
                            {
                                if(typeof callback_fn != 'undefined' && values_to_be_copied != '')
                                {
                                    var text_array = values_to_be_copied.split('\n').join('\\n');
                                    eval('' + callback_fn + '("' + text_array + '")');
                                }
                                Common.growlMessage('Copied to clipboard', 'success', 5000);
                            }
                        }
                    }
                }

            });

        return ;
    };

    var addToKeywordManager = function (self) {

        var modal_ref = $('#' + css_class.add_to_keyword_manager_modal_id);
        if (modal_ref.length == 0) {
            self_ref.keyword_manager_with_tag = 1;
            ajaxCallManagers(function () {
                addToKeywordManager(self)
            });
            return;
        }

        if(manger_is_demo_mode === 'yes') //This value is set in appropriate blade file
        {
            bootbox.alert(MvDataTableFilterDesign.managerGuestUserErrorMessage);
            return;
        }

        addToCommonProcess(self, css_class.add_to_keyword_manager_modal_id, css_class.selected_attr_km_id, 'add_to_keyword_manager');
    };

    var addToPageManager = function (self) {

        var modal_ref = $('#' + css_class.add_to_keyword_manager_modal_id);
        if (modal_ref.length == 0) {
            self_ref.page_manager_with_tag = 1;
            ajaxCallManagers(function () {
                addToKeywordManager(self)
            });
            return;
        }

        if(manger_is_demo_mode === 'yes') //This value is set in appropriate blade file
        {
            bootbox.alert(MvDataTableFilterDesign.managerGuestUserErrorMessage);
            return;
        }

        addToCommonProcess(self, css_class.add_to_page_manager_modal_id, css_class.selected_attr_pm_id, 'add_to_page_manager');
    };

    var addToCommonProcess = function (self, modal_id, selected_qty_id, form_id) {
        var table_id = $(self).attr('data-table-id');
        var column_index = $(self).attr('data-column-index');
        var data_column_type = $(self).attr('data-column-type');
        MvDataTableCheckbox.getTableColumnValues(table_id, column_index, function (records, record_count) {
            var modal_ref = $('#' + modal_id);
            modal_ref.find('#' + selected_qty_id).html(record_count);
            modal_ref.modal('show');
            $("#" + form_id).data("table-id", table_id);
            $("#" + form_id).data("modal-id", modal_id);
            var records_details = [];
            var records_details_str = '';
            for (var i = 0; i < records.length; i++) {
                //records_details.push({name: data_column_type + '[]', value: records[i]});
                records_details_str += records[i] + ',';
            }
            var charlist = ',';
            charlist = !charlist ? ' \\s\u00A0' : (charlist + '')
                .replace(/([[\]().?/*{}+$^:])/g, '\\$1');
            var re = new RegExp('[' + charlist + ']+$', 'g');
            records_details_str = (records_details_str + '').replace(re, '');

            records_details.push({name: data_column_type, value: records_details_str});

            $("#" + form_id).data('post_data', records_details);
            $("#" + form_id).data("data-ajax-validation-on-success", function (response) {
                var select_ref = $("#" + form_id).find('.select-manager');
                var modal_ref = $('#' + $("#" + form_id).data("modal-id"));
                MvDataTableCheckbox.clearOrSelectAllRecords($("#" + form_id).data("table-id"), false);
                modal_ref.modal('hide');
                select_ref.select2("val", "");
                /*for (var i = 0; i < response.length; i++) {
                    Common.growlMessage(response[i].message, 'success');
                }*/
                if(typeof response['newly_added_tags'] != 'undefined'){
                    $.each(response['newly_added_tags'], function( key, value ) {
                        $('#tag_campaign_ids').append('<option value=' + value + '>' + key + '</option>');
                        $("#tag_campaign_ids").select2('data', {id: value, text: key});
                    });
                }
                Common.growlMessage(response['message'], 'success');
            });
        });
    };

    var putMaxSourceAllowed = function (selector, max_allowed, records) {
        if (max_allowed == '' || typeof max_allowed == 'undefined' || max_allowed == 'NaN')
            max_allowed = 10000; //Set max_allowed if it's not given

        var results = $.trim($("#" + selector).val());
        var new_elements = [];
        var index = 0;
        var record_length = records.length;
        while (max_allowed > index && record_length > index) {
            new_elements.push(records[index]);
            index++;
        }

        if (results != '') {
            results += '\n';
        }

        var new_value = new_elements.join('\n');

        $("#" + selector).val(results + new_value);
        $("#" + selector).change(); //Firing the change event for this textarea

    };

    //Was purposely refactored on 23rd June, 2016 by Sameer because we needed to call only this subset of functions on an Ajax re-initilization of the table. Calling above functions on re-initialization would be problematic as they would be duplicating the events.
    function registerSubEvents() {
        // Register Prefilter
        $("." + css_class.pre_filter_link).off('click');
        $("." + css_class.pre_filter_link).click(function () {
            processPreFilter(this);
        });

        // Reset Prefilter
        $("." + css_class.reset_all_filter).off('click');
        $("." + css_class.reset_all_filter).click(function () {
            var table_id = $(this).attr('table-id');
            datatableFilterClear(table_id);
        });

        // Register PrefilterToolbar
        $("." + css_class.pre_filter_link_toolbar).off('click');
        $("." + css_class.pre_filter_link_toolbar).click(function () {
            processPreFilter(this);
            /* var parent_li = $(this).parents("li");
             parent_li.addClass("active");
             parent_li.find("." + css_class.datatable_toolbar_selected_text_container).removeClass("hidden");
             parent_li.find("." + css_class.datatable_toolbar_selected_text).html($(this).children('a').html());*/
        });

        // Register Special PrefilterToolbar
        $("." + css_class.special_pre_filter_link_toolbar).off('click');
        $("." + css_class.special_pre_filter_link_toolbar).click(function () {

            var table_id = $(this).attr('table-id');
            var overwrite_existing_filter = $(this).attr('overwrite-existing-filter');
            var words = $(this).attr('words');
            var column_number = $(this).attr('data-col');
            var filter_type = $(this).attr('filter-type');
            var filter_sub_type = $(this).attr('filter-sub-type');
            var words_json = {};
            if (typeof words === 'undefined' || words === '')
            {
                return;
            }

            words_json = JSON.parse(words);
            return dataTableInLineSpecialFilter(table_id, column_number, overwrite_existing_filter, words_json, filter_type, filter_sub_type, true);
        });

        //Keyword Group Special Filters: Contains Multiple/Doesn not Contain Multiple (With Contains Filter of parent Keyword for Child Keyword)
        $("." + css_class.keyword_group_special_pre_filter_link_toolbar).off('click');
        $("." + css_class.keyword_group_special_pre_filter_link_toolbar).click(function () {

            var table_id = $(this).attr('table-id');
            var overwrite_existing_filter = $(this).attr('overwrite-existing-filter');
            var words = $(this).attr('words');
            var column_number = $(this).attr('data-col');
            var filter_type = $(this).attr('filter-type');
            var filter_sub_type = $(this).attr('filter-sub-type');
            var words_json = {};
            if (typeof words === 'undefined' || words === '')
            {
                return;
            }

            words_json = JSON.parse(words);
            return dataTableInLineKeywordGroupSpecialFilter(this, table_id, column_number, overwrite_existing_filter, words_json, filter_type, filter_sub_type, true);
        });

        //Keyword Group Special Filters: Contains Multiple/Doesn not Contain Multiple (With Contains Filter of parent Keyword for Child Keyword)
        $("." + css_class.keyword_group_filter_dropper).unbind('click.keyword_group_generate');
        $("." + css_class.keyword_group_filter_dropper).bind('click.keyword_group_generate', function () {

            var list_exist = $(this).find('div.keyword-group-wrapper').length > 0;

            if(list_exist && !MvDataTableFilterDesign.shouldRefreshKgData)
            {
                return;
            }

            var table_id = $(this).attr('table-id');
            var column_name = $(this).attr('col-name');
            var column_index = $(this).attr('col-index');

            drawKeywordGroupFilterData(table_id, column_name, column_index)
        });

        //  Filter dropdown click outside close
        $("." + css_class.column_filter_container).off('click');
        $("." + css_class.column_filter_container).click(function (event) {
            stopTableSorting(event);
        });

        $("." + css_class.column_filter_btn).off('click');
        $("." + css_class.column_filter_btn).click(function (e) {
            $(this).closest("table").find("." + css_class.column_filter_container).removeClass("open");
            var obj_ref = $(this).parent("." + css_class.column_filter_container);
            obj_ref.addClass("open");
            obj_ref.find("." + css_class.column_filter_condition + " li:eq(1)").show();
            obj_ref.find("." + css_class.column_filter_condition + " li:eq(0) operator_select").val('0');
            stopTableSorting(e);
        });

        $("." + css_class.filter_now).off("click");
        $("." + css_class.filter_now).click(function () {
            $(this).parents("." + css_class.column_filter_container).removeClass("open");
            var tableId = $(this).attr("tableId");
            datatableColumnFilterNow(this, tableId);
        });

        $("." + css_class.filter_now_from_contains_modal).off("click");
        $("." + css_class.filter_now_from_contains_modal).click(function () {
            var tableId = $(this).attr("table-id");
            var columnIndex = $(this).attr("column-index");
            var filter_now_button_id = tableId + '_filter_now_' + columnIndex;
            $('#' + filter_now_button_id).click();
        });

        $("." + css_class.contains_multiple_close).off("click");
        $("." + css_class.contains_multiple_close).click(function () {
            var tableId = $(this).attr("table-id");
            var columnIndex = $(this).attr("column-index");
            var operator_select = tableId + 'Operator' + columnIndex;
            $('#' + operator_select).prop('selectedIndex',0);
        });

        $(document).off("click", "." + css_class.open_contains_multiple_modal);
        $(document).on('click', "." + css_class.open_contains_multiple_modal, function (event) {
            var table_id = $(this).attr('table-id');
            var column_index = $(this).attr('column-index');
            var contains_modal_id_part = table_id + 'FilterTextArea' + column_index;
            var operator_select_id = table_id + 'Operator' + column_index;

            $("#" + operator_select_id + " option:contains(Contains (multiple))").attr('selected', 'selected');
            $('#contains_multiple_modal_for_' + contains_modal_id_part).modal();
        });

        $(document).off("click", "." + css_class.open_does_not_contain_multiple_modal);
        $(document).on('click', "." + css_class.open_does_not_contain_multiple_modal, function (event) {
            var table_id = $(this).attr('table-id');
            var column_index = $(this).attr('column-index');
            var contains_modal_id_part = table_id + 'FilterTextArea' + column_index;
            var operator_select_id = table_id + 'Operator' + column_index;

            $("#" + operator_select_id + " option:contains(Does not contain (multiple))").attr('selected', 'selected');
            $('#does_not_contain_multiple_modal_for_' + contains_modal_id_part).modal();
        });

        $("." + css_class.close_filter).off('click');
        $("." + css_class.close_filter).click(function () {
            $(this).parents("." + css_class.column_filter_container).removeClass("open");
        });

        $('.' + css_class.operator_select).off('click');
        $('.' + css_class.operator_select).change(function () {
            var option = $(this).find('option:selected').text();
            if (option == 'Is Empty' || option == 'Is Not Empty') {
                $(this).parent().next().hide();
            } else if(option == 'Contains (multiple)' || option == 'Does not contain (multiple)') {

                var contains_modal_id_part = $(this).data('contains-modal-id-part');
                var table_id = $(this).attr('table-id');
                var column_index = $(this).attr('column-index');
                var prent_ul = $(this).parent().parent();
                var filter_button = prent_ul.find($('.filter_now'));
                var filter_button_id = table_id + '_filter_now_' + column_index;
                filter_button.attr('id', filter_button_id);
                //$(this).prop('selectedIndex', 0);

                if(option == 'Contains (multiple)'){
                    $('#contains_multiple_modal_for_' + contains_modal_id_part).modal();
                }else{
                    $('#does_not_contain_multiple_modal_for_' + contains_modal_id_part).modal();
                }

            } else {
                $(this).parent().next().show();
            }
        });

        //Delete all events already assigned to $('.export-datatable')
        $('.export-datatable').off();
        $('.export-datatable').click(function () {
            initiateExport(this);
        });

        $('li.main-bucket-item').unbind('click.keyword_group_main_click_hand');
        $('li.main-bucket-item').bind('click.keyword_group_main_click_hand', function () {

            //var is_filter_force_off = $(this).parent().children('.filter-span').children('label').children('[type="checkbox"]').is(':checked');

            $(this).children('span.keyword-group-branch').children('.keyword-group-caret').toggleClass('open');
            $(this).next('ul.sub-child-bucket').slideToggle(700);

        });

        $('[type="checkbox"].keyword-group').off('change');
        $('[type="checkbox"].keyword-group').change(function () {

            var table_id = $(this).attr('table-id');
            var column_index = $(this).attr('column-index');
            var any_one_filter = $('input#' + table_id + 'FilterTextArea' + column_index + '_radio_any_one');
            var filter_toolbars = $('.bucket-item[pre-filter-condition][table-id="' + table_id + '"]');

            $(any_one_filter).attr('checked', 'checked');

            var filter_on = !$(this).is(':checked');

            if (filter_on)
            {
                $.each(filter_toolbars, function (index, element) {

                    var filter_condition = $(element).attr('pre-filter-condition');
                    var on_click_condition = $(element).attr('onclick');

                    filter_condition = filter_condition.replace('\"Contains (multiple)\"', '\"Does not contain (multiple)\"');
                    filter_condition = filter_condition.replace('\"Contains\"', '\"Does not contain\"');

                    if (typeof on_click_condition !== 'undefined' )
                    {
                        on_click_condition = on_click_condition.replace((table_id + 'FilterTextArea' + column_index), (table_id + 'FilterTextArea' + column_index + '_does_not') );
                    }

                    $(element).attr('pre-filter-condition', filter_condition);
                    $(element).attr('onclick', on_click_condition);
                });

                //Clear Off does_not_contain_filter
                $('#' + table_id + 'FilterTextArea' + column_index + '_does_not').val('');

            }
            else
            {
                $.each(filter_toolbars, function (index, element) {

                    var filter_condition = $(element).attr('pre-filter-condition');
                    var on_click_condition = $(element).attr('onclick');

                    filter_condition = filter_condition.replace('\"Does not contain (multiple)\"', '\"Contains (multiple)\"');
                    filter_condition = filter_condition.replace('\"Does not contain\"', '\"Contains\"');

                    if (typeof on_click_condition != 'undefined' )
                    {
                        on_click_condition = on_click_condition.replace((table_id + 'FilterTextArea' + column_index + '_does_not'), (table_id + 'FilterTextArea' + column_index));
                    }
                    $(element).attr('pre-filter-condition', filter_condition);
                    $(element).attr('onclick', on_click_condition);
                });
            }


        });

        $('.main-bucket').prev('a').off('click');
        $('.main-bucket').prev('a').click(function () {
            $(this).next('ul').toggle(500).toggleClass('open');
        });

    }

    var registerEvents = function () {

        //Register Add to Datatable

        $(document).on('click', '.' + css_class.keyword_attribute_class, function () {
            filterKeywordAttribute(this);
        });

        $(document).on('click', '.' + css_class.add_from_manager_class, function () {
            addFromManager(this);
        });

        $(document).on('click', '.' + css_class.filter_from_manager_class, function () {
            filterFromManager(this);
        });

        $(document).on('click', '.' + css_class.filter_from_manager_in_built_class, function () {
            filterFromManager(this);
        });

        $(document).on('click', '.get-source-data', function () {
            var table_id = $(this).attr('data-table-id');
            MvDataTableCheckbox.redrawTable(table_id);
        });

        $(document).on('click', '.add-data-source', function () {
            var table_id = $(this).attr('data-table-id');
            var self = this;
            MvDataTableCheckbox.getTableColumnValues(table_id, 2, function (records, record_count) {
                var source_selector = $(self).parents('.modal').attr('data-source-selector');
                var max_allowed = $(self).parents('.modal').attr('data-max-allowed');
                $(self).parents('.modal').modal('hide');
                putMaxSourceAllowed(source_selector, max_allowed, records);
                MvDataTableCheckbox.clearOrSelectAllRecords(table_id, false);
            });
        });

        $(document).on('click', "." + css_class.add_to_tag_manager_class, function () {
            addToTagManager(this);
        });

        $(document).on('click', "." + css_class.copy_to_clipboard_class, function () {
            copyToClipboard(this);
        });

        $(document).on('click', "." + css_class.copy_to_filter_class, function () {
            copyToFilter(this);
        });

        $(document).on('click', "." + css_class.bulk_generate_kd_class, function () {
            bulkKDProcess(this);
        });

        $(document).on('click', "." + css_class.add_to_keyword_manager_class, function () {
            addToKeywordManager(this);
        });

        $(document).on('click', "." + css_class.add_to_page_manager_class, function () {
            addToPageManager(this);
        });


        $(document).on('click', "." + css_class.clear_filter, function (event) {

            var obj_ref = $(this).parents("." + css_class.filter_conditions_container);
            var filter_length = obj_ref.children().length;
            var table_id = $(this).attr('table_id');
            if (filter_length == 1) {
                $("#" + table_id + "_filter").hide();
                $('.'+table_id+'.mx_dt_text_area').val('');
            }
            closeDatatableFilterTag(this);
            //Added by lohith for removing text selector filter.
            //Functions available in text-selector-filter.js
            if(typeof TextSelectorFilter.clear.include === 'function'){
                TextSelectorFilter.clear.include();
            }

            if(typeof TextSelectorFilter.clear.exclude === 'function'){
                TextSelectorFilter.clear.exclude();
            }

            if( typeof post_filter_clear_callback[table_id] !== 'undefined')
            {
                post_filter_clear_callback[table_id](this);
            }
        });

        $(document).off("click", "." + css_class.clear_all_filters);
        $(document).on('click', "." + css_class.clear_all_filters, function (event) {
            var self = this;
            bootbox.confirm(MvDataTableFilterDesign.filterConditionMsg, function (result) {
                if (result) {
                    //Added by lohith for removing text selector filter.
                    //Functions available in text-selector-filter.js
                    if(typeof TextSelectorFilter.clear.include === 'function'){
                        TextSelectorFilter.clear.include();
                    }

                    if(typeof TextSelectorFilter.clear.exclude === 'function'){
                        TextSelectorFilter.clear.exclude();
                    }

                    var table_id = $(self).attr('tableId');
                    $('.'+table_id+'.mx_dt_text_area').val('');
                    datatableFilterClear(table_id);

                    if( typeof post_filter_clear_callback[table_id] !== 'undefined')
                    {
                        post_filter_clear_callback[table_id](self);
                    }

                    if(MvDataTableFilterDesign.clearAllFilterCallback)
                        MvDataTableFilterDesign.clearAllFilterCallback();
                }
            });
        });

        //$("." + css_class.custom_table_options).off("click");
        $(document).on('click', "." + css_class.custom_table_options, function (event) {
            $(this).find('.list-group').toggle();
            var ulMaxHeight = $(this).parents(".dataTable-toolbar").siblings(".dataTables_wrapper").height() - 70;
            $(this).children(".md-checkbox-list").css("max-height", ulMaxHeight);
        });

        registerSubEvents();//Was purposely refactored on 23rd June, 2016 by Sameer because we needed to call only this subset of functions on an Ajax re-initilization of the table. Calling above functions on re-initialization would be problematic as they would be duplicating the events.
    };

    var initiateExport = function (self) {
        var table_id = $(self).attr('table-id');
        var ajax_url = $("#" + table_id).DataTable().ajax.url();

        if (ajax_url != null) {
            //$("#" + table_id).DataTable().context[0].aoColumns
            sColumnNames = prepareDataTableColumnNamesArray($("#" + table_id).DataTable(), table_id, $(self).attr('delimiter'));

            //To do: Check with mahesh/sameer to confirm which method is better iframe or form with post method
            //var iframe_download_container = MvDataTableFilterDesign.exportReportMessage + "<iframe style='height:0; width:0; border: 0;' frameBorder='0' src='" + ajax_url + "?export=true&export_column_names=" + encodeURIComponent(sColumnNames) + "&file_type=" + $(self).attr('file-type') + "&export_report_name=" + encodeURIComponent($(self).attr('report-name')) + "&export_report_date=" + encodeURIComponent($(self).attr('report-date')) + "&export_strip_columns=" + $(self).attr('strip-columns') + "'></iframe>";

            var download_form = "<form id='export_form' method='post' action='" + ajax_url + "'>";
            download_form += "<input type='hidden' name='export' value='true' />";
            //download_form += "<input type='hidden' name='export_column_names' value='" + JSON.stringify(sColumnNames) + "' />";
            download_form += "<textarea style='visibility: hidden; position: absolute;' name='export_column_names'>" + JSON.stringify(sColumnNames) + "</textarea>";
            download_form += "<input type='hidden' name='file_type' value='" + $(self).attr('file-type') + "' />";
            download_form += "<input type='hidden' name='export_report_name' value='" + $(self).attr('report-name') + "' />";
            download_form += "<input type='hidden' name='export_report_date' value='" + $(self).attr('report-date') + "' />";
            download_form += "<input type='hidden' name='export_strip_columns' value='" + $(self).attr('strip-columns') + "' />";
            download_form += "<input type='hidden' name='_token' value='" + $(self).attr('token') + "' />";

            if (typeof API_KEY != 'undefined' && typeof MODE != 'undefined') {
                download_form += "<input type='hidden' name='api-key' value='" + API_KEY + "' />";
                download_form += "<input type='hidden' name='mode' value='" + MODE + "' />";
            }
            if(typeof live_campaign_id != 'undefined'){
                download_form += "<input type='hidden' name='live_campaign_id' value='" + live_campaign_id + "' />";
            }

            download_form += "</form>";

            Common.growlMessage(MvDataTableFilterDesign.exportReportMessage + download_form, 'info');

            var settings = $("#" + table_id).DataTable().settings();
            var ajaxData = settings[0].oAjaxData;
            var columns = ajaxData.columns;

            $('<input>').attr({type: 'hidden', name: 'draw', value: ajaxData.draw}).appendTo($('#export_form'));
            $('<input>').attr({
                type: 'hidden',
                name: 'order[0][column]',
                value: ajaxData.order[0].column
            }).appendTo($('#export_form'));
            $('<input>').attr({
                type: 'hidden',
                name: 'order[0][dir]',
                value: ajaxData.order[0].dir
            }).appendTo($('#export_form'));
            $('<input>').attr({type: 'hidden', name: 'start', value: '0'}).appendTo($('#export_form'));
            $('<input>').attr({type: 'hidden', name: 'length', value: '-1'}).appendTo($('#export_form'));

            var predefined_keys = ['columns', 'draw', 'length', 'maximizerFilter', 'order', 'search', 'start'];
            for (var key in ajaxData) {
                if (jQuery.inArray(key, predefined_keys) < 0) {
                    if(ajaxData[key] instanceof Array)
                    {
                        for (var i = 0; i < ajaxData[key].length; i++) {
                            $('<input>').attr({
                                type: 'hidden',
                                name: key + '[]',
                                value: ajaxData[key][i]
                            }).appendTo($('#export_form'));
                        }
                    }
                    else{
                        $('<input>').attr({
                            type: 'hidden',
                            name: key,
                            value: ajaxData[key]
                        }).appendTo($('#export_form'));
                    }
                }
            }

            for (var i = 0; i < columns.length; i++) {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'columns[' + i + '][data]',
                    value: columns[i].data
                }).appendTo($('#export_form'));
                $('<input>').attr({
                    type: 'hidden',
                    name: 'columns[' + i + '][name]',
                    value: columns[i].name
                }).appendTo($('#export_form'));
                $('<input>').attr({
                    type: 'hidden',
                    name: 'columns[' + i + '][column_title]',
                    value: columns[i].column_title
                }).appendTo($('#export_form'));
                $('<input>').attr({
                    type: 'hidden',
                    name: 'columns[' + i + '][searchable]',
                    value: columns[i].searchable
                }).appendTo($('#export_form'));
                $('<input>').attr({
                    type: 'hidden',
                    name: 'columns[' + i + '][orderable]',
                    value: columns[i].orderable
                }).appendTo($('#export_form'));
                $('<input>').attr({
                    type: 'hidden',
                    name: 'columns[' + i + '][search][value]',
                    value: ''
                }).appendTo($('#export_form'));
                $('<input>').attr({
                    type: 'hidden',
                    name: 'columns[' + i + '][search][regex]',
                    value: false
                }).appendTo($('#export_form'));
            }

            var maximizer_filter = dataTableFilterRecord[table_id];
            if(typeof maximizer_filter != 'undefined')
            {
                $.each(maximizer_filter, function(col_index, operator_value)
                {
                    var operator = '';

                    $.each(operator_value, function(key_operator, value_details)
                    {
                        operator = key_operator;

                        $.each(value_details, function(index, value_object)
                        {
                            try //try catch is used because, exporting to excel should not be affected by any error in filter.
                            {
                                for(var key in value_object)
                                {
                                    if(value_object[key] instanceof Array)
                                    {
                                        for (var i = 0; i < value_object[key].length; i++) {
                                            $('<input>').attr({
                                                type: 'hidden',
                                                name: 'maximizerFilter[' + col_index + '][' + operator + '][' + index + '][' + key + '][]',
                                                value: value_object[key][i]
                                            }).appendTo($('#export_form'));
                                        }

                                    }else{

                                        $('<input>').attr({
                                            type: 'hidden',
                                            name: 'maximizerFilter[' + col_index + '][' + operator + '][' + index + '][' + key + ']',
                                            value: value_object[key]
                                        }).appendTo($('#export_form'));

                                    }
                                }

                            }catch(err) {}

                        });

                    });

                });
            }

            $('#export_form').submit();
        } else {
            Common.growlMessage(MvDataTableFilterDesign.exportReportFailureMessage, 'danger');
        }
    };

    var prepareDataTableColumnNamesArray = function (datatable_obj, table_id, delimiter) {
        /*var columns = "";
         debug.log(aoColumns);
         jQuery.each(aoColumns, function(index, column) {
         // do not add checkbox column to the array
         if(column.data != 'check_box_id'){
         columns += (columns == "") ? column.sTitle : delimiter + column.sTitle;
         }
         });*/

        //debug.log(datatable_obj.columns().visible().join(', '));

        //Find out the index of hidden columns
        var hidden_column_index = [];
        var col_status = datatable_obj.columns().visible();
        for (var i = 0; i < col_status.length; i++) {
            if (col_status[i] == false)
                hidden_column_index.push(i);
        }

        //Show all the columns
        datatable_obj.columns().visible(true, true);

        var columns = [];

        $('#' + table_id + ' thead').children('tr').each(function () {

            var col_ar = [];

            $(this).children('th').each(function () {

                var colspan = 1;
                var rowspan = 1;
                if ($(this).find("input").attr('type') != "checkbox" && !$(this).hasClass("hidden") && !$(this).hasClass("hidden_in_export") || ( $(this).hasClass("show_in_export")) ) {
                    colspan = $(this).attr('colspan');
                    rowspan = $(this).attr('rowspan');

                    var col_name = $(this).clone().children().remove().end().text();
                    if(col_name == "" || typeof col_name == "undefined")
                        col_name = ($(this).find("span").html() == undefined) ? $(this).html() : $(this).find("span").html();

                    col_name = col_name.replace(/(<([^>]+)>)/ig,"");
                    col_ar.push({'col_name': col_name, 'rowspan': rowspan, 'colspan': colspan});
                }

            });

            columns.push(col_ar);

        });

        //As of now, 12th August, 2016, we are showing the filtered columns if it is hidden
        //hide the columns which were hidden
        for (var i = 0; i < hidden_column_index.length; i++) {
            datatable_obj.column(hidden_column_index[i]).visible(false, false);
        }

        return columns;
    };

    var drawfilterForGlobalSearch = function (table_id) {

        var input_value = MvDataTableCheckbox.getGlobalSearchValue(table_id);

        var filter_ref = $("#" + table_id + "_filter ." + css_class.filter_conditions_container);

        var and_ref = filter_ref.find(".and_column_input-1-1");

        if (and_ref.length == 0 && input_value != '') {
            /* First time with this operator */
            filter_ref.append(MvDataTableFilterDesign.AndField('All columns', 'Contains', input_value, -1, table_id, -1));
        }
        else if (input_value == '') {
            and_ref.remove();
        }
        else {
            and_ref.find('.and_column_value').html(input_value);
        }

        if (filter_ref.html() != '') {
            $("#" + table_id + "_filter").show();
        }
        else {
            $("#" + table_id + "_filter").hide();
        }
    };

    var overwrite_existing_filter_decision = function (table_id, pre_filter_condition) {
        bootbox.confirm(MvDataTableFilterDesign.overWriteExistingFilterMsg, function (result) {
            if (result) {
                datatableFilterClear(table_id, false);
            }
            datatableColumnPreFilterNow(table_id, false, false, pre_filter_condition);
        });
    };

    var datatableColumnPreFilterNow = function (table_id, overwrite_existing_filter, overwrite_existing_filter_without_asking, pre_filter_condition, data_filter_id, is_special_filter) {

        if (MvDataTableCheckbox.isAnyItemChecked(table_id)) {
            debug.log("Item checked and going to go to prefilternowhandler");
            MvDataTableCheckbox.preFilterNowHandler(table_id, overwrite_existing_filter, overwrite_existing_filter_without_asking, pre_filter_condition);
            return false;
        }

        /* This will overwrite the existing filter after asking permission, If Yes then Overwrite or else append to the existing Filter */
        if (overwrite_existing_filter && typeof dataTableFilterRecord[table_id] != 'undefined') {
            debug.log("overwrite the existing filter after asking permission, If Yes then Overwrite or else append to the existing Filter");
            overwrite_existing_filter_decision(table_id, pre_filter_condition);
            return false;
        }

        /* This will overwrite the existing filter without asking permission */
        if (overwrite_existing_filter_without_asking && typeof dataTableFilterRecord[table_id] != 'undefined') {
            debug.log("overwrite the existing filter without asking permission");
            datatableFilterClear(table_id, false);
        }

        debug.log("The pre-filter condition passed ");
        debug.log(pre_filter_condition);
        var columnIndexes = Common.extractKey(pre_filter_condition);
        debug.log(columnIndexes);
        var redraw_signal = false;
        var thead_ref = $("#" + table_id + " thead");
        var column_to_hide = -1;
        for (var i = 0; i < columnIndexes.length; i++) {
            var column_index = parseInt(columnIndexes[i]);
            var th_ref = thead_ref.find("th[data-col=" + column_index + "]");

            var filter_type = th_ref.attr("data-filter-type");
            column_to_hide = -1;
            //Added by Sameer on 7th August, 2016 to handle filters on columns hidden using .default-hidden class
            if (typeof filter_type == 'undefined') {
                //This will happen if we are hiding using default-hidden method because the DataTable removes the column completely from the DOM, hence we need to access it through the DataTable object
                var table_ref = $("#" + table_id).DataTable();
                th_ref = table_ref.column(column_index).header();//this retrieves the header node, i.e. the th
                filter_type = $(th_ref).attr('data-filter-type');
                $(th_ref).removeClass('default-hidden'); //Remove this later if you ever want to hide the columns after filtering based on their existing hidden state, similarly update code below where

                th_ref = $(th_ref);
                table_ref.column(column_index).visible(true);

                column_to_hide = column_index;
                //show the column
                //set a flag that the column has been shown
                //initialize the filter
            }
            var filter_now = th_ref.find("." + css_class.filter_now);
            var operator_allowed = [];
            var operand_ref;
            var operand_ref_2;
            var operator_ref = $("#" + table_id + "Operator" + column_index);
            if (typeof operator_ref == 'undefined') {
                continue;
            }
            switch (filter_type) {
                case 'text':
                    operator_allowed = DataTableFilterSettings.text;
                    operand_ref = $("#" + table_id + "FilterText" + column_index);
                    break;
                case 'number':
                    operator_allowed = DataTableFilterSettings.number;
                    operand_ref = $("#" + table_id + "FilterNumber" + column_index + '_1');
                    operand_ref_2 = $("#" + table_id + "FilterNumber" + column_index + '_2');
                    break;
                case 'select':
                    operator_allowed = DataTableFilterSettings.select;
                    operand_ref = $("#" + table_id + "FilterSelect" + column_index);
                    break;
                case 'date':
                    operator_allowed = DataTableFilterSettings.all;
                    operand_ref = $("#" + table_id + "FilterDateTime" + column_index);
                    break;
                default:
                    operator_allowed = DataTableFilterSettings.all;
                    operand_ref = $("#" + table_id + "FilterText" + column_index);
            }

            var operators = Common.extractKey(pre_filter_condition[column_index]);
            debug.log(operators);
            for (var j = 0; j < operators.length; j++) {
                debug.log("Allowed operators for filter type " + filter_type);
                debug.log(operator_allowed);
                if (operator_allowed.indexOf(convertOperator(operators[j])) < 0) {
                    debug.log("Not allowed: " + operators[j]);
                    continue;
                }

                $("#" + table_id + "Operator" + column_index + " option").filter(function () {
                    return $(this).text() == operators[j];
                }).prop('selected', true);

                var value_ref = pre_filter_condition[column_index][operators[j]];
                for (var k = 0; k < value_ref.length; k++) {
                    operand_ref.val(value_ref[k]);

                    if (operators[j] == 'Between') {
                        operand_ref_2.val(value_ref[++k]);
                    }

                    redraw_signal = true;
                    //pass flag about filter being set dynaically
                    datatableColumnFilterNow(filter_now, table_id, false, column_to_hide, data_filter_id);
                }
            }
        }

        if (redraw_signal) {
            redrawTable(table_id);
        }
    };

    var dataTableInLineSpecialFilter = function (table_id, column_index, overwrite_existing_filter, words_json, operator_value, filter_sub_type, redraw, data_filter_id) {

        if ( (column_index < 0) || (operator_value !== "Contains (multiple)" && operator_value !== "Does not contain (multiple)") )
        {
            return false;
        }

        var th_ref = $('table#'+ table_id +'>thead').find('th[data-col='+ column_index +']');
        var column_title = checkTitle(th_ref);
        if (!column_title)
        {
            return false;
        }

        //Operator value for the selected filter type
        var operator_index_value = DataTableFilterSettings.text.indexOf(operator_value);

        var text_area_ref = $("#" + table_id + "FilterTextArea" + column_index);
        var does_not_text_area_ref = $("#" + table_id + "FilterTextArea" + column_index + "_does_not");
        var filter_sub_type_radio_selector_text = "input[name='" + table_id + "FilterTextArea" + column_index + "_radio']";

        var searched_text = words_json.join('\n');
        var radio_type = '';

        //Pasting the filtered text
        if (operator_value === 'Contains (multiple)')
        {
            text_area_ref.val(searched_text);

        }else if(operator_value === 'Does not contain (multiple)')
        {
            does_not_text_area_ref.val(searched_text);
        }

        //Choosing the radio
        if(typeof filter_sub_type === 'undefined' || filter_sub_type === 'all')
        {
            $(filter_sub_type_radio_selector_text + '[value=all]' ).attr('checked', 'checked');
            radio_type = 'all';
        }
        else if(filter_sub_type === 'any')
        {
            $(filter_sub_type_radio_selector_text + '[value=any]' ).attr('checked', 'checked');
            radio_type = 'any';
        }

        //Making Data table Filter record with: Complete Reset
        dataTableFilterRecord[table_id] = {};

        //Complete Reset
        if (operator_value === "Contains (multiple)")
        {
            dataTableFilterRecord[table_id][column_index] = { 'Contains (multiple)': [] };
        }
        else if(operator_value === "Does not contain (multiple)")
        {
            dataTableFilterRecord[table_id][column_index] = { 'Does not contain (multiple)': [] };
        }

        //Pushing In the Filter Record
        dataTableFilterRecord[table_id][column_index][operator_value].push({ search_value: searched_text, 'concat_columns': '_', 'all_values': radio_type });

        var filter_condition_ref = $("#" + table_id + "_filter >ul>." + css_class.filter_conditions_container);
        var filter_added_html = MvDataTableFilterDesign.AndField(column_title, operator_value, searched_text, column_index, table_id, operator_index_value, data_filter_id, radio_type);
        filter_condition_ref.html(filter_added_html);

        if (MvDataTableCheckbox.isNotAjax(table_id))
        {
            pushFilteringConditionForNonAjax(table_id);
        }

        //Show or Hide the Filter Applied Info 'text' Area
        if (dataTableFilterRecord[table_id].hasOwnProperty(column_index)) {
            $("#" + table_id + "_filter").show();
        }
        else {
            $("#" + table_id + "_filter").hide();
        }

        if (redraw)
        {
            redrawTable(table_id);
        }

        //Add fliter class to the column for which the filter has been added
        if (typeof self_ref.last_filter_applied_column_index[table_id] != 'undefined') {
            self_ref.last_filter_applied_column_index[table_id].push(column_index);
        } else {
            self_ref.last_filter_applied_column_index[table_id] = [column_index];
        }
    };

    var dataTableInLineKeywordGroupSpecialFilter = function (self, table_id, column_index, overwrite_existing_filter, words_json, operator_value, filter_sub_type, redraw, data_filter_id) {

        if ( (column_index < 0) || (operator_value !== "Contains (multiple)" && operator_value !== "Does not contain (multiple)") )
        {
            return false;
        }

        var th_ref = $('table#'+ table_id +'>thead').find('th[data-col='+ column_index +']');
        var column_title = checkTitle(th_ref);
        if (!column_title)
        {
            return false;
        }

        var is_parent_keyword = $(self).hasClass('all-li');
        //Operator value for the selected filter type
        var operator_index_value = DataTableFilterSettings.text.indexOf(operator_value);

        var text_area_ref = $("#" + table_id + "FilterTextArea" + column_index);
        var does_not_text_area_ref = $("#" + table_id + "FilterTextArea" + column_index + "_does_not");
        var filter_sub_type_radio_selector_text = "input[name='" + table_id + "FilterTextArea" + column_index + "_radio']";

        var searched_text = words_json.join('\n');
        var radio_type = '';
        var parent_keyword_filter_type = '';
        var filter_condition_ref = $("#" + table_id + "_filter >ul>." + css_class.filter_conditions_container);
        var filter_added_html = '';

        //Making Data table Filter record with: Complete Reset
        dataTableFilterRecord[table_id] = {};

        //Pasting the filtered text
        if (operator_value === 'Contains (multiple)')
        {
            //Set the search Value
            text_area_ref.val(searched_text);

            //Init the Data table records
            if(!is_parent_keyword)
            {
                dataTableFilterRecord[table_id][column_index] = { 'Contains (multiple)': [], 'Contains': [] };
                parent_keyword_filter_type = 'Contains';
            }
            else
            {
                dataTableFilterRecord[table_id][column_index] = { 'Contains (multiple)': [] };
            }

        }
        else if(operator_value === 'Does not contain (multiple)')
        {
            //Set the search Value
            does_not_text_area_ref.val(searched_text);

            //Init the Data table records
            if(!is_parent_keyword)
            {
                dataTableFilterRecord[table_id][column_index] = { 'Does not contain (multiple)': [], 'Does not contain': [] };
                parent_keyword_filter_type = 'Does not contain';
            }
            else
            {
                dataTableFilterRecord[table_id][column_index] = { 'Does not contain (multiple)': [] };
            }
        }

        //Choosing the radio
        if(typeof filter_sub_type === 'undefined' || filter_sub_type === 'all')
        {
            $(filter_sub_type_radio_selector_text + '[value=all]' ).attr('checked', 'checked');
            radio_type = 'all';
        }
        else if(filter_sub_type === 'any')
        {
            $(filter_sub_type_radio_selector_text + '[value=any]' ).attr('checked', 'checked');
            radio_type = 'any';
        }

        //Pushing In the Main Filter Record
        dataTableFilterRecord[table_id][column_index][operator_value].push({ search_value: searched_text, 'concat_columns': '_', 'all_values': radio_type });

        //If its not a parnet Keyword Filter, Apply Contains filter
        if(!is_parent_keyword)
        {
            var parent_words_json_string = $(self).parent().find('.all-li').attr('words');
            var parent_words_json_object = {};
            var parent_words_text = '';
            var parent_operator_index_value = DataTableFilterSettings.text.indexOf(parent_keyword_filter_type);
            if(typeof parent_words_json_string !== 'undefined' || parent_words_json_string !== '')
            {
                parent_words_json_object = JSON.parse(parent_words_json_string);
                parent_words_text = parent_words_json_object.join(' || ');
            }

            //Pushing In the Parent Filter Record
            dataTableFilterRecord[table_id][column_index][parent_keyword_filter_type].push({ search_value: parent_words_text, 'concat_columns': '_', 'all_values': radio_type });

            filter_added_html += MvDataTableFilterDesign.AndField(column_title, parent_keyword_filter_type, parent_words_text, column_index, table_id, parent_operator_index_value, data_filter_id, radio_type);
        }

        filter_added_html += MvDataTableFilterDesign.AndField(column_title, operator_value, searched_text, column_index, table_id, operator_index_value, data_filter_id, radio_type);
        filter_condition_ref.html(filter_added_html);

        if (MvDataTableCheckbox.isNotAjax(table_id))
        {
            pushFilteringConditionForNonAjax(table_id);
        }

        //Show or Hide the Filter Applied Info 'text' Area
        if (dataTableFilterRecord[table_id].hasOwnProperty(column_index)) {
            $("#" + table_id + "_filter").show();
        }
        else {
            $("#" + table_id + "_filter").hide();
        }

        if (redraw)
        {
            redrawTable(table_id);
        }

        //Add fliter class to the column for which the filter has been added
        if (typeof self_ref.last_filter_applied_column_index[table_id] != 'undefined') {
            self_ref.last_filter_applied_column_index[table_id].push(column_index);
        } else {
            self_ref.last_filter_applied_column_index[table_id] = [column_index];
        }
    };

    var dataTableInLineTextSelectorSpecialFilter = function (table_id, column_index, overwrite_existing_filter, include, exclude, redraw, clear_partial, data_filter_id) {

        var th_ref = $('table#'+ table_id +'>thead').find('th[data-col='+ column_index +']');
        var column_title = checkTitle(th_ref);

        if ( ( !column_title || (exclude.length === 0 && include.length === 0)) && !clear_partial )
        {
            return false;
        }

        //Operator value for the selected filter types: 'Contains (multiple), 'Does not contain (multiple)'
        var operator_index_value_contains_multiple = DataTableFilterSettings.text.indexOf('Contains (multiple)');
        var operator_index_value_does_not_contain_multiple = DataTableFilterSettings.text.indexOf('Does not contain (multiple)');

        var text_area_ref = $("#" + table_id + "FilterTextArea" + column_index);
        var does_not_text_area_ref = $("#" + table_id + "FilterTextArea" + column_index + "_does_not");
        var filter_sub_type_radio_selector_text = "input[name='" + table_id + "FilterTextArea" + column_index + "_radio']";

        var current_contains_multiple_values = text_area_ref.val();
        var current_does_not_contains_multiple_values = does_not_text_area_ref.val();

        var all_include = [];
        var all_exclude = [];

        if(!clear_partial)
        {
            all_include = (include.join('\n') + '\n' + current_contains_multiple_values).split('\n');
            all_exclude = (exclude.join('\n') + '\n' + current_does_not_contains_multiple_values).split('\n');

            all_include = $.unique(all_include.filter(function(item){return item !== ''}));
            all_exclude = $.unique(all_exclude.filter(function(item){return item !== ''}));
        }
        else
        {
            all_include = include;
            all_exclude = exclude;
        }

        var searched_text_contains_multiple = all_include.join('\n');
        var searched_text_does_not_contain_multiple = all_exclude.join('\n');
        var radio_type_contains = 'any';
        var radio_type_does_not_contains = 'all';
        var filter_condition_ref = $("#" + table_id + "_filter >ul>." + css_class.filter_conditions_container);
        var filter_added_html = '';

        //Making Data table Filter record with: Complete Reset
        dataTableFilterRecord[table_id] = typeof dataTableFilterRecord[table_id] === 'undefined' ? {} : dataTableFilterRecord[table_id];

        if(overwrite_existing_filter == true)
        {
            dataTableFilterRecord[table_id] = {};
        }
        else
        {
            filter_added_html += getAllOtherFilterAppliedHtml(table_id, column_index);
        }

        //Pasting the filtered text: 'Contains (multiple)'
        if (include.length > 0)
        {
            //Set the search Value
            text_area_ref.val(searched_text_contains_multiple);

            //Init the Data table records
            dataTableFilterRecord[table_id][column_index] = $.extend(dataTableFilterRecord[table_id][column_index], { 'Contains (multiple)': [] });

            //Choosing the radio
            $(filter_sub_type_radio_selector_text + '[value=all]' ).attr('checked', 'checked');

            //Pushing In the Main Filter Record
            dataTableFilterRecord[table_id][column_index]['Contains (multiple)'].push({ search_value: searched_text_contains_multiple, 'concat_columns': '_', 'all_values': radio_type_contains });

            //Creating the Filter Text
            filter_added_html += MvDataTableFilterDesign.AndField(column_title, 'Contains (multiple)', searched_text_contains_multiple, column_index, table_id, operator_index_value_contains_multiple, data_filter_id, radio_type_contains);
        }
        else if(clear_partial && typeof dataTableFilterRecord[table_id][column_index] != 'undefined')
        {
            delete dataTableFilterRecord[table_id][column_index]['Contains (multiple)'];
        }

        //Pasting the filtered text: 'Does not contain (multiple)'
        if(exclude.length > 0)
        {
            //Set the search Value
            does_not_text_area_ref.val(searched_text_does_not_contain_multiple);

            //Init the Data table records
            dataTableFilterRecord[table_id][column_index] = $.extend(dataTableFilterRecord[table_id][column_index], { 'Does not contain (multiple)': [] });

            //Choosing the radio
            //$(filter_sub_type_radio_selector_text + '[value=any]' ).attr('checked', 'checked');

            //Pushing In the Main Filter Record
            dataTableFilterRecord[table_id][column_index]['Does not contain (multiple)'].push({ search_value: searched_text_does_not_contain_multiple, 'concat_columns': '_', 'all_values': radio_type_does_not_contains });

            //Creating the Filter Text
            filter_added_html += MvDataTableFilterDesign.AndField(column_title, 'Does not contain (multiple)', searched_text_does_not_contain_multiple, column_index, table_id, operator_index_value_does_not_contain_multiple, data_filter_id, radio_type_does_not_contains);
        }
        else if(clear_partial && typeof dataTableFilterRecord[table_id][column_index] != 'undefined')
        {
            delete dataTableFilterRecord[table_id][column_index]['Does not contain (multiple)'];
        }

        if(filter_added_html == '' || typeof filter_added_html == 'undefined')
        {
            datatableFilterClear(table_id);
        }

        filter_condition_ref.html(filter_added_html);

        if (MvDataTableCheckbox.isNotAjax(table_id))
        {
            pushFilteringConditionForNonAjax(table_id);
        }

        //Show or Hide the Filter Applied Info 'text' Area
        if (typeof dataTableFilterRecord[table_id] != 'undefined' && dataTableFilterRecord[table_id].hasOwnProperty(column_index)) {
            $("#" + table_id + "_filter").show();
        }
        else {
            $("#" + table_id + "_filter").hide();
        }

        if (redraw)
        {
            redrawTable(table_id);
        }

        //Add fliter class to the column for which the filter has been added
        if (typeof self_ref.last_filter_applied_column_index[table_id] != 'undefined') {
            self_ref.last_filter_applied_column_index[table_id].push(column_index);
        } else {
            self_ref.last_filter_applied_column_index[table_id] = [column_index];
        }
    };

    var getStaticTableDataCount = function (table_id, live) {

        var settings = $("#" + table_id).DataTable().settings();
        var ajaxData = settings[0].oAjaxData;
        var data_count = settings[0].json.recordsFiltered;

        if(live)
        {
            //fetch the current live records count and return.
        }

        if(typeof data_count == 'undefined')
        {
            data_count = 0;
        }

        return data_count;
    };

    var getAllOtherFilterAppliedHtml = function (table_id, column_index) {

        if(typeof dataTableFilterRecord[table_id][0] === 'undefined')
        {
            return '';
        }

        var filter_applied_html = '';
        dataTableFilterRecord[table_id] = typeof dataTableFilterRecord[table_id] === 'undefined' ? {} : dataTableFilterRecord[table_id];

        $.each(dataTableFilterRecord[table_id][0], function (filter_type, column_details) {

            $.each(column_details, function (column_number, details) {

                var th_ref = $('table#'+ table_id +'>thead').find('th[data-col='+ column_number +']');
                var column_title = checkTitle(th_ref);
                var operator_index = DataTableFilterSettings.text.indexOf(filter_type);
                var searched_text = details.search_value;
                var data_filter_id;
                var radio_type = details.all_values;

                if(column_index != column_number || (column_index == column_number && $.inArray(filter_type, [ 'Contains (multiple)', 'Does not contain (multiple)' ]) < 0))
                {
                    filter_applied_html += MvDataTableFilterDesign.AndField(column_title, filter_type, searched_text, column_number, table_id, operator_index, data_filter_id, radio_type);
                }

            });
        });

        return filter_applied_html;
    };

    var convertOperator = function (operator) {
        switch (operator) {
            case '>':
                return '&gt;';
            case '>=':
                return '&gt;=';
            case '<':
                return '&lt;';
            case '<=':
                return '&lt;=';
        }
        return operator;
    };

    var toTitleCase = function (str) {
        return str.replace(/\w\S*/g, function (txt) {
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        });
    };

    var datatableKeywordsAttributes = function (table_id, column_name, column_index, column_value, column_value_id, redraw) {
        if (typeof redraw == 'undefined') {
            redraw = true;
        }

        if (typeof dataTableFilterRecord[table_id] == 'undefined') {
            dataTableFilterRecord[table_id] = {};
        }

        if (typeof dataTableFilterRecord[table_id][column_index] == 'undefined') {
            dataTableFilterRecord[table_id][column_index] = {};
        }

        var columns = dataTableFilterRecord[table_id][column_index];
        var operator_value = DataTableFilterSettings.filter[3];
        if (typeof columns['k_attr'] == 'undefined') {
            columns['k_attr'] = [];
            columns['k_attr'].push({
                search_value: [],
                type: 4
            });
        }

        if (columns['k_attr'][0]['search_value'].indexOf(column_value_id) >= 0) {
            return false;
        }

        columns['k_attr'][0]['search_value'].push(column_value_id);

        $("#" + table_id + "_filter ." + css_class.filter_conditions_container).append(MvDataTableFilterDesign.AndField(column_name, 'k_attr', column_value, column_index, table_id, 4));


        if (dataTableFilterRecord[table_id].hasOwnProperty(column_index)) {
            $("#" + table_id + "_filter").show();
        }
        else {
            $("#" + table_id + "_filter").hide();
        }

        if (redraw) {
            redrawTable(table_id);
        }

        return true;
    };

    var datatableManagerFilter = function (table_id, data_type, column_index, type, column_value, column_value_id, exclude_selected, redraw) {

        if (typeof redraw == 'undefined') {
            redraw = true;
        }

        if (typeof dataTableFilterRecord[table_id] == 'undefined') {
            dataTableFilterRecord[table_id] = {};
        }

        if (typeof dataTableFilterRecord[table_id][column_index] == 'undefined') {
            dataTableFilterRecord[table_id][column_index] = {};
        }

        var operator_value = DataTableFilterSettings.filter[(type - 1)];
        var columns = dataTableFilterRecord[table_id][column_index];

        if (typeof columns[operator_value] == 'undefined') {
            columns[operator_value] = [];
            /* First time with this operator */
            $("#" + table_id + "_filter ." + css_class.filter_conditions_container).append(MvDataTableFilterDesign.AndField(toTitleCase(data_type), operator_value, column_value, column_index, table_id, type));
        }
        else {
            var filter_condition_ref = $("#" + table_id + "_filter ." + css_class.filter_conditions_container + " ." + css_class.and_column_input + column_index + type);

            var signal = 0;

            if ($.trim(filter_condition_ref.find('.' + css_class.and_column_value).html()) == column_value) {
                signal = 1;
            }

            filter_condition_ref.find('.' + css_class.or_column_value).each(function () {
                if ($.trim($(this).html()) == column_value) {
                    signal = 1;
                    return false;
                }
            });

            /* Don't Delete this because above foreach loop is running */
            if (signal == 1) {
                return false;
            }

            filter_condition_ref.find("." + css_class.and_column_value).parent().append(MvDataTableFilterDesign.OrField(column_value));

        }

        if (typeof columns[operator_value][0] == 'undefined') {
            /* formation of final array */
            columns[operator_value].push({
                search_value: [],
                type: type,
                source_type: specialOperatorForFilter[data_type],
                exclude: exclude_selected
            });
        }

        columns[operator_value][0]['search_value'].push(column_value_id);


        if (dataTableFilterRecord[table_id].hasOwnProperty(column_index)) {
            $("#" + table_id + "_filter").show();
        }
        else {
            $("#" + table_id + "_filter").hide();
        }

        if (redraw) {
            redrawTable(table_id);
        }

        return true;
    };

    var datatableColumnFilterNow = function (self, table_id, redraw, column_to_hide, data_filter_id) {
        debug.log(self);
        if (MvDataTableCheckbox.isAnyItemChecked(table_id)) {
            MvDataTableCheckbox.filterNowHandler(table_id, self);
            return false;
        }

        if (typeof redraw == 'undefined') {
            redraw = true;
        }

        if (typeof column_to_hide == "undefined") {
            column_to_hide = -1;
        }

        var th_ref = $(self).parents("th");
        //detect if flag set, then hide thos columns again

        var column_index = checkColIndex(th_ref);
        debug.log(column_index);
        var operator_ref = $("#" + table_id + "Operator" + column_index);
        var datetime_ref = $("#" + table_id + "FilterDateTime" + column_index);
        var select_ref = $("#" + table_id + "FilterSelect" + column_index);
        var text_ref = $("#" + table_id + "FilterText" + column_index);
        var text_area_ref = $("#" + table_id + "FilterTextArea" + column_index);
        var does_not_text_area_ref = $("#" + table_id + "FilterTextArea" + column_index + "_does_not");
        var all_values_radio = $("input[name='" + table_id + "FilterTextArea" + column_index + "_radio']:checked");
        var number_ref_1 = $("#" + table_id + "FilterNumber" + column_index + '_1');
        var number_ref_2 = $("#" + table_id + "FilterNumber" + column_index + '_2');

        if (column_index < 0) {
            return false;
        }

        var column_title = checkTitle(th_ref);
        if (!column_title) {
            return false;
        }

        var operator_index_value = operator_ref.val();
        var operator_value = operator_ref.find('option:selected').text();
        if (operator_value == "") {
            bootbox.alert(MvDataTableFilterDesign.blankOperatorConditionMsg);
            return false;
        }

        var filter_type = checkFilterType(th_ref);
        debug.log("Filter type: " + filter_type + " for col " + column_index);
        var column_value = '';
        var search_value = '';
        var all_values = '';

        if (filter_type == 'date') {
            column_value = datetime_ref.val();
            search_value = $.datepicker.formatDate('yy-mm-dd', new Date(column_value));
            column_value = $.datepicker.formatDate('M d, yy', new Date(column_value));
        }
        else if (filter_type == 'select') {
            column_value = select_ref.find('option:selected').text();
            search_value = select_ref.val();
        }
        else if (filter_type == 'number') {

            var selected_option = operator_ref.find('option:selected').text();
            debug.log('Operator selected: ' + selected_option);
            if (selected_option != 'Between') {
                column_value = number_ref_1.val();
            }
            else {
                $('#filter_number_error_container').remove();
                var from = number_ref_1.val();
                var to = number_ref_2.val();
                debug.log("Between filter: " + number_ref_1.val() + " & " + number_ref_2.val());
                //if it's a pre-defined filter, then the values will come comma-separated and so which we should explode and take the second number as vale
                if (from.indexOf(',') > 0) {
                    var both_values = from.split(',');
                    from = both_values[0];
                    to = both_values[1];
                    debug.log("After re-extraction from comma-separation: From: " + from + " & To: " + to);
                }
                if (from == '' || to == '') {

                    $('#number_filter_to_div' + table_id + column_index).append("<div style='color: red' id='filter_number_error_container'><small>From & To Required</small></div>");
                    $(self).parents("." + css_class.column_filter_container).addClass("open");
                    return;
                }
                else {
                    $('#filter_number_error_container').remove();
                    column_value = [number_ref_1.val(), number_ref_2.val()];
                }


            }

            search_value = column_value;
        }
        else {
            if (operator_value == "Contains (multiple)"){

                column_value = text_area_ref.val();
                all_values = all_values_radio.val();

            }else if(operator_value == "Does not contain (multiple)"){
                column_value = does_not_text_area_ref.val();
            }else{
                column_value = text_ref.val();
            }
            search_value = column_value;
        }

        if (typeof dataTableFilterRecord[table_id] == 'undefined') {
            dataTableFilterRecord[table_id] = {};
        }

        if (typeof dataTableFilterRecord[table_id][column_index] == 'undefined') {
            dataTableFilterRecord[table_id][column_index] = {};
        }

        var columns = dataTableFilterRecord[table_id][column_index];

        /*custom CONCAT Condition */
        var concat_columns_attr = checkConcat(th_ref);
        var concat_separator = checkConcatSeparator(th_ref);
        var concat_columns = '_'; // No effect on concat in server side
        if (concat_columns_attr.length > 0) {
            concat_columns = concat_columns_attr.join('_') + '_' + concat_separator;
        }
        if (operator_value == "Is Empty" || operator_value == "Is Not Empty") {
            column_value = '';
        }

        if (typeof columns[operator_value] == 'undefined') {
            columns[operator_value] = [];
            /* First time with this operator */
            $("#" + table_id + "_filter ." + css_class.filter_conditions_container).append(MvDataTableFilterDesign.AndField(column_title, operator_value, column_value, column_index, table_id, operator_index_value, data_filter_id, all_values));
        }
        else if (operator_value != "Is Empty" && operator_value != "Is Not Empty") {
            var filter_condition_ref = $("#" + table_id + "_filter ." + css_class.filter_conditions_container + " ." + css_class.and_column_input + column_index + operator_index_value);

            var data = $.trim(filter_condition_ref.find("." + css_class.and_column_value).html());

            if (data == column_value) {
                if (redraw)
                    bootbox.alert(MvDataTableFilterDesign.sameConditionMsg);
                return false;
            }

            var signal = 0;
            filter_condition_ref.find('.' + css_class.or_column_value).each(function () {
                if ($.trim($(this).html()) == column_value) {
                    if (redraw)
                        bootbox.alert(MvDataTableFilterDesign.sameConditionMsg);
                    signal = 1;
                    return false;
                }
            });

            /* Don't Delete this because above foreach loop is running */
            if (signal == 1) {
                return false;
            }

            if (operator_value == "Contains (multiple)" || operator_value == "Does not contain (multiple)") {
                filter_condition_ref.find("." + css_class.and_column_value).parent().parent().html(MvDataTableFilterDesign.AndField(column_title, operator_value, column_value, column_index, table_id, operator_index_value, data_filter_id, all_values));
            }else {
                filter_condition_ref.find("." + css_class.and_column_value).parent().append(MvDataTableFilterDesign.OrField(column_value));
            }

        }
        else {
            if (redraw)
                bootbox.alert(MvDataTableFilterDesign.sameConditionMsg);
            return false;
        }

        /* formation of final array */
        if (operator_value == "Contains (multiple)" || operator_value == "Does not contain (multiple)"){
            columns[operator_value].splice(0, 1, {search_value: search_value, concat_columns: concat_columns, all_values: all_values})
        }else{
            columns[operator_value].push({search_value: search_value, concat_columns: concat_columns, all_values: all_values});
        }

        /* Reset All Controls */
        operator_ref.prop('selectedIndex', 0);
        datetime_ref.val('');
        select_ref.prop('selectedIndex', 0);
        number_ref_1.val('');
        number_ref_1.removeAttr('placeholder');
        number_ref_2.parent().hide('slow');
        number_ref_2.val('');
        text_ref.val('');

        if (dataTableFilterRecord[table_id].hasOwnProperty(column_index)) {
            $("#" + table_id + "_filter").show();
        }
        else {
            $("#" + table_id + "_filter").hide();
        }

        if (MvDataTableCheckbox.isNotAjax(table_id)) {
            pushFilteringConditionForNonAjax(table_id);
        }

        if (redraw) {
            redrawTable(table_id);
        }

        if (column_to_hide > 0) {
            var table_ref = $("#" + table_id).DataTable();
            table_ref.column(column_index).visible(true);
        }

        //Add fliter class to the column for which the filter has been added
        if (typeof self_ref.last_filter_applied_column_index[table_id] != 'undefined') {
            self_ref.last_filter_applied_column_index[table_id].push(column_index);
        } else {
            self_ref.last_filter_applied_column_index[table_id] = [column_index];
        }

    };

    var applyFilterClass = function (table_id) {
        if (typeof self_ref.last_filter_applied_column_index[table_id] != 'undefined') {
            var table = $('#' + table_id).DataTable();
            var length = self_ref.last_filter_applied_column_index[table_id].length;

            for (var i = 0; i < length; i++) {
                table
                    .column(self_ref.last_filter_applied_column_index[table_id][i])
                    .nodes()
                    .to$()      // Convert to a jQuery object
                    .addClass('dtblFilterCell');

                var head = table.column(self_ref.last_filter_applied_column_index[table_id][i]).header();
                $(head).addClass('dtblFilterCell');
            }
        }
    }

    var redrawTable = function (table_id) {
        $("#" + table_id).DataTable().draw();
    };

    var processNonAjaxFilter = function (table_id, aData) {
        var records = dataTableFilterRecord[table_id];
        var search_value = '';
        var current_condition = true;
        var previous_condition = true;

        for (var column_index in records) {
            for (var operator in records[column_index]) {
                for (var i = 0; i < records[column_index][operator].length; i++) {
                    search_value = records[column_index][operator][i]['search_value'];

                    current_condition = processOrFilterCondition(aData[column_index], operator, search_value);
                    if (current_condition) {
                        // If any one condition true no need of going forward because one true in `or` expressions makes result tru.
                        break;
                    }
                }

                previous_condition = previous_condition && current_condition;

                // If any one condition false no need of going forward because one false in `and` expressions makes result false.
                if (!previous_condition) {
                    return false;
                }
            }
        }
        return previous_condition;
    };

    var pushFilteringConditionForNonAjax = function (table_id) {
        jQuery.fn.dataTableExt.afnFiltering.push(
            function (oSettings, aData, iDataIndex) {
                return processNonAjaxFilter(table_id, aData);
            }
        );
    };

    var processOrFilterCondition = function (operand, operator, search_value) {
        switch (operator) {
            case '='                :
                return operand.toLowerCase() == search_value.toLowerCase();
            case 'Not Equals'       :
                return operand.toLowerCase() != search_value.toLowerCase();
            case "Contains"         :
                return operand.toLowerCase().indexOf(search_value.toLowerCase()) >= 0;
            case "Does not contain" :
                return operand.toLowerCase().indexOf(search_value.toLowerCase()) < 0;
            case "Is Empty"         :
                return operand === '';
            case "Is Not Empty"     :
                return operand !== '';
        }

        if (search_value === '') {
            return false;
        }

        if (!isNaN(operand)) {
            /* Converted to floating Number */
            operand = parseFloat(operand);
            search_value = parseFloat(search_value);
        }

        switch (operator) {
            case '<'             :
                return operand < search_value;
            case '>'             :
                return operand > search_value;
            case '<='            :
                return operand <= search_value;
            case '>='            :
                return operand >= search_value;
        }

        return true;
    };

    var filterOperatorUniqueId = function (operator) {
        switch (operator) {
            case '='                :
                return '__eq__';
            case 'Not Equals'       :
                return '__not__eq__';
            case "Contains"         :
                return '__contains__';
            case "Does not contain" :
                return '__does__not__contain__';
            case "Is Empty"         :
                return '__is__empty__';
            case "Is Not Empty"     :
                return '__is__not__empty__';
            case '<'             :
                return '__lt__';
            case '>'             :
                return '__gt__';
            case '<='            :
                return '__le__';
            case '>='            :
                return '__ge__';
        }

        return '_';
    };

    var datatableFilterClear = function (table_id, redraw) {
        delete dataTableFilterRecord[table_id];

        $("#" + table_id + "_filter ." + css_class.filter_conditions_container).html('');
        $("#" + table_id + "_filter").hide();
        $("#" + table_id + "_wrapper .dataTables_filter label input").val('');

        if($.fn.DataTable.isDataTable( '#' + table_id ))
            MvDataTableCheckbox.setGlobalSearchValue(table_id, '');

        if (typeof redraw == 'undefined') {
            redrawTable(table_id);
        }

        if (typeof self_ref.last_filter_applied_column_index[table_id] != 'undefined') {
            var table = $('#' + table_id).DataTable();
            var length = self_ref.last_filter_applied_column_index[table_id].length;
            for (var i = 0; i < length; i++) {
                var head = table.column(self_ref.last_filter_applied_column_index[table_id][i]).header();
                $(head).removeClass('dtblFilterCell');
            }
            self_ref.last_filter_applied_column_index[table_id] = undefined;
        }
    };

    var closeDatatableFilterTag = function (self) {
        var table_id = $(self).attr("table_id");
        var column_index = $(self).attr("column_index");
        var operator_value = $(self).attr("operator_value");
        var operator_index_value = $(self).attr("operator_index_value");

        $('.' + css_class.and_column_input + column_index + operator_index_value).remove();

        if (column_index != '-1') {
            delete dataTableFilterRecord[table_id][column_index][operator_value];
        }
        else {
            $("#" + table_id + "_wrapper .dataTables_filter label input").val('');
            MvDataTableCheckbox.setGlobalSearchValue(table_id, '');
        }

        if (column_index == '-1' || dataTableFilterRecord[table_id].hasOwnProperty(column_index)) {
            redrawTable(table_id);
        }

        if (typeof self_ref.last_filter_applied_column_index[table_id] != 'undefined') {
            var index = self_ref.last_filter_applied_column_index[table_id].indexOf(column_index);
            var table = $('#' + table_id).DataTable();
            var head = table.column(self_ref.last_filter_applied_column_index[table_id][index]).header();
            $(head).removeClass('dtblFilterCell');
            if (index > -1)
                self_ref.last_filter_applied_column_index[table_id].splice(index, 1);
        }
    };

    var serverParams = function (aoDataSelf, datableId, callback) {
        var columns = aoDataSelf.columns;
        /* Sending Column Title Text */
        var column_index = 0;
        $("#" + datableId + " thead tr th").each(function (index) {

            var title = $(this).attr("data-custom-title");
            if (typeof title != 'undefined') {
                columns[column_index]['column_title'] = title;
                column_index++;
            }
        });

        /* for sending custom individual Filter Data */
        aoDataSelf.maximizerFilter = dataTableFilterRecord[datableId];

        if (typeof callback != 'undefined') {
            /* For Custom Ajax Parameter */
            callback(aoDataSelf);
        }
    };

    var drawKeywordGroupFilterData = function (table_id, column_name, column_index) {

        var ajax_url = $("#" + table_id).DataTable().ajax.url();
        var settings = $("#" + table_id).DataTable().settings();
        var ajaxData = settings[0].oAjaxData;
        var columns = ajaxData.columns;

        ajaxData.start = 0;
        ajaxData.length = -1;

        var post_data = make_array({'columns': columns}).concat(ajaxData)[0];
        post_data.data_keyword_grouping = 'on';
        post_data.keyword_grouping_column_name = column_name;
        post_data.keyword_grouping_column_index = column_index;

        var interval;
        var progress_bar = $('#' + table_id + '_keyword_groups_progress');
        progress_bar.val(1);
        var size = MvDataTableFilter.getStaticTableDataCount(table_id);
        var brick = size * 0.01;
        progress_bar.attr('max', size);

        var input = {
            url: ajax_url,
            data: post_data,
            timeout: 60000*3,
            beforeSend: function () {
                var refresh_icon = $('<i>').addClass('fa fa-refresh fa-spin refresh-icon').attr({ 'table-id': table_id });
                $('.keyword-group-filter-refresh[table-id='+ table_id +']').append(refresh_icon);

                interval = setInterval(function () {
                    var current_value = progress_bar.val();
                    progress_bar.val(current_value + brick);

                }, 150);

            },
            success: function (response) {
                this.mySuccess();
                MvDataTableFilterDesign.shouldRefreshKgData = false;
                progress_bar.val(100);
                clearInterval(interval);
                progress_bar.val(0);

                $('.refresh-icon[table-id='+ table_id +']').remove();
                MvDataTableFilter.drawKeywordGroupFilter(table_id, JSON.parse(response));
            },
            type: 'POST'
        };

        Common.Ajax(input);
    };

    var drawKeywordGroupFilter = function (table_id, keyword_group_data) {

        var data_finger_print = keyword_group_data.keyword_data_print;
        var keyword_groups = keyword_group_data.keyword_groups;

        var append_to = $('#' + table_id + '_keyword_groups');

        if(typeof keyword_group_data.keyword_groups == 'undefined')
        {
            $('div.keyword-group-wrapper[table-id=' + table_id + ']').remove();
            var no_data_html = KeywordGroup.loaders.loadNoData(table_id);

            append_to.append(no_data_html);
            KeywordGroup.events.init();
            return append_to;
        }

        var keyword_group_finger_print_exist = $('div.keyword-group-wrapper[finger-print=' + data_finger_print + '][table-id=' + table_id + ']').length > 0;

        if( ! keyword_group_finger_print_exist )
        {
            $('div.keyword-group-wrapper[table-id=' + table_id + ']').remove();

            var json_keyword_group_data = [];
            var column_number = 0;
            var column_name = '';
            if(typeof keyword_groups !== 'undefined')
            {
                json_keyword_group_data = keyword_groups;
                column_number = keyword_group_data.column_index;
                column_name = keyword_group_data.column_name;
            }

            var html = KeywordGroup.init(data_finger_print, table_id, column_number, column_name, json_keyword_group_data);

            append_to.append(html).show();

            if(typeof keyword_group_data.warnings !== 'undefined' && keyword_group_data.warnings !== '')
            {
                $('.keyword-group-warning-span').attr('title', keyword_group_data.warnings);
            }

            KeywordGroup.events.init();
            $('div.keyword-group-wrapper[table-id=' + table_id + ']').addClass('open').find('.keyword-group-ul-wrapper').addClass('open');
        }
    };

    return {
        dataTableFilterRecord: dataTableFilterRecord,
        postFilterClearCallback: post_filter_clear_callback,
        init: function () {
            drawIndividualColumnFilter();
            registerEvents();
            ajaxCallManagers();
            getKeywordDifficultyModal();
        },
        registerSubEvents: function () {
            registerSubEvents();
        },
        initializeSpecificTableId: function (tableId) {
            drawIndividualColumnFilter(tableId);
            //registerEvents();
            registerSubEvents();//Important to not call registerEvents() as these events will be duplicated on an ajax call //Was purposely refactored on 23rd June, 2016 by Sameer because we needed to call only this subset of functions on an Ajax re-initilization of the table. Calling above functions on re-initialization would be problematic as they would be duplicating the events.
        },
        serverParams: function (aoDataSelf, datableId, callback) {
            serverParams(aoDataSelf, datableId, callback);
        },
        datatableColumnFilterNow: function (self, table_id) {
            datatableColumnFilterNow(self, table_id);
        },
        datatableColumnPreFilterNow: function (table_id, overwrite_existing_filter, overwrite_existing_filter_without_asking, pre_filter_condition) {
            datatableColumnPreFilterNow(table_id, overwrite_existing_filter, overwrite_existing_filter_without_asking, pre_filter_condition);
        },
        enableManagers: function (keyword_manager, page_manager, tag_manager, manager_url) {
            enableManagers(keyword_manager, page_manager, tag_manager, manager_url);
        },
        enableKeywordDifficulty: function (enable_kd, kd_modal_url) {
            enableKeywordDifficulty(enable_kd, kd_modal_url);
        },
        setLoadedManagersWithoutTag: function (keyword_manager, page_manager, tag_manager) {
            setLoadedManagersWithoutTag(keyword_manager, page_manager, tag_manager);
        },
        setLoadedManagersWithTag: function (keyword_manager, page_manager, tag_manager) {
            setLoadedManagersWithTag(keyword_manager, page_manager, tag_manager);
        },
        isKeywordManagerLoadedWithoutTag: function () {
            return (self_ref.keyword_manager_without_tag > 0 );
        },
        isTagManagerLoadedWithoutTag: function () {
            return (self_ref.tag_manager_without_tag > 0);
        },
        isPageManagerLoadedWithoutTag: function () {
            return (self_ref.page_manager_without_tag > 0);
        },
        isKeywordManagerLoadedWithTag: function () {
            return (self_ref.keyword_manager_with_tag > 0 );
        },
        isTagManagerLoadedWithTag: function () {
            return (self_ref.tag_manager_with_tag > 0);
        },
        isPageManagerLoadedWithTag: function () {
            return (self_ref.page_manager_with_tag > 0);
        },
        tagSelect: function (desired_data) {
            if (desired_data.loading) return desired_data.text;
            if (desired_data.id === desired_data.text) {
                return '<span>' + desired_data.text + '</span><small class="pull-right">' + MvDataTableFilterDesign.create_new_tag + '</small>';
            }
            return desired_data.text;
        },
        drawfilterForGlobalSearch: function (table_id) {
            drawfilterForGlobalSearch(table_id);
        },
        datatableClearAllFilter: function (table_id) {
            datatableFilterClear(table_id, false);
        },
        datatableClearAllFilterRedraw: function (table_id) {
            datatableFilterClear(table_id);
        },
        datatableAppliedFilters: function (table_id) {
            return dataTableFilterRecord[table_id];
        },
        filterOperatorUniqueId: function (operator) {
            return filterOperatorUniqueId(operator);
        },
        setDatatablePostFilterClearCallback: function (table_id, callback) {
            post_filter_clear_callback[table_id] = callback;
        },
        applyFilterClass: function (table_id) {
            applyFilterClass(table_id);
        },
        hideDefaultHiddenColumns: function (table_id) {
            //This is to hide columsn where we deine the class .default-hidden on the th
            //On 27th August, 2016 added second 'false' parameter to visible functions, hoping to see if it makes impact on speed. I think it made impact. (Sameer Panjwani). If the columns don't redraw properly, then we'll need to remove this or to not compromise on speed can try recalculate function (refer docs of visible)
            var table = $('#' + table_id).DataTable();
            table.columns('.default-hidden').visible(false,false);
        },
        adjustOddEvenColumns: function (table_id) {
            var table = $('#' + table_id).DataTable();
            if($('#' + table_id+' > thead >tr > th[class*="odd-even-group"]').length <=0){
                //Don't run this function if odd-even-group classes not present in the table
                return false;
            }
            var j = 0;
            var h = 1;
            var assumed_max_columns_to_parse = 20; //if ever there will be more columns for grouping by odd-even then we should increase this number or make it dynamic based on actual number
            for (var i = 0; i <= assumed_max_columns_to_parse; i++) {
                if ($('#' + table_id + ' .odd-even-group-' + i).length > 0) {
                    j++;
                    h = j%2;
                    if(h==0) { h=2;}
                    table.columns('.odd-even-group-' + i).header().flatten().to$().removeClass('col-group-1 col-group-2').addClass('col-group-' + h); //this is necessary to add to all hidden columns

                    table.columns('.odd-even-group-' + i).nodes().flatten().to$().removeClass('col-group-1 col-group-2').addClass('col-group-' + h); //this is necessary to add to all hidden columns children (row data)
                    $('.odd-even-group-' + i).removeClass('col-group-1 col-group-2').addClass('col-group-' + h); //this is necessary to add to any parent headers not covered

                    //console.log("added col-group " + h + " to temp-group " + i);
                }
            }

        },
        toggleTableColumnVisibility: function (element) {

            //On 27th August, 2016 added second 'false' parameter to visible functions, hoping to see if it makes impact on speed. I think it made impact. (Sameer Panjwani). If the columns don't redraw properly, then we'll need to remove this or to not compromise on speed can try recalculate function (refer docs of visible)

            //this is for the toggle functionality of the show/hide columns feature
            //show a loader here

            var checkbox_id = $(element).data('checkbox-id');
            var class_name = $(element).data('toggle-class');
            var table_id = $(element).data('table-id');
            var visible_status = 'show';

            if ($('#' + checkbox_id).is(":checked") == true) {
                visible_status = 'hide';
            }

            MvDataTableFilter.processToggleTableColumnVisibility(table_id, class_name, visible_status);
        },
        processToggleTableColumnVisibility: function(table_id, class_name, visible_status) {
            if($('#' + table_id).length < 1){
                return;
            }

            var table = $('#' + table_id).DataTable();
            var class_name_org = class_name;
            if(visible_status === 'show')
            {
                $('#'+class_name).prop('checked', true);
                if (class_name.indexOf('_parent') > 0) {

                    //This is to handle the situation of hiding columns that are not hidden by default and where there are multiple children columns
                    //To make this work the checkbox class for the dropdown under the show/hide array must be {class_name}_parent and the columns to be hidden must be {class_name}_child
                    class_name = class_name.replace("_parent", "_child");//We use the parent class name just to identify all the children columns
                    var table_child_columns = table.columns('.' + class_name).header().flatten().to$();

                    table.columns('.' + class_name).visible(true,false);

                    // console.log($(table_child_columns).attr('class'));
                    $(table_child_columns).removeClass('parent-hidden'); //we add a parent-hidden class the first time so that we can prevent showing columns that are hidden by default; we then remove it so that it can be shown if the parent column is checked again
                    table.columns('.default-hidden').visible(false,false);
                    var class_contents = $(table_child_columns).attr('class');
                    var col_group_name = class_contents.match(/(col-group-)\d/g);
                    if (col_group_name != null && col_group_name[0] != "") {

                        var col_group_number = parseInt(col_group_name[0].replace("col-group-", ""));

                    }

                } else {
                    table.columns('.' + class_name).header().flatten().to$().removeClass('default-hidden'); //we remove the class default-hidden for all columns including the parent-hidden so that it can be shown again if made visible later
                    table.columns('.' + class_name + ':not(.parent-hidden)').visible(true,false); //we are removinv the class as well so that it will show after a table redraw as well, since we're hididng based on the condition of the class being present/absent in datatable-js.blade.php
                }

            }else{
                $('#'+class_name).prop('checked', false);
                if (class_name.indexOf('_parent') > 0) {
                    class_name = class_name.replace("_parent", "_child");
                    var table_child_columns = table.columns('.' + class_name).header().flatten().to$();

                    $(table_child_columns).addClass('parent-hidden');
                    table.columns('.' + class_name).visible(false,false);

                } else {

                    table.columns('.' + class_name).visible(false,false).header().flatten().to$().addClass('default-hidden');
                }
            }
            MvDataTableFilter.adjustOddEvenColumns(table_id);
            var save_visibility_function_name = 'saveVisibilityState_' + table_id;
            if(typeof save_view_state_callback[table_id] !== 'undefined'){
                save_view_state_callback[table_id](visible_status, table_id, class_name_org);
            }
            //table.columns.adjust().draw( false );
            //hide the loader here
        },
        drawKeywordGroupFilter: function (table_id, keyword_group_data) {
            return drawKeywordGroupFilter(table_id, keyword_group_data);
        },
        drawKeywordGroupFilterData: function (table_id, column_name, column_index) {
            return drawKeywordGroupFilterData(table_id, column_name, column_index);
        },
        dataTableInLineTextSelectorSpecialFilter: dataTableInLineTextSelectorSpecialFilter,
        getStaticTableDataCount: getStaticTableDataCount,
        updateSaveViewStateCallback: function(table_id, callback){
            save_view_state_callback[table_id] = callback;
        }
    }
}();

function uc_words(str) {
    str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
        return letter.toUpperCase();
    });

    return str;
}

//Test comment
function prepare_keyword_values(self, post_to)
{
    var json_string = $(self).attr('word-json-word');
    var json_string_contain = $(self).attr('word-json-contain');
    var table_id = $(self).attr('table-id');
    var is_append_on = !$('#' + table_id + '_keyword_group_checkbox').is(':checked');

    var previous_value = $(post_to).val();

    if(is_append_on)
    {
        if (previous_value != '')
        {
            previous_value = previous_value + '\n';
        }
    }
    else
    {
        previous_value = '';
        $(post_to).val('');
    }

    if(typeof json_string == 'undefined')
    {
        return ;
    }

    var non_json = jQuery.makeArray(JSON.parse(json_string));
    var non_json_contain = jQuery.makeArray(JSON.parse(json_string_contain));


    if(non_json_contain.length == 1 && is_append_on)
    {
        non_json_contain = non_json_contain.join('\n') + '\n';
    }
    else
    {
        non_json_contain = '';
    }

    var value_to_post = previous_value + non_json_contain + ' ' + non_json.join(' \n ') + ' ';
    var value_post_array = data = $.unique( value_to_post.split('\n') );

    $(post_to).val(value_post_array.join('\n'));

    return value_post_array;
}

function column_data_copier_event() {
    var x = event.clientX, y = event.clientY,
        elementMouseIsOver = document.elementFromPoint(x, y);
}

function choose_column(element, view_or_all) {
    var table_id = $(element).attr('table-id');
    if (typeof table_id == 'undefined') {
        return;
    }

    var columns = $("#" + table_id).DataTable().settings()[0].oAjaxData.columns;

    if (columns.length == 0) {
        bootbox.alert('No Columns Available');
        return;
    }

    var select = $('<select>').attr({'class': 'column-chooser'});
    //select.append($('<option>').attr({value: '', 'column-number': ''}).html('Select a Column'));

    $.each(columns, function (index, column_item) {

        var is_head_visible = $('th[aria-controls="' + table_id + '"][data-col="' + index + '"]').is(':visible');
        if (column_item.column_title != '' && column_item.orderable && is_head_visible) {
            select.append($('<option>').attr({value: index, 'column-number': index}).html(column_item.column_title));
        }

    });

    var out_side_closure = $('<div>').append( $('<div>').attr({'class': 'col-md-6'}).append('Please select a column <br>').append(select) );
    var extra_options_html = '<div class="col-md-6 copy-type-chooser"> ' +
        'Choose Which data to copy: ' +
        '<label for="copy_radio_all" > All </lable> ' +
        '<input type="radio" name="column_copy_radio" id="copy_radio_all" checked="checked" value="all"> ' +
        '<label for="copy_radio_view" style="margin-left: 7px;"> In View </lable> ' +
        '<input type="radio" name="column_copy_radio" id="copy_radio_view" checked="" value="view"> ' +
        '</div>';

    out_side_closure.append(extra_options_html);
    //out_side_closure.a

    var html_to_message = $(out_side_closure).html();
    //select.select2();

    bootbox.dialog({
        title: "Choose a Column to Copy From",
        message: '<div class="col-md-12">' + html_to_message + '</div>',
        buttons: {
            danger: {
                className: "cancel1",
                label: '<i class="fa fa-times"></i> Cancel'
            },
            success: {
                label: '<i class="fa fa-check"></i> Confirm',
                className: 'btn mon-btn-blue',
                callback: function () {
                    var column_number = $('select.column-chooser').val();
                    view_or_all = $('input[name="column_copy_radio"]:checked').val();
                    copy_table_column_data(table_id, column_number, view_or_all)
                }
            }
        },
        callback: function (result) {
        }
    });

    $('select.column-chooser').select2().next().css('width', '200px');
    //$('select.column-chooser').next().css('width', '200px');
}

function copy_table_column_data(table_id, column_number, view_or_all) {

    if (typeof table_id == 'undefined' || typeof column_number == 'undefined') {
        return;
    }

    if (typeof view_or_all == 'undefined' || view_or_all == 'view') {
        return copy_table_column_data_view(table_id, column_number);
    }
    else if (view_or_all == 'all') {
        return copy_table_column_data_all(table_id, column_number);
    }
}

function copy_table_column_data_view(table_id, column_number) {
    var table_body_rows = $('table#' + table_id + '>tbody>tr');
    var text_array = [];

    $.each(table_body_rows, function (index, element) {

        var column_text = $($(element).find('td:not(.hidden)')[column_number - 1]).text();
        if (typeof column_text != 'undefined') {
            text_array.push(column_text.trim());
        }
    });

    copy_text_to_clipboard(text_array.join('\n'), table_id);
}

function copy_table_column_data_all(table_id, column_number) {
    var ajax_url = $("#" + table_id).DataTable().ajax.url();
    var settings = $("#" + table_id).DataTable().settings();
    var ajaxData = settings[0].oAjaxData;
    var columns = ajaxData.columns;

    var warning_boot_box;

    ajaxData.start = 0;
    ajaxData.length = -1;

    var post_data = make_array({'columns': columns}).concat(ajaxData)[0];
    post_data.data_copy = 'on';
    post_data.data_copy_col_num = column_number;

    var input = {
        url: ajax_url,
        data: post_data,
        beforeSend: function () {
            warning_boot_box =
                bootbox.dialog({
                    title: "Please wait",
                    message: '<div class="col-md-12 time-warning-content" style="text-align: center;"> Please wait Until we fetch the complete data. <br><br> <i class="fa fa-spinner fa-3 fa-spin"></i></div>',
                    buttons: {
                        success: {
                            label: '<i class="fa fa-check"></i> Ok',
                            className: 'btn mon-btn-blue'
                        }
                    },
                    callback: function (result) {
                    }
                });
        },
        success: function (column_values_array) {

            if (column_values_array.length == 0) {
                return;
            }

            column_values_array = JSON.parse(column_values_array);
            var text = column_values_array.join('\n');
            copy_text_to_clipboard(text, table_id);

            window.setTimeout(function () {
                var warning_content = $('.time-warning-content');
                if(warning_content.length > 0)
                {
                    warning_boot_box.remove();
                }
            }, 1000);
        },
        type: 'POST'
    };

    return $.ajax(input);
}

function copy_text_to_clipboard(text, table_id) {
    var temp_textarea = $('<textarea>');
    //Appending to the body is not working when the dialog box is open.
    //var append_to = $('#' + table_id);
    var append_to = $('.copy-type-chooser');

    if(append_to.length == 0)
    {
        append_to = $('body');
    }

    append_to.append(temp_textarea);
    temp_textarea.html(text);
    temp_textarea.focusin();
    temp_textarea.select();
    var copy_status = document.execCommand('copy', true);
    temp_textarea.remove();

    if (copy_status == false) {
        return copy_text_manual(text);
    }
    else
    {
        Common.growlMessage('Copied to clipboard', 'success', 5000);
    }
}

function copy_text_manual(text)
{
    var temp_textarea = $('<textarea>').attr({ row: 100, cols: 50 });
    temp_textarea.html(text);
    var extra_html = $('<div>').append(temp_textarea).html();

    var copy_manually_boot_box =
        bootbox.dialog({
            title: "Please Copy Manually",
            message: '<div class="col-md-12 copy-type-chooser" style="text-align: center;"> Text: <br><br> '+ extra_html +' </div>',
            buttons: {
                success: {
                    label: '<i class="fa fa-check"></i> Copy',
                    className: 'btn mon-btn-blue',
                    callback: function (result) {

                        var temp_textarea = $('.copy-type-chooser>textarea');
                        temp_textarea.focusin();
                        temp_textarea.select();
                        var copy_status = document.execCommand('copy', true);

                        if (copy_status == false) {
                            Common.growlMessage('Unable to copy Automatically', 'danger', 2000);
                        }
                        else
                        {
                            Common.growlMessage('Copied to clipboard', 'success', 5000);
                        }
                    }
                }
            }

        });

    return ;
}

function make_array(data_object) {
    if (typeof data_object != 'object') {
        return data_object;
    }

    var data_array = [];
    $.each(data_object, function (index, item) {

        if (typeof item != 'object') {
            data_array[index] = item;
        }
        else {
            data_array[index] = make_array(item);
        }

    });

    return data_array;
}