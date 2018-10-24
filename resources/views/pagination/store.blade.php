<div class="pageLink">
    <ul>
        <li class={{ ($paginator->currentPage() == 1) ? ' disabled' : '' }}><a href="{{ $paginator->url(1) }}">上一页</a></li>
        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
        <li><a href="{{ $paginator->url($i) }}" class={{ ($paginator->currentPage() == $i) ? ' active' : '' }}>{{ $i }}</a></li>
        @endfor
        <li class={{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : '' }}><a href="{{ $paginator->url($paginator->currentPage()+1) }}">下一页</a></li>
    </ul>
</div>