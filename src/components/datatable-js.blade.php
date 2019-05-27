if ( document.getElementById('{{$table_id}}') )
{
MvDataTableCheckbox.resetKeyNames('{{$table_id}}');

@if(!is_bool($table_info))
    $('#{{$table_id}}').attr('info', '{{$table_info}}');
@endif

var before_first_request_{{$table_id}} = true;
MvDataTableFilter.enableManagers({{$keyword_manager_column}}, {{$page_manager_column}}, {{$tag_manager_column}}, '{{$manager_url}}');
MvDataTableFilter.enableKeywordDifficulty({{$enable_kd}}, '{{$kd_modal_url}}');
var {{$table_id}}_oTable = $("#{{$table_id}}").DataTable({
processing: true,
<?php if($custom_empty_table_message != ''){ ?>
language: {
emptyTable: "{{$custom_empty_table_message}}"
},
<?php } ?>
<?php if($pdf_view=="yes") { ?>
//bInfo:false,
dom:"t",
//paging:false,
//searching:false,
pageLength:<?=$data_rows;?>,
<?php }else{ ?>
<?php if($loading_on_scroll == 'yes'){ ?>
dom: "frtiS",
scrollX: true,
scrollY: <?=$scroll_y;?>,
deferRender: true,
scroller: {
loadingIndicator: true
},
<?php } ?>
searching: {{ $searching }},
paging: {{ $paging }},
ordering: {{ $ordering }},
info: {{ $info }},
pageLength:<?=$page_length;?>,
<?php } ?>
autoWidth: <?=($auto_width)?'true':'false';?>,
<?php if(!empty($ajax_url)) { ?>
preDrawCallback: function(settings){
if(before_first_request_{{$table_id}})
{
MvDataTableFilter.datatableClearAllFilter("{{$table_id}}");
@if(!empty($pre_draw_callback_function))
    {!! $pre_draw_callback_function !!}
@endif
}
$("#{{$table_id}}").css("opacity",0.5);
$("#{{$table_id}}_processing").html('<button class="btn"><i class="fa fa-spinner fa-spin"></i> Loading the table\'s contents</button>').css('position','absolute');


},
serverSide: true,
ajax: {
type: "POST",
url: "<?=asset(urldecode($ajax_url))?>",
data: function ( d ) {
MvDataTableFilter.serverParams(d, "{{$table_id}}"@if(! empty($ajax_callback_function) ), {!! $ajax_callback_function !!} @endif);
MvDataTableCheckbox.setKeyName(d, "{{$table_id}}");
},
error: function(jqXHR, exception) {
Common.datatableAjaxErrorHandler(jqXHR, "{{$table_id}}");
},
},
<?php } if($js_order != '[]') { ?>
order:  <?= $js_order ?>,
<?php } ?>
drawCallback: function( settings ) {
$("#{{$table_id}}").css("opacity",1);
MvDataTableCheckbox.drawCallbackDatatable("{{$table_id}}", <?= $checkbox_columns ?>);
MvDataTableCheckbox.insertUniform("{{$table_id}}");
MvDataTableCheckbox.insertIntoCheckboxControlsExtraText("{{$table_id}}", "{{$checkbox_control_text}}");
MvDataTableCheckbox.disableFilter("{{$table_id}}");
/*MvDataTableFilter.applyFilterClass("{{$table_id}}");*/
<?php if($hide_default_columns){ ?>
MvDataTableFilter.hideDefaultHiddenColumns('{{$table_id}}');
<?php } ?>
MvDataTableFilter.adjustOddEvenColumns('{{$table_id}}');

@if(! empty($ajax_success_callback_function) )
    {!! $ajax_success_callback_function !!}
@endif



@if(!is_bool($text_selector_filter))
    TextSelectorFilter.init('{{$table_id}}', '{{$text_selector_filter}}');
@endif
setTimeout(function(){ $("#{{$table_id}}_wrapper").find('input[type=search]').prop('disabled', false); }, 1300);

},
bWidth: false,
columns: <?= $js_columns ?>
<?php if(!empty($datatable_js_objects))
{
    foreach($datatable_js_objects as $key=>$value)
    {
        echo ",\n".$key.": $value";
    }

}
?>

<?php if(! empty($datatable_fixed_columns_objects))
{
$count_of_variable = count( $datatable_fixed_columns_objects );
$each_line=',\n';
foreach($datatable_fixed_columns_objects as $key=>$value)
{
    $string_for = $key.": $value";
    if($count_of_variable != 1)
    {
        $string_for .= $each_line;
    }
    $count_of_variable--;
}
?>
,fixedColumns: {
{{$string_for}}
}
<?php
}
?>

});
$("#{{$table_id}}").data("ajax_retry", 0);
var ajax_request = "{{$ajax_request}}";
if(ajax_request=="is_ajax"){
MvDataTableFilter.initializeSpecificTableId('{{$table_id}}');//this is done so as to re-initialize the filters
MvDataTableCheckbox.clearOrSelectAllRecords('{{$table_id}}', false); // This is done so as to clear all the checkboxes if selected before ajax call
}
//console.log("For table id: "+{{$table_id}}+" the ajax request is "+ajax_request);
before_first_request_{{$table_id}} = false;

<?php if($pdf_view=="yes") { ?>
$('#{{$table_id}}').parents('.mv-box-layout').addClass('mv-box-layout-pdf');
<?php } ?>
@if($visibility_set_callback != "")
    {!! $visibility_set_callback !!};
@endif
@if($visibility_save_call_back != "")
    MvDataTableFilter.updateSaveViewStateCallback('{{$table_id}}', {!! $visibility_save_call_back !!});
@endif
}