<div class="searchInput">
	<span class="oh">
		<i class="addressIcon"></i>
		<a class="pavilionBut" href="/pavilionsLocation/{{$ConfPavilion->id}}">{{$ConfPavilion->name}}</a>
		<i class="bottomIcon"></i>
	</span>
	<span style="border-left: 1px solid #eee;">
		<input class="searchBut" type="search" placeholder="输入您想搜索的内容..." onclick="window.location.href='{{url('/search')}}'">
	</span>
</div>