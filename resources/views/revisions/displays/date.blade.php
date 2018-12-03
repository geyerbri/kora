<span><b>{{$data['name']}}:</b>
@if($data['data']['circa'] != '')
    circa
@endif
@if($data['data']['month'] == 0 && $data['data']['day'] == 0)
    {{$data['data']['year']}}
@elseif ($data['data']['day'] == 0 && $data['data']['year'] == 0)
    {{DateTime::createFromFormat('m', $data['data']['month'])->format('F')}}
@elseif ($data['data']['day'] == 0)
    {{DateTime::createFromFormat('m', $data['data']['month'])->format('F').', '. $data['data']['year']}}
@elseif ($data['data']['year'] == 0)
    {{DateTime::createFromFormat('m', $data['data']['month'])->format('F').' '. $data['data']['day']}}
@elseif ($data['data']['format'] == 'MMDDYYYY')
    {{$data['data']['month'].'-'.$data['data']['day'].'-'.$data['data']['year']}}
@elseif ($data['data']['format'] == 'DDMMYYYY')
    {{$data['data']['day'].'-'.$data['data']['month'].'-'.$data['data']['year']}}
@elseif ($data['data']['format'] == 'YYYYMMDD')
    {{$data['data']['year'].'-'.$data['data']['month'].'-'.$data['data']['day']}}
@endif
@if($data['data']['era'] != '')
    {{$data['data']['era']}}
@endif
</span><br/>