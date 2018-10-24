@extends('layout')
<style>
    .box{padding:20px;}
    .box ul li{color:#00CCFF;padding-bottom:10px;font-size:16px;}
</style>
@section("content")
<div class="box">
    <h4>数据报表</h4>
    <ul>
      <a href="{{url('/datareport/goodssale')}}" style="text-decoration:none"><li> 按商品销售额排名 </li></a>
      <a href="{{url('/datareport/salepercent')}}" style="text-decoration:none"><li> 按商品售后率排名 </li></a>
      <a href="{{url('/datareport/copnsale')}}" style="text-decoration:none"><li> 按地方馆销售额排名 </li></a>
      <a href="{{url('/datareport/addmem')}}" style="text-decoration:none"><li> 平台用户新增报表 </li></a>
      <a href="{{url('/datareport/guidessale')}}" style="text-decoration:none"><li> 导游按累计销售额排名 </li></a>
      <a href="{{url('/datareport/tasale')}}" style="text-decoration:none"><li> 旅行社按累计销售额排名 </li></a>
      <a href="{{url('/datareport/guidebandmem')}}" style="text-decoration:none"><li> 导游按绑定人数排名 </li></a>
      <a href="{{url('/datareport/tabandmem')}}" style="text-decoration:none"><li> 旅行社按绑定导游排名 </li></a>
      <a href="{{url('/datareport/guidebandh')}}" style="text-decoration:none"><li> 导游按绑定公众号人数排名 </li></a> 
    </ul>
</div>
@endsection














































