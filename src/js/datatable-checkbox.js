/**
 * Created by maximizer on 11/6/15.
 */
var MvDataTableCheckboxDesign = {
    CssClassCollection: {
        mv_checkbox: 'mv_single_checkbox',
        mv_checkbox_page_wise: 'mv_checkbox_page_wise',
        mv_records_toolbar: 'mv_records_toolbar',
        mv_selected_records: 'mv_selected_records',
        mv_clear_records_counter: 'mv_clear_records_counter',
        mv_select_all_records: 'mv_select_all_records',
        mv_clear_records: 'mv_clear_records',
        mv_hidden: 'hidden',
        mv_tooltips: 'tooltips'
    },
    All: 'All',
    No: 'No',
    RecordSelected: 'records have been selected',
    SelectMsg: 'Select',
    ClearMsg: 'Clear',
    RecordMsg: 'records',
    CheckboxesMsg: 'checkboxes',
    SearchAlertMsg: 'You have checked',
    SearchAlertSuffixMsg: 'records in the table. If you have not already tagged or copied the selected records, please do so now since all the checkboxes will be cleared after you proceed with this filter request. Are you sure you want to proceed?',
    SelectedRecords: function (records, table_id) {
        return '<div class="mv_records_toolbar" table-id="' + table_id + '"><hr class="clearfix margin-bottom-10 margin-top-10"><span class="mv_selected_records"><strong>' + records + '</strong> ' + MvDataTableCheckboxDesign.RecordSelected + ' &nbsp;</span></div>';
    },
    TotalRecords: function (total_records) {
        return '<a class="mv_select_all_records hidden primary-btn btn btn-sm tooltips"> <i class="fa fa-check-square"></i> ' + MvDataTableCheckboxDesign.SelectMsg + ' ' + MvDataTableCheckboxDesign.All + ' ' + total_records + ' ' + MvDataTableCheckboxDesign.RecordMsg + '</a>';
    },
    ClearRecords: function (records) {
        return '<a class="mv_clear_records cancel-btn btn btn-sm" > <i class="fa fa-remove"></i> '+MvDataTableCheckboxDesign.ClearMsg + ' <span class="mv_clear_records_counter">' + records + '</span> ' + MvDataTableCheckboxDesign.CheckboxesMsg+'</a>';
    },
    SearchMessage: function (records) {
        return MvDataTableCheckboxDesign.SearchAlertMsg + ' ' + records + ' ' + MvDataTableCheckboxDesign.SearchAlertSuffixMsg;
    }
};

var MvDataTableCheckbox = function () {
    var css_class = MvDataTableCheckboxDesign.CssClassCollection;
    var dataTableCheckedRecord = [];

    var processCheckBox = function (self, table_id, row_index) {
        if (typeof dataTableCheckedRecord[table_id] == 'undefined') {
            return false;
        }

        if (calculationOfNoOfRecords(table_id) < 0) {
            return false;
        }

        var keyArray = Common.extractKey(dataTableCheckedRecord[table_id]['columns']);
        if (keyArray.length == 0) {
            $(self).prop("checked", false);
            alert('For Developer: U have not assigned any column for checkbox');
            return false;
        }

        var ref = getAdata(table_id, row_index);
        for (var i = 0; i < keyArray.length; i++) {
            if (typeof dataTableCheckedRecord[table_id]['columns'][keyArray[i]] != 'undefined') {
                var elements = dataTableCheckedRecord[table_id]['columns'][keyArray[i]];
                if ((!isEnabledSelectAll(table_id) && $(self).is(':checked')) || (isEnabledSelectAll(table_id) && !$(self).is(':checked') )) {
                    elements.push(ref[keyArray[i]]);
                }
                else {
                    deleteIfExist(ref[keyArray[i]], elements);
                }
            }
        }
        uniqueValueInsert(self, table_id, row_index);

        //console.log(dataTableCheckedRecord[table_id]);
        return true;
    };

    var uniqueValueInsert = function (self, table_id, row_index) {
        var check_value = dataTableCheckedRecord[table_id]['check_value'];
        var long_string = makeLongString(table_id, row_index);
        if ((!isEnabledSelectAll(table_id) && $(self).is(':checked')) || (isEnabledSelectAll(table_id) && !$(self).is(':checked') )) {
            pushIfNotExist(long_string, check_value);
        }
        else {
            deleteIfExist(long_string, check_value);
        }
    };

    var getIndexOf = function (find_me, find_in) {
        return find_in.indexOf(find_me);
    };

    var deleteIfExist = function (find_me, find_in) {
        var position = getIndexOf(find_me, find_in);
        if (position >= 0) {
            find_in.splice(position, 1);
        }
    };

    var pushIfNotExist = function (find_me, find_in) {
        var position = getIndexOf(find_me, find_in);
        if (position < 0) {
            find_in.push(find_me);
        }
    };

    var makeLongString = function (table_id, row_index) {
        if (calculationOfNoOfRecords(table_id) < 0) {
            return false;
        }
        var ref = getAdata(table_id, row_index);
        var keyArray = Common.extractKey(ref);

        var long_string = '';
        for (var i = 0; i < keyArray.length; i++) {
            long_string += ref[keyArray[i]] + "|-|"; //'-' changed to '|-|' by vinod on May-22-2017
        }
        // could have been saved memory by using eqivalent to md5
        return long_string.replace(/(<([^>]+)>)/ig, "");
    };

    var getTableId = function (self) {
        return $(self).parents('table').attr('id');
    };

    var individualCheckBox = function (self) {
        var table_id = getTableId(self);
        if(!checkSelectedCheckBoxCount(self, table_id, false)){
            return false;
        }
        var row_index = $(self).parents('tr').index();
        processCheckBox(self, table_id, row_index);
        putRecordsSelected(table_id);
    };

    var pageWiseCheckBox = function (self) {
        var table_id = getTableId(self);
        if (typeof dataTableCheckedRecord[table_id] == 'undefined') {
            return false;
        }

        $("#" + table_id + " tbody tr").each(function (index) {

            if(!checkSelectedCheckBoxCount(self, table_id, false)){
                return false;
            }

            var ref = $(this).find('td:eq(0) input');
            if (ref.is(':checked') != $(self).is(':checked')) {
                ref.prop("checked", $(self).is(':checked'));
                return processCheckBox(ref, table_id, index);
            }
        });
        putRecordsSelected(table_id);
    };

    var checkSelectedCheckBoxCount = function(self, table_id, select_all) {
        var limit = $('#' + table_id).data('check-box-limit');
        var status = true;

        if(limit > 0){

            if(!select_all && !$(self).is(':checked')){
                return true;
            }

            if(select_all) {
                var total_records = getTotalRecords(table_id);
                if(limit < total_records){
                    status = false;
                }
            }else {
                var selected_count = calculateNoOfRecordsChecked(table_id);
                if (selected_count >= limit) {
                    status = false
                }
            }
        }

        if(!status){
            alert('You cannot select more than ' + limit + 'rows');
            $(self).removeAttr("checked");
            $(self).parent('span').removeClass('checked');
        }

        return status;
    };

    var updateUniform = function (table_id) {
        if (jQuery.uniform) {
            if(typeof $('#' + table_id).DataTable().scroller == 'undefined') {
                jQuery.uniform.update("#" + table_id + " ." + css_class.mv_checkbox + ", #" + table_id + " ." + css_class.mv_checkbox_page_wise);
            }else {
                jQuery.uniform.update($('#' + table_id).parent().parent().find('.' + css_class.mv_checkbox));
                jQuery.uniform.update($('#' + table_id).parent().parent().find('.' + css_class.mv_checkbox_page_wise));
            }
        }
    };

    var insertUniform = function (table_id) {
        if (jQuery.uniform) {
            if(typeof $('#' + table_id).DataTable().scroller == 'undefined')
            {
                $("#" + table_id + " ." + css_class.mv_checkbox + ", #" + table_id + " ." + css_class.mv_checkbox_page_wise).uniform();
            }else {
                $('#' + table_id).parent().parent().find('.' + css_class.mv_checkbox).uniform();
                $('#' + table_id).parent().parent().find('.' + css_class.mv_checkbox_page_wise).uniform();
            }
        }
    };

    var putRecordsSelected = function (table_id) {
        var clear_records = convertNoOfRecordsCheckedToText(table_id);
        var records = calculateNoOfRecordsChecked(table_id);
        var total_records = getTotalRecords(table_id);

        var table_ref = $("#" + table_id);
        var table_wrapper_ref = table_ref.parents('.table-scrollable');
        if (records == 0) {
            //table_wrapper_ref.prev().remove();
            $("#" + table_id+"_wrapper .mv_records_toolbar").remove(); //Added by Nagaraj on 17th June, 2016 because when calling this function after ajax the global search bar and global records dropdown section was getting removed, this is to ensure only the speciic section is removed
            table_wrapper_ref.find("." + css_class.mv_checkbox_page_wise).prop('checked', false);
            updateUniform(table_id);
            return false;
        }

        if (table_wrapper_ref.prev().attr('class') != css_class.mv_records_toolbar) {
            table_wrapper_ref.before(MvDataTableCheckboxDesign.SelectedRecords(records, table_id));

            if (dataTableCheckedRecord[table_id]['checkbox_control_text'] != '') {
                table_wrapper_ref.prev().prepend(Common.htmlDecode(dataTableCheckedRecord[table_id]['checkbox_control_text']));
            }

            table_wrapper_ref.prev().append(MvDataTableCheckboxDesign.TotalRecords(total_records));

            table_wrapper_ref.prev().append(MvDataTableCheckboxDesign.ClearRecords(clear_records));
        }
        else {
            table_wrapper_ref.prev().find('.' + css_class.mv_selected_records + ' strong').html(records);
            var clearObj = table_wrapper_ref.prev().find('.' + css_class.mv_clear_records);
            clearObj.find('.' + css_class.mv_clear_records_counter).html(clear_records);
            /*clearObj.attr('data-original-title', MvDataTableCheckboxDesign.ClearMsg + ' ' + clear_records + ' ' + MvDataTableCheckboxDesign.CheckboxesMsg);
            clearObj.tooltip('fixTitle');*/
        }

        var total_ref = table_wrapper_ref.prev().find('.' + css_class.mv_select_all_records);

        if (total_records == records) {
            total_ref.addClass(css_class.mv_hidden);
        }
        else {
            total_ref.removeClass(css_class.mv_hidden);
        }

        table_ref.find("." + css_class.mv_checkbox_page_wise).prop('checked', countOfCheckedCheckbox(table_id) == calculationOfNoOfRecords(table_id));
        updateUniform(table_id);
    };

    var registerDatatableEvents = function (table_id) {

    };

    var setKeyName = function (ajax_data, table_id) {
        if (typeof dataTableCheckedRecord[table_id] == 'undefined') {
            dataTableCheckedRecord[table_id] = {};
            registerEvents(table_id);
        }

        if (typeof dataTableCheckedRecord[table_id]['columns'] == 'undefined') {
            dataTableCheckedRecord[table_id]['columns'] = {};
        }

        if (typeof dataTableCheckedRecord[table_id]['select_all'] == 'undefined') {
            dataTableCheckedRecord[table_id]['select_all'] = false;
        }

        if (typeof dataTableCheckedRecord[table_id]['check_value'] == 'undefined') {
            dataTableCheckedRecord[table_id]['check_value'] = [];
        }

        dataTableCheckedRecord[table_id]['ajax_data'] = ajax_data;

    };

    var resetKeyNames = function (table_id) { //This function will be called just before datatable is initialized
        delete dataTableCheckedRecord[table_id]; //Only this line for now. Any other can be added as when required.
    };

    var drawCallbackDatatable = function (table_id, column_names) {
        var firstSignal = 0;

        if (typeof dataTableCheckedRecord[table_id] == 'undefined') {
            setKeyName('', table_id);
        }

        for (var i = 0; i < column_names.length; i++) {
            if (typeof dataTableCheckedRecord[table_id]['columns'][column_names[i]] == 'undefined') {
                dataTableCheckedRecord[table_id]['columns'][column_names[i]] = [];
                firstSignal = 1;
            }
        }

        if (typeof dataTableCheckedRecord[table_id]['ajax_url'] == 'undefined') {
            dataTableCheckedRecord[table_id]['ajax_url'] = getAjaxUrl(table_id);
        }

        if (firstSignal == 0) {
            refreshDataTableForCheckbox(table_id);
        }
    };

    var countOfCheckedCheckbox = function (table_id) {
        var checked_checkbox = 0;
        $("#" + table_id + " tbody tr").each(function () {
            var ref = $(this).find('td:eq(0) input');
            if (ref.is(':checked')) {
                checked_checkbox++;
            }
        });

        return checked_checkbox;
    };

    var selectFirstNRows = function(table_id, count) {
        for (var row_index = 0; row_index < count; row_index++){
            var check_value = dataTableCheckedRecord[table_id]['check_value'];
            var long_string = makeLongString(table_id, row_index);
            pushIfNotExist(long_string, check_value);
        }
        traverseAllCheckbox(table_id);
        putRecordsSelected(table_id);
        return true;
    };

    var traverseAllCheckbox = function (table_id) {
        var record_ref = dataTableCheckedRecord[table_id]['check_value'];
        if (isEnabledSelectAll(table_id)) {
            $("#" + table_id).find("." + css_class.mv_checkbox).prop('checked', true);
            updateUniform(table_id);
        }

        if (record_ref.length == 0) {
            return false;
        }

        $("#" + table_id + " tbody tr").each(function (row_index) {
            var ref = $(this).find('td:eq(0) input');
            if (record_ref.indexOf(makeLongString(table_id, row_index)) >= 0) {
                if (!isEnabledSelectAll(table_id)) {
                    ref.prop('checked', true);
                }
                else {
                    ref.prop('checked', false);
                }
            }
        });

        updateUniform(table_id);
    };

    var refreshDataTableForCheckbox = function (table_id) {
        var table_ref = $("#" + table_id);
        table_ref.find("." + css_class.mv_checkbox).prop('checked', false);
        updateUniform(table_id);

        if (dataTableCheckedRecord[table_id]['check_value'].length == 0 && !isEnabledSelectAll(table_id)) {
            return false;
        }

        traverseAllCheckbox(table_id);
        putRecordsSelected(table_id);
    };

    var getAjaxUrl = function (table_id) {
        if (getTableSetting(table_id).ajax == null) {
            return false;
        }
        return getTableSetting(table_id).ajax.url;
    };

    var selectAllCheckbox = function (self) {
        var table_id = $(self).parent().attr("table-id");
        if(!checkSelectedCheckBoxCount(self, table_id, true)){
            return false;
        }
        clearOrSelectAllRecords(table_id, true);
    };

    var clearAllRecords = function (self) {
        var table_id = $(self).parent().attr("table-id");
        clearOrSelectAllRecords(table_id, false);
    };

    var clearOrSelectAllRecords = function (table_id, status) {
        setCheckBox(table_id, status);
        emptyAllKeys(table_id);
        putRecordsSelected(table_id);
    };

    var setCheckBox = function (table_id, status) {
        dataTableCheckedRecord[table_id]['select_all'] = status;
        var table_ref = $("#" + table_id);
        table_ref.find("." + css_class.mv_checkbox_page_wise).prop('checked', status);
        table_ref.find("." + css_class.mv_checkbox).prop('checked', status);
        updateUniform(table_id);
    };

    var emptyAllKeys = function (table_id) {
        var keyArray = Common.extractKey(dataTableCheckedRecord[table_id]['columns']);
        for (var i = 0; i < keyArray.length; i++) {
            dataTableCheckedRecord[table_id]['columns'][keyArray[i]] = [];
        }

        dataTableCheckedRecord[table_id]['check_value'] = [];
    };

    var getAllTableRecords = function (table_id) {
        if (dataTableCheckedRecord[table_id]) {
            return dataTableCheckedRecord[table_id];
        }
        return false;
    };

    var getTableRecordsAjax = function (response, columnName, excludeValues) {
        var column_values = [];
        for (var i = 0; i < response.data.length; i++) {
            column_values.push(response.data[i][columnName]);
        }
        column_values = $.unique(column_values);
        if (excludeValues.length == 0) {
            return column_values;
        }

        return Common.remove_from_array(excludeValues, column_values);
    };


    var getTableColumnValues = function (table_id, columnNameOrIndex, callback) {
        var records = getAllTableRecords(table_id);
        if (!records) {
            callback(false, 0);
            return false;
        }

        var columnName = columnNameOrIndex;
        if (!isNaN(columnNameOrIndex) && !isNotAjax(table_id)) {
            columnName = getColumnNameFromIndex(table_id, columnNameOrIndex);
            if (!columnName) {
                callback(false, 0);
                return false;
            }
        }
        if (typeof records.columns[columnName] == 'undefined') {
            callback(false, 0);
            return false;
        }

        if (!records.select_all) {
            var unique_values = $.unique(records.columns[columnName]);
            records = convertNoOfRecordsCheckedToText(table_id);
            //clearOrSelectAllRecords(table_id, false);
            callback(unique_values, records);
            return unique_values;
        }

        /* For Handling not Ajax */
        if (isNotAjax(table_id)) {
            var column_values = $.unique(getAllTableDataForNonAjax(table_id, columnName));
            var excludeValues = records.columns[columnName];
            if (excludeValues.length > 0) {
                column_values = Common.remove_from_array(excludeValues, column_values);
            }
            records = convertNoOfRecordsCheckedToText(table_id);
            //clearOrSelectAllRecords(table_id, false);
            callback(column_values, records);
            return column_values;
        }

        /* Initialiaze start and length */
        records.ajax_data.start = 0;
        records.ajax_data.length = getTotalRecords(table_id);
        records.ajax_data.checkbox_column = columnName;

        var input = {
            url: records.ajax_url,
            data: records.ajax_data,
            success: function (response) {
                var column_values = response;//getTableRecordsAjax(response, columnName, records.columns[columnName]);
                var checked_checkbox = convertNoOfRecordsCheckedToText(table_id);
                //clearOrSelectAllRecords(table_id, false);
                callback(column_values, checked_checkbox);
            },
            type: 'POST',
            beforeSend: function () {
                $("#" + table_id + "_processing").show();
            },
            complete: function () {
                $("#" + table_id + "_processing").hide();
            }
        };
        Common.Ajax(input);
    };

    var getValuesToCopy = function (table_id, columnIndex, callback) {
        var records = getAllTableRecords(table_id);
        if (!records) {
            callback(false);
            return false;
        }

        var columnName = columnIndex;
        if (!isNaN(columnIndex) && !isNotAjax(table_id)) {
            columnName = getColumnNameFromIndex(table_id, columnIndex);
            if (!columnName) {
                callback(false);
                return false;
            }
        }

        if (!records.select_all) {
            var unique_values = $.unique(records.check_value);
            var values = [];
            columnIndex = columnIndex - 1; //This is to remove checkbox column index
            for (var i = 0; i < unique_values.length; i++) {
                var temp_values = unique_values[i].split('|-|');
                values[i] = temp_values[columnIndex].trim();
            }
            callback(values);
            return values;
        }

        /* For Handling not Ajax */ //@todo Handle non-ajax table
        if (isNotAjax(table_id)) {
            var column_values = $.unique(getAllTableDataForNonAjax(table_id, columnName));
            var excludeValues = records.columns[columnName];
            if (excludeValues.length > 0) {
                column_values = Common.remove_from_array(excludeValues, column_values);
            }
            records = convertNoOfRecordsCheckedToText(table_id);
            //clearOrSelectAllRecords(table_id, false);
            callback(column_values, records);
            return column_values;
        }

        /* Initialiaze start and length */
        records.ajax_data.start = 0;
        records.ajax_data.length = getTotalRecords(table_id);
        records.ajax_data.checkbox_column = columnName;

        var input = {
            url: records.ajax_url,
            data: records.ajax_data,
            success: function (response) {
                var column_values = response;//getTableRecordsAjax(response, columnName, records.columns[columnName]);
                var checked_checkbox = convertNoOfRecordsCheckedToText(table_id);
                callback(column_values);
            },
            type: 'POST',
            beforeSend: function () {
                $("#" + table_id + "_processing").show();
            },
            complete: function () {
                $("#" + table_id + "_processing").hide();
            }
        };
        Common.Ajax(input);
    };

    var getColumnValues = function (table_id, columnNamesOrIndexes, callback) {
        var records = getAllTableRecords(table_id);
        if (!records) {
            callback(false);
            return false;
        }

        var columnNames = [];
        var columnIndexes = [];
        var col_count = columnNamesOrIndexes.length;
        for(var i = 0; i < col_count; i++)
        {
            var columnNameOrIndex = columnNamesOrIndexes[i];
            if (!isNaN(columnNameOrIndex) && !isNotAjax(table_id)) {
                columnIndexes[i] = columnNameOrIndex;
                columnNames[i] = getColumnNameFromIndex(table_id, columnNameOrIndex);
                if (!columnNames[i]) {
                    callback(false);
                    return false;
                }
            }else{
                columnIndexes[i] = getColumnIndexFromName(table_id, columnNameOrIndex);
                columnNames[i] = columnNameOrIndex;
            }
        }

        if (!records.select_all) {
            var unique_values = $.unique(records.check_value);
            var values = [];
            var count = columnIndexes.length;
            for (var i = 0; i < unique_values.length; i++) {
                var temp_values = unique_values[i].split('|-|');
                var row_values = [];
                for(var j = 0; j < count; j++)
                {
                    columnIndex = columnIndexes[j] - 1;
                    row_values[columnNames[j]] = temp_values[columnIndex].trim();
                }
                values[i] = row_values;
            }
            callback(values);
            return values;
        }

        /* For Handling not Ajax */
        if (isNotAjax(table_id)) {
            //@todo Handle non-ajax table
        }

        /* Initialiaze start and length */
        records.ajax_data.start = 0;
        records.ajax_data.length = getTotalRecords(table_id);
        //records.ajax_data.checkbox_column = columnNames[0];

        var input = {
            url: records.ajax_url,
            data: records.ajax_data,
            success: function (response) {
                var values = [];
                var column_values = response.data;//getTableRecordsAjax(response, columnName, records.columns[columnName]);
                var length = column_values.length;
                var count = columnNames.length;
                for(var i = 0; i < length; i++)
                {
                    var row_values = [];
                    for(var j = 0; j < count; j++)
                    {
                        var columnName = columnNames[j];
                        row_values[columnNames[j]] = column_values[i][columnName].toString().replace(/(<([^>]+)>)/ig, "").trim();
                    }
                    values[i] = row_values;
                }
                var checked_checkbox = convertNoOfRecordsCheckedToText(table_id);
                callback(values);
            },
            type: 'POST',
            beforeSend: function () {
                $("#" + table_id + "_processing").show();
            },
            complete: function () {
                $("#" + table_id + "_processing").hide();
            }
        };
        Common.Ajax(input);
    };

    var filterNowHandler = function (table_id, self) {
        bootbox.confirm(MvDataTableCheckboxDesign.SearchMessage(convertNoOfRecordsCheckedToText(table_id)), function (flag) {
            if (flag) {
                stepsAfterAllowingContinueToSearch(table_id);
                MvDataTableFilter.datatableColumnFilterNow(self, table_id);
            }
        });
    };

    var preFilterNowHandler = function (table_id, overwrite_existing_filter, overwrite_existing_filter_without_asking, pre_filter_condition) {
        bootbox.confirm(MvDataTableCheckboxDesign.SearchMessage(convertNoOfRecordsCheckedToText(table_id)), function (flag) {
            if (flag) {
                stepsAfterAllowingContinueToSearch(table_id);
                MvDataTableFilter.datatableColumnPreFilterNow(table_id, overwrite_existing_filter, overwrite_existing_filter_without_asking, pre_filter_condition);
            }
        });
    };

    var stepsAfterAllowingContinueToSearch = function (table_id) {
        setCheckBox(table_id, false);
        emptyAllKeys(table_id);
        putRecordsSelected(table_id);
    };

    var signalForfilterEvents = function (table_id, input_self) {
        var input_value = $(input_self).val();
        if (!isAnyItemChecked(table_id)) {
            $(input_self).data('mv', input_value);
            setGlobalSearchValue(table_id, input_value);
            redrawTable(table_id);
            return false;
        }

        if (typeof $(input_self).data('wait') != 'undefined') {
            return false;
        }

        $(input_self).data('wait', 'true');

        var prev_value = $(input_self).data('mv');
        if (typeof prev_value == 'undefined') {
            prev_value = '';
        }

        bootbox.confirm(MvDataTableCheckboxDesign.SearchMessage(convertNoOfRecordsCheckedToText(table_id)), function (flag) {
            if (flag) {
                stepsAfterAllowingContinueToSearch(table_id);
                setGlobalSearchValue(table_id, input_value);
                redrawTable(table_id);
            }
            else {
                $(input_self).val(prev_value);
            }
            $(input_self).removeData('wait');
        });
    };

    var globalSearchBoxHandler = function (self) {
        var table_id = $(self).attr('aria-controls');
        signalForfilterEvents(table_id, self);
    };

    var registerEvents = function (table_id) {

        if(typeof $('#' + table_id).DataTable().scroller == 'undefined') {

            $(document).on('click', "#" + table_id + " ." + css_class.mv_checkbox_page_wise, function () {
                pageWiseCheckBox(this);
            });
        }else {

            $('#' + table_id).parent().parent().find('.' + css_class.mv_checkbox_page_wise).on('click', function () {
                pageWiseCheckBox(this);
            });
        }

        $(document).on('click', "#" + table_id + " ." + css_class.mv_checkbox, function () {
            individualCheckBox(this);
        });

        $(document).on('click', "#" + table_id + "_wrapper ." + css_class.mv_select_all_records, function () {
            selectAllCheckbox(this);
        });

        $(document).on('click', "#" + table_id + "_wrapper ." + css_class.mv_clear_records, function () {
            var attr = $(this).attr('data-original-title');
            if (typeof attr !== typeof undefined && attr !== false) {
                $(this).tooltip('destroy');
            }
            clearAllRecords(this);
        });

        $("#" + table_id + "_wrapper .dataTables_filter label input").unbind();

        $(document).on('keyup', "#" + table_id + "_wrapper .dataTables_filter label input", function (event) {
            globalSearchBoxHandler(this);
            MvDataTableFilter.drawfilterForGlobalSearch(table_id);
        });
    };

    var getAllTableDataForNonAjax = function (table_id, column_index) {
        var setting_ref = getTableSetting(table_id);
        var data = [];
        var count = getTotalRecords(table_id);
        var record_obj = setting_ref.aoData;
        for (var i = 0; i < count; i++) {
            data.push(record_obj[i]._aData[column_index]);
        }
        return data;
    };

    var redrawTable = function (table_id) {
        var processing_id = table_id + "_processing";
        var processing_ref = document.getElementById(processing_id);
        if (processing_ref == null) {
            return false;
        }
        processing_ref.innerHTML = Common.messageCollection.datatable_processing_message;
        $("#" + table_id).DataTable().draw();
    };

    var isNotAjax = function (table_id) {
        return dataTableCheckedRecord[table_id]['ajax_url'] == false;
    };

    var getTotalRecords = function (table_id) {
        if (isNotAjax(table_id)) {
            return getTableSetting(table_id).aiDisplay.length;
        }
        return parseInt(getTableSetting(table_id)._iRecordsDisplay);
    };

    var getTableSetting = function (table_id) {
        return $("#" + table_id).DataTable().settings()[0];
    };

    var getAdata = function (table_id, row_index) {
        var setting_ref = getTableSetting(table_id);
        if (isNotAjax(table_id)) {
            row_index = row_index + parseInt(setting_ref._iDisplayStart);
        }
        return setting_ref.aoData[row_index]._aData;
    };

    var setGlobalSearchValue = function (table_id, search_value) {
        $("#" + table_id).DataTable().settings()[0].oPreviousSearch.sSearch = search_value;
    };

    var getGlobalSearchValue = function (table_id) {
        return getTableSetting(table_id).oPreviousSearch.sSearch;
    };

    var calculationOfNoOfRecords = function (table_id) {
        var record = $("#" + table_id).find("." + css_class.mv_checkbox).length;

        if (record == 0) {
            return -1;
        }

        return record;
    };

    var getColumnNameFromIndex = function (table_id, index) {
        var setting = getTableSetting(table_id);
        if (typeof setting.aoColumns[index] == 'undefined') {
            return false;
        }
        return setting.aoColumns[index].data;
    };

    var getColumnIndexFromName = function (table_id, name) {
        var index = false;
        var setting = getTableSetting(table_id);

        var col_count = setting.aoColumns.length;
        for(var i = 0; i < col_count; i++)
        {
            if(name == setting.aoColumns[i].data)
            {
                index = i;
                break;
            }
        }

        return index;
    };

    var isAnyItemChecked = function (table_id) {
        var setting_ref = getTableSetting(table_id);
        if (isEnabledSelectAll(table_id)) {
            return dataTableCheckedRecord[table_id]['check_value'].length != getTotalRecords(table_id);
        }
        return dataTableCheckedRecord[table_id]['check_value'].length != 0;
    };

    var isEnabledSelectAll = function (table_id) {
        if (typeof dataTableCheckedRecord[table_id] == 'undefined') {
            return false;
        }
        return dataTableCheckedRecord[table_id]['select_all'];
    };

    var convertNoOfRecordsCheckedToText = function (table_id) {
        var records = calculateNoOfRecordsChecked(table_id);
        if (getTotalRecords(table_id) == records) {
            return MvDataTableCheckboxDesign.All;
        }
        if (records == 0) {
            return MvDataTableCheckboxDesign.No;
        }
        return records;
    };

    var calculateNoOfRecordsChecked = function (table_id) {
        if (typeof dataTableCheckedRecord[table_id] == 'undefined') {
            return false;
        }
        if (isEnabledSelectAll(table_id)) {
            return getTotalRecords(table_id) - dataTableCheckedRecord[table_id]['check_value'].length

        }
        return dataTableCheckedRecord[table_id]['check_value'].length;
    };

    var insertIntoCheckboxControlsExtraText = function (table_id, html_text) {
        if (typeof dataTableCheckedRecord[table_id] == 'undefined') {
            return false;
        }
        dataTableCheckedRecord[table_id]['checkbox_control_text'] = html_text;
    };

    var isRequiredToCallDataTable = function (table_id) {
        if (typeof dataTableCheckedRecord[table_id] == 'undefined') {
            return true;
        }
        return false;
    };

    var callDataTableIfRequired = function (table_id, datatable_callback) {
        if (isRequiredToCallDataTable(table_id)) {
            datatable_callback();
        }
        else {
            redrawTable(table_id);
        }
    };

    return {
        dataTableCheckedRecord: dataTableCheckedRecord,
        drawCallbackDatatable: function (table_id, column_names) {
            drawCallbackDatatable(table_id, column_names)
        },
        setKeyName: function (table_id, ajax_data) {
            setKeyName(table_id, ajax_data);
        },
        getAllTableRecords: function (table_id) {
            return getAllTableRecords(table_id);
        },
        getTableColumnValues: function (table_id, columnNameOrIndex, callback) {
            return getTableColumnValues(table_id, columnNameOrIndex, callback);
        },
        getValuesToCopy: function (table_id, columnNameOrIndex, callback) {
            return getValuesToCopy(table_id, columnNameOrIndex, callback);
        },
        getColumnValues: function (table_id, columnNamesOrIndexes, callback) {
            return getColumnValues(table_id, columnNamesOrIndexes, callback);
        },
        isAnyItemChecked: function (table_id) {
            return isAnyItemChecked(table_id);
        },
        filterNowHandler: function (table_id, self) {
            filterNowHandler(table_id, self);
        },
        preFilterNowHandler: function (table_id, overwrite_existing_filter, overwrite_existing_filter_without_asking, pre_filter_condition) {
            preFilterNowHandler(table_id, overwrite_existing_filter, overwrite_existing_filter_without_asking, pre_filter_condition);
        },
        isNotAjax: function (table_id) {
            return isNotAjax(table_id);
        },
        updateUniform: function (table_id) {
            updateUniform(table_id);
        },
        insertUniform: function (table_id) {
            insertUniform(table_id);
        },
        insertIntoCheckboxControlsExtraText: function (table_id, html_text) {
            insertIntoCheckboxControlsExtraText(table_id, html_text);
        },
        isRequiredToCallDataTable: function (table_id) {
            isRequiredToCallDataTable(table_id);
        },
        callDataTableIfRequired: function (table_id, datatable_callback) {
            callDataTableIfRequired(table_id, datatable_callback);
        },
        redrawTable: function (table_id) {
            redrawTable(table_id);
        },
        disableFilter: function (table_id) {
            if (getTotalRecords(table_id) < 5) {
                $("#" + table_id + " ." + MvDataTableFilterDesign.CssClassCollection.column_filter_container).hide();
            }
            else {
                $("#" + table_id + " ." + MvDataTableFilterDesign.CssClassCollection.column_filter_container).show();
            }
        },
        clearOrSelectAllRecords: function (table_id, status) {
            return clearOrSelectAllRecords(table_id, status);
        },
        setGlobalSearchValue: function (table_id, search_value) {
            return setGlobalSearchValue(table_id, search_value);
        },
        getGlobalSearchValue: function (table_id) {
            return getGlobalSearchValue(table_id);
        },
        resetKeyNames: function (table_id) {
            return resetKeyNames(table_id);
        },
        selectFirstNRows: function(table_id, count){
            return selectFirstNRows(table_id, count);
        }
    }

}();