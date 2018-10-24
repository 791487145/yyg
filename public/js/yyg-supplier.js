//导航
$(function () {
    // if($(".leftSide").height()<$(".rightCon").height()){
    // 	$(".leftSide").css("height",$(".rightCon").height()+"px");
    // }
    $(".nav li").click(function () {
        $(this).addClass("active").siblings("li").removeClass("active");
    });
    $(".nav>li").click(function () {
        $(this).children("ul").slideDown();
        $(this).siblings("li").children("ul").slideUp();
    });

//文字溢出

    $(".limitText").each(function () {
        var oldText = $(this).text();
        if (oldText.length > 24) {
            var newText = oldText.substring(0, 24) + "...";
            $(this).text(newText);
        }
    });


    $(".fileUpload input").change(function () {
        var file = $(this)[0].files;
        var result = $(this).parents(".fileResult")[0];

        for (i = 0; i < file.length; i++) {
            var reader = new FileReader();
            reader.readAsDataURL(file[i]);
            reader.onload = function (e) {
                //多图预览
                result.innerHTML = '<span><img src="' + this.result + '" alt="" /></span>' + result.innerHTML;
            }
        }
    });
    $(".oneFileResult input").change(function () {
        $(this).parent("label").remove()
    });
//批量导入
    $(".orderImportButton input[type=file]").change(function () {
        var filename = getFileName($(this).val());

        function getFileName(o) {
            var pos = o.lastIndexOf("\\");
            return o.substring(pos + 1);
        }

        $(this).parent().hide().next(".fileName").text(filename).show().next(".submit").show();
    });
//文本限制
    $(".textCount textarea").on("click keyup", function () {
    	var getNum = $(this).attr("data-num");
    	var maxNum = 60;
    	if(getNum){
    		maxNum = getNum;
    	}
        var $this = $(this),
            _val = $this.val(),
            count = "";
        if (_val.length > maxNum) {
            $this.val(_val.substring(0, maxNum));
        }
        count = $this.val().length;
        $(this).siblings(".textCountNum").children("i").text(count);
    });
//复制table
    $(".addTable").click(function () {
        var timestamp = Date.parse(new Date());
        var table = '<span class="tab-del" id="tab-'+timestamp+'"></span><table>';
        table += $(this).parent().siblings("table").first().html();
        table += '</table>';
        $(this).parent().before(table);
        express_handle();
        pick_handle();
        $('#tab-'+timestamp).next('table').find('.stock i').text('0');
        $('#tab-'+timestamp).next('table').find('input').each(function(e){
       		$(this).val("");
        });
        $('#tab-'+timestamp).click(function(){
            $(this).next().remove();
            $(this).remove();
        });
    });
//tab
    $(".statusTab span").click(function () {
        $(this).addClass("active").siblings().removeClass("active");
        $(".tabBox>div").eq($(this).index()).addClass("active").siblings().removeClass();
    });
//allcheck
    $(".allCheck").click(function () {
        if ($(this).is(":checked")) {
            $(this).parents("table").find("input[type='checkbox']").prop("checked", true);
        } else {
            $(this).parents("table").find("input[type='checkbox']").prop("checked", false);
        }
    });
    $("input[type='checkbox']:not(.allCheck)").click(function () {
        $(this).parents("table").find(".allCheck").prop("checked", false);
    });
//popup
    $(".popupShow").click(function () {
        $(".popupWrap").css("margin-top", -$(".popupWrap").height() / 2 + "px");
        $(".popupBg,.popupWrap").show();
    });
    $(".popupBg,.popupWrap .buttonGroup .cancel").click(function(){
        $(".popupBg,.popupWrap").hide();
    });
});
function popupConfirm(box) {
    $(".popupBg").show();
    $("." + box).show().css("margin-top", -$(".popupWrap").height() / 2 + "px");
}