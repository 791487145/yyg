@extends('layout_pop')
@section('content')
    <div style="margin: 20px 40px">

    @foreach($data as $v)
        <div>{{$v['created_at']}}，规格：{{$v['spec_name']}},价格从{{$v['before']}}调整至{{$v['after']}}，操作人：{{$v['user']}}；</div>
    @endforeach
    </div>
@endsection