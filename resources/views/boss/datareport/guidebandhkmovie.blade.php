
@extends('layout')
@section("content")
<div class="page-container ml-20 mr-20">
    <div class="mb-10">
        <div style="height: 15px;"></div>
        <form method="get" action="/ta/tamanages">
            <span class="inline">日期范围：
                <input type="text" name="datemin" onfocus="WdatePicker({ dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}' })" value="" id="datemin" class="input-text Wdate" style="width:180px;">
            	-
            	<input type="text" name="datemax" onfocus="WdatePicker({ dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d' })" value="" id="datemax" class="input-text Wdate" style="width:180px;">
            </span>
            <span class="f-r mr-20">
	            <input type="submit" class="btn btn-success" id="" value="搜索" name="">
	            <span><a class="btn btn-success" href="" onclick="">导出</a></span>
            </span>
        </form>
    </div>
    <table class="table table-border table-bordered table-bg">
        <thead>
	        <tr>
	            <th style="font-size: 16px" scope="col" colspan="11">导游按绑定公众号人数排名</th>
	        </tr>
	        <tr class="text-c">
	        	<th>排名</th>
	            <th>导游姓名</th>
	            <th>绑定公众号人数</th>
	        </tr>
        </thead>
        <tbody>
	        <tr class="text-c">
	            <td>1</td>
	            <td>二锅头如果她</td>
	            <td>1999</td>
	        </tr>
	        <tr class="text-c">
	            <td>1</td>
	            <td>二锅头如果她</td>
	            <td>1999</td>
	        </tr>
	        <tr class="text-c">
	            <td>1</td>
	            <td>二锅头如果她</td>
	            <td>1999</td>
	        </tr>
        </tbody>
    </table>
</div>
@endsection
@section('javascript')
<script type="text/javascript">
	
</script>
@endsection