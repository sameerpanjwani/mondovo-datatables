<div class="custom-table-options inline pull-left">
    <span class="custom-table-options-tittle-text">{{$custom_title}}</span><span class="caret"></span>
    <ul class="list-group md-checkbox-list custom-table-options-list style-3" style="height: 210px; width: 230px" @if(!empty($list_id)) list-id="{{$list_id}}" @endif >
        @foreach($hide_show_elements as $column_title => $content_attributes)
                @if(isset($content_attributes['option_group']))
                    <li class="list-group-item">
                    {{ $column_title }}
                        <ul class="list-group md-checkbox-list custom-table-options-list">
                            @foreach($content_attributes['option_group'] as $column_title => $content_attributes)
                                @include('components.show-hide-columns-toolbar-inner-li')
                            @endforeach
                        </ul>
                    </li>
                @else
                    @include('components.show-hide-columns-toolbar-inner-li')
                @endif
        @endforeach
    </ul>
</div>