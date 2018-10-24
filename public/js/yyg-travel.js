//导航
$(function () {

    $(".nav li").click(function () {
        $(this).addClass("active").siblings("li").removeClass("active");
    });
    $(".nav>li").click(function () {
        $(this).children("ul").slideDown();
        $(this).siblings("li").children("ul").slideUp();
    });
    $(".showSecondTable").click(function () {
        if ($(this).hasClass("on")) {
            $(this).removeClass("on").parents("tr").next(".secondTable").hide();
        } else {
            $(this).addClass("on").parents("tr").next(".secondTable").show();
        }
    });
    //文字溢出
    $(".limitText").each(function () {
        var oldText = $(this).text();
        if (oldText.length > 12) {
            var newText = oldText.substring(0, 12) + "...";
            $(this).text(newText);
        }
    });
    //$(".fileUpload input").change(function () {
    //    var file = $(this)[0].files;
    //    var result = $(this).parents(".fileResult")[0];
    //
    //    for (i = 0; i < file.length; i++) {
    //        var reader = new FileReader();
    //        reader.readAsDataURL(file[i]);
    //        reader.onload = function (e) {
    //            //多图预览
    //            result.innerHTML = '<span><img src="' + this.result + '" alt="" /></span>' + result.innerHTML;
    //        }
    //    }
    //});
    //$(".oneFileResult input").change(function () {
    //    $(this).parents("span").remove()
    //})
//二级表格

//批量导入
    $(".orderImportButton input[type=file]").change(function () {
        var filename = getFileName($(this).val());
        function getFileName(o) {
            var pos = o.lastIndexOf("\\");
            return o.substring(pos + 1);
        }
        $(this).parents(".upLoad").hide().next().show().parent(".orderImportButton").prepend('<div class="fileName">' + filename + '</div><div class="fileInfo"><label>旅行团名称：<input type="text"></label><label>指派导游：<select><option>李二狗</option><option>王二狗（未激活）</option></select></label></div>');
    });

//tab
    $(".statusTab span").click(function () {
        $(this).addClass("active").siblings().removeClass("active");
        $(".tabBox>div").eq($(this).index()).addClass("active").siblings().removeClass();
    })

//popup
    $(".popupShow").click(function () {
        $(".popupWrap").css("margin-top", -$(".popupWrap").height() / 2 + "px");
        $(".popupBg,.popupWrap").show();
    })

    $(".popupBg,.popupWrap .buttonGroup .cancel,.popupWrap .buttonGroup .close").click(function () {
        $(".popupBg,.popupWrap").hide();
    })
})
function popupConfirm(box) {
    $(".popupBg,.popupWrap").hide();
    $(".popupBg").show();
    $("." + box).css("margin-top", -$("." + box).height() / 2 + "px").show();
}