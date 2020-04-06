<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $file_name }}</title>
    <meta charset="UTF-8">
    <meta name=description content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<table>
    {{--<tr><td colspan="2">Logo Comes Here!</td></tr>--}}

    @foreach($head_rows as $head_row)
        <tr align="center" style="color: #ffffff; background-color: #366092; text-align: center; vertical-align: middle;">
            @if(is_array($head_row))
                @foreach($head_row as $heading)
                    <th colspan="{{ $heading->colspan }}" rowspan="{{ $heading->rowspan }}" align="center"  style="color: #ffffff; background-color: #366092; text-align: center; vertical-align: middle;"><b>{!! $heading->col_name !!}</b></th>
                @endforeach
            @else
                <th colspan="{{ $head_row->colspan }}" rowspan="{{ $head_row->rowspan }}" align="center"  style="color: #ffffff; background-color: #366092; text-align: center; vertical-align: middle;"><b>{!! $head_row->col_name !!}</b></th>
            @endif
        </tr>
    @endforeach

    @foreach($data_rows as $data_row)
        <tr>
            <td>{!! implode("</td><td>", $data_row) !!}</td>
        </tr>
    @endforeach
</table>

</body>
</html>