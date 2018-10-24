<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

    </head>
    <body>
        <ul>
            商品管理-商品审核
            <li><a href="/goods/audit/0">待审核</a></li>
            <li><a href="/goods/audit/1">已通过</a></li>
            <li><a href="/goods/audit/3">已驳回</a></li>
        </ul>
        <table>
            <tr>
                <td>商品ID</td>
                <td >商品信息</td>
                <td>所属分馆</td>
                <td>所属分类</td>
                <td>提交时间</td>
                <td>审核状态</td>
                <td>操作</td>
            </tr>
            @if(isset($good))
            @if(count($good) > 0)
            @foreach ($good as $goods)
                <tr>
                    <td>{{ $goods->id }}</td>
                    <td >
                        <img src="{{$goods->cover}}" alt=" ">
                        {{ $goods->title }}
                        {{ $goods->price_buying }}
                    </td>
                    <td>{{ $goods->pavilion }}</td>
                    <td>{{ $goods->category_name }}</td>
                    <td>{{ $goods->created_at }}</td>
                    <td>
                        @if($good->state == 0)
                            待审核
                        @endif
                        @if($good->state == 1)
                            已通过
                            @endif
                            @if($good->state == 3)
                            已驳回
                            @endif
                    </td>
                    <td><a href="">查看</a> </td>
            </tr>
            @endforeach
            @endif
            <tr>

                <td><?php echo $good->render(); ?></td>

            </tr>
            <tr>
                <td> 每页显示5条</td>
            </tr>

            @endif
        </table>

    </body>
</html>
