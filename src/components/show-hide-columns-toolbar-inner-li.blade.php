<li class="list-group-item @if(isset($content_attributes['css-class'])) {{$content_attributes['css-class']}} @endif" data-checkbox-id="{{ $content_attributes['class'] }}" data-table-id="{{ $table_id }}" data-name="{{$table_id}}_{{ $content_attributes['class'] }}" data-toggle-class="{{ $content_attributes['class'] }}" onclick="MvDataTableFilter.toggleTableColumnVisibility(this);" @if(isset($content_attributes['data-attributes'])) {{$content_attributes['data-attributes']}} @endif>
    <div class="show-hide-list">
        <div class="updated-filter-checkbox">
            <input type="checkbox" id="{{ $content_attributes['class'] }}" @if($content_attributes['status'] == 'checked') checked data-default="checked" @endif @if(isset($content_attributes['data-class'])) class="{{$content_attributes['data-class']}}" @endif>
            <label>
                <span class="box">{!! $column_title !!}</span>
            </label>
        </div>
    </div>
</li>