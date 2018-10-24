<html>
<body>
<span style="color:red">未审核导游信息</span>
<div>导游姓名:<span><?php echo $data->real_name?></span></div>
<div>手机号码:<span><?php echo $data->mobile ?></span></div>
<div>导游证卡号/身份证号:<span><?php echo $data->guide_no?></span></div>
<div>
    @if(!empty($data->guide_photo_1))
        <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}<?php echo $data->guide_photo_1?>">
    @endif
    @if(!empty($data->guide_photo_2))
        <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}<?php echo $data->guide_photo_2?>">
    @endif
</div>
<div>
    <a href="http://{{env('BOSS_DOMAIN')}}/guide/<?php echo $data->id?>/mail/audit">导游审核通过</a>
</div>

</body>
</html>