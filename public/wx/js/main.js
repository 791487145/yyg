//search
$(".rankButton span").click(function(){
    $(this).addClass("active").siblings().removeClass("active")
})
$(".searchList li").click(function(){
    $(".searchInput .search input").val($(this).text());
})
//文字溢出
$(function(){
    $(".goodsBox .description,.orderGoods .name").each(function(){
        var oldText = $(this).text();  
        if (oldText.length > 20) {  
            var newText = oldText.substring(0,20)+"...";  
            $(this).text(newText);  
        }       
    })
})
//提示弹窗
function information(info){
	$('#add').text(info);
	$(".addCartTip").stop().fadeIn();
	setTimeout(function(){$(".addCartTip").fadeOut()},1000)
}
//弹窗
function alertPopup(obj,popupDone){
	if(typeof obj !== "object"){
		obj={};
	}
	var title = obj.title?obj.title:"标题";
	var conten = obj.conten?obj.conten:"内容";
	var popup = '<div class="popupBox">'+
					'<div class="popupConten">'+
						'<div class="popupTitle">'+title+'</div>'+
						'<div class="popupInfo">'+conten+'</div>'+
						'<div class="popupButton">'+
					        '<button class="popupCancel">取消</button>'+
					        '<span></span>'+
					        '<button class="popupDone">确定</button>'+
					    '</div>'+
					'</div>'+
				'</div>'
	$(".popupBox").remove();
	$("body").append(popup);
	$(".popupCancel").click(function(){
		$(".popupBox").remove();
	});
	$(".popupDone").click(function(){
		$(".popupBox").remove();
		if(typeof popupDone == "function"){
			popupDone();
		}
	});
}
//alertPopup({"title":"title","conten":"conten"},function(){
//	alert("333");
//});


$(".imgPopup img").click(function(e){
	var imgList = '';
	var thisBox = $(this).parent(".imgPopup").find("img");
	thisBox.each(function(e){
		var src = thisBox.eq(e).attr("src");
		imgList += '<div class="swiper-slide"><img src="'+src+'"/></div>'
	})
	var imgPopup =  '<div id="imgPopup"><span class="close">x</span>'+
						'<div class="swiper-wrapper">'+imgList+'</div>'+
						'<div class="pagination"></div>'+
					'</div>';
					
	$("#imgPopup").remove();
	$("body").append(imgPopup);
	$("#imgPopup").fadeIn();
	$("#imgPopup .close").click(function(){
		$("#imgPopup").fadeOut();
	})
	//启动滑动
	var index = parseInt($(this).attr("data-index"));
	var swiperPopup = new Swiper('#imgPopup',{
	    loop: true,
	    initialSlide :index,
	    pagination: '#imgPopup .pagination',
	    paginationClickable: false
	})
})

//加入购物车
function cartNum(num){
	$(".footNav .shopCartButton b").show()
	$(".footNav .shopCartButton b").text(num);
}
//tab
$(function(){
	var w = $(".classifyHead .tabButton li").outerWidth(),
	len = $(".classifyHead .tabButton li").length;

	$(".classifyHead .tabButton ul").css("width",w*len+6+"px")
})
$(".tabButton li").click(function(){
	$(this).addClass("active").siblings().removeClass("active");
	$(".tabBoxGroup .tabBox").eq($(this).index()).addClass("active").siblings().removeClass("active");
})
$(".goodsDetail .favor").click(function(){
	if($(this).hasClass("favored")){
		$(this).removeClass("favored").children("a").text("收藏")
	}else{
		$(this).addClass("favored").children("a").text("已收藏")
	}
})
//放大图
$(".goodsDetail .banner").click(function(){
	if ($(this).hasClass("active")) {
		$(".goodsDetail .banner").css({"margin-top":"0","top":"0"}).removeClass("active");
		$(".bannerShade").remove();
	}else{
		$("body").append("<div class='bannerShade'></div>")
		$(this).addClass("active").animate({"top":"50%","margin-top":-$(this).height()/2+"px"
		});
	}
	$(".bannerShade").click(function(){
		$(".goodsDetail .banner").css({"margin-top":"0","top":"0"}).removeClass("active");
		$(this).remove();
	})
})
//popup
function popupShow(box){
	popupHide();
	$(".popupBg,.popupWrap").hide();
	$(".popupBg").show();
	$("."+box).css("margin-top",-$("."+box).height()/2+"px").show();
}


popupShows("customeServiceS")
function popupShows(box){
	$(".popupBg,.popupWrap").hide();
	$(".popupBgs").show();
	$("."+box).css("margin-top",-$("."+box).height()/2+"px").show();
}
function popupHide(){
	$(".popupBgs,.popupWraps").hide();
}
function bottomPopupShow(box){
	$(".popupBg").show();
	$("."+box).slideDown();
}
$(function(){
	$(".popupWrap").each(function(){
		$(this).css("margin-top",-$(this).height()/2)
	})
	$(".popupBg,.popupWrap .close,.bottomPopup .close").click(function(){
		$(".popupBg,.popupWrap").hide();
		$(".bottomPopup").slideUp();
	})
})
//加减
$(".amountInput .add").click(function(){
	var n=$(this).siblings(".number").val();
	var num=parseInt(n)+1;
	$(this).siblings(".number").val(num);
	// if(num>2){
	// 	$(this).siblings(".minus").removeClass("disabled");
	// }
});
$(".amountInput .minus").click(function(){
	var n=$(this).siblings(".number").val();
	var num=parseInt(n)-1;
	if(num<=1) num=1;
	$(this).siblings(".number").val(num);
	// if(num<=2){
	// 	$(this).addClass("disabled");
	// 	$(this).siblings(".number").val("2");						
	// }else{
	// 	$(this).siblings(".number").val(num);			
	// }
});
//删除
$(".addressList .delete").click(function(){
	$(this).parents(".box").remove()
})
//allcheck
$(".allCheck").click(function(){
	if($(this).is(":checked")){
		$("body").find("input[type='checkbox']").prop("checked",true);
	}else{
		$("body").find("input[type='checkbox']").prop("checked",false);
	}
})

$("input[type='checkbox']:not(.allCheck)").click(function(){
	$(".allCheck").prop("checked",false);
})

$(".allChildCheck").click(function(){
	if($(this).is(":checked")){
		$(this).parents(".orderGroup").find("input[type='checkbox']").prop("checked",true);
	}else{
		$(this).parents(".orderGroup").find("input[type='checkbox']").prop("checked",false);
	}
})
$("input[type='checkbox']:not(.allChildCheck)").click(function(){
	$(this).parents(".orderGroup").find(".allChildCheck").prop("checked",false);
})
//购物车编辑
$(".allOrderTop .edit").click(function(){
	if($(this).hasClass("on")){
		$(this).removeClass("on").val("编辑");
	}else{
		$(this).addClass("on").val("完成");	
	}
	$(".cartFoot").toggle();
})
//文本限制
$(".textCount textarea").on("click keyup", function() {  
    var $this = $(this),  
        _val = $this.val(),  
        count = "";  
    if (_val.length > 120) {  
        $this.val(_val.substring(0, 120));  
    }  
    count = $this.val().length;  
    $(this).siblings(".textCountNum").children("i").text(count);  
});
//多图预览
/*$(".fileUpload input").change(function(){
    var file = $(this)[0].files;
    var result = $(this).parents(".fileResult");
    var spanLen = $(this).parents(".fileResult").children("span").length;

    var reader = new FileReader();
    reader.readAsDataURL(file[0]);
	reader.onload = function(e){

	}
    if(spanLen>=5) {
    	$(".fileUpload").hide();
    }
});*/
setImg();
function setImg(){
	var width = $(".commentBox .commentImgBox img").width();
	$(".commentBox .commentImgBox img").height(width);
}

//获取url中的参数
function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg);  //匹配目标参数
    if (r != null) return decodeURI(r[2]); return null; //返回参数值
}

function localStorages(type,name,value){
	if(type == "add"){
//		localStorage.setItem(name, JSON.stringify(value));
		localStorage.setItem(name,value);
		return false;
	}else if(type == "remove"){
		localStorage.removeItem(name);
		return false;
	}else{
		var getValue =  JSON.parse(localStorage.getItem(name));
		return getValue;
	}
}






