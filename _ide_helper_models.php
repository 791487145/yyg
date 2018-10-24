<?php
/**
 * An helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\AppVersion
 *
 * @property integer $id
 * @property integer $uid 操作人id
 * @property string $name 名称ios/android
 * @property string $version 版本号
 * @property string $url 地址
 * @property string $content 更新说明
 * @property boolean $is_force 是否强制升级 1为强制更新
 * @property boolean $is_selected 0不是默认版本 1 是默认版本
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereVersion($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereIsForce($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereIsSelected($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class AppVersion extends \Eloquent {}
}

namespace App\Models{
/**
 * Class ConfBank
 *
 * @property integer $id
 * @property integer $display_order
 * @property string $name
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBank whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBank whereDisplayOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBank whereName($value)
 * @mixin \Eloquent
 */
	class ConfBank extends \Eloquent {}
}

namespace App\Models{
/**
 * Class ConfBanner
 *
 * @property integer $id
 * @property integer $pavilion_id
 * @property string $name
 * @property string $cover
 * @property integer $display_order
 * @property string $url
 * @property string $start_time
 * @property string $end_time
 * @property string $state
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner wherePavilionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereCover($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereDisplayOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereStartTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereEndTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property boolean $url_type 0.URL, 1商品ID
 * @property string $url_content
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereUrlType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereUrlContent($value)
 */
	class ConfBanner extends \Eloquent {}
}

namespace App\Models{
/**
 * Class ConfCategory
 *
 * @property integer $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCategory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCategory whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property boolean $display_order
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCategory whereDisplayOrder($value)
 */
	class ConfCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * Class ConfCity
 *
 * @property integer $id
 * @property string $name
 * @property string $zip_code
 * @property string $path
 * @property integer $parent_id
 * @property string $created
 * @property boolean $state
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCity whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCity whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCity whereZipCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCity wherePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCity whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCity whereCreated($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCity whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCity whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class ConfCity extends \Eloquent {}
}

namespace App\Models{
/**
 * Class ConfExpress
 *
 * @property integer $id
 * @property string $tel
 * @property string $name
 * @property integer $order_sort
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfExpress whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfExpress whereTel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfExpress whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfExpress whereOrderSort($value)
 * @mixin \Eloquent
 */
	class ConfExpress extends \Eloquent {}
}

namespace App\Models{
/**
 * Class ConfHotWord
 *
 * @property integer $id
 * @property string $name
 * @property integer $url
 * @property integer $display_order
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfHotWord whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfHotWord whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfHotWord whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfHotWord whereDisplayOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfHotWord whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfHotWord whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class ConfHotWord extends \Eloquent {}
}

namespace App\Models{
/**
 * Class ConfPavilion
 *
 * @property integer $id
 * @property string $name
 * @property string $cover
 * @property string $background
 * @property integer $display_order
 * @property integer $state
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon  $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilion whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilion whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilion whereCover($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilion whereBackground($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilion whereDisplayOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilion whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilion whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class ConfPavilion extends \Eloquent {}
}

namespace App\Models{
/**
 * Class ConfPavilionTag
 *
 * @property integer $id
 * @property integer $pavilion_id
 * @property string $name
 * @property integer $goods_id
 * @property integer $display_order
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilionTag whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilionTag wherePavilionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilionTag whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilionTag whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilionTag whereDisplayOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilionTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilionTag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class ConfPavilionTag extends \Eloquent {}
}

namespace App\Models{
/**
 * Class ConfTheme
 *
 * @property integer $id
 * @property integer $pavilion_id
 * @property integer $display_order
 * @property string $name
 * @property string $url
 * @property string $cover
 * @property \Carbon\Carbon $created_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme wherePavilionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereDisplayOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereCover($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereCreatedAt($value)
 * @mixin \Eloquent
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereUpdatedAt($value)
 * @property boolean $state
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereState($value)
 * @property boolean $url_type
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereUrlType($value)
 */
	class ConfTheme extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Device
 *
 * @property integer $id
 * @property integer $uid
 * @property string $app_name
 * @property string $app_version
 * @property string $device_token
 * @property boolean $push_badge 1接收，-1禁止
 * @property boolean $push_alert 1接收，-1禁止
 * @property boolean $push_sound 1接收，-1禁止
 * @property boolean $status 1接收，-1禁止
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device whereAppName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device whereAppVersion($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device whereDeviceToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device wherePushBadge($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device wherePushAlert($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device wherePushSound($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Device extends \Eloquent {}
}

namespace App\Models{
/**
 * Class GoodsBase
 *
 * @property integer $id
 * @property string $title
 * @property integer $supplier_id 供应商ID
 * @property integer $category_id 品类id
 * @property integer $pavilion 所属馆
 * @property integer $location
 * @property integer $location_order
 * @property string $cover 封面图
 * @property boolean $state -1删除 0未审核 1.上架 2下架 3驳回 4售罄
 * @property integer $num 库存数量
 * @property integer $num_sold 已销数量
 * @property integer $num_browse 浏览量
 * @property float $guide_amount 导游反复金额
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereSupplierId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase wherePavilion($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereCover($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereNumSold($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereLocation($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereLocationOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereNumBrowse($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereGuideAmount($value)
 * @property integer $num_favorite 收藏数量
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereNumFavorite($value)
 * @mixin \Eloquent
 */
	class GoodsBase extends \Eloquent {}
}

namespace App\Models{
/**
 * Class GoodsCategory
 *
 * @property integer $id
 * @property integer $goods_id
 * @property integer $category_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsCategory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsCategory whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class GoodsCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * Class GoodsExt
 *
 * @property integer $id
 * @property integer $goods_id
 * @property string $important_tips 重要提示
 * @property string $send_out_address 发货地
 * @property string $send_out_desc 发货说明
 * @property string $product_area 产地
 * @property string $shelf_life 保质期
 * @property string $pack 包装
 * @property string $store 贮藏
 * @property string $express_desc 快递描述
 * @property string $sold_desc 售后说明
 * @property string $level 等级
 * @property string $product_license 产品许可证
 * @property string $company 公司
 * @property string $dealer 经销商
 * @property string $food_addiitive 食品添加剂
 * @property string $food_burden 食品配料表
 * @property string $address 地址
 * @property string $remark 备注
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereImportantTips($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereSendOutAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereSendOutDesc($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereProductArea($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereShelfLife($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt wherePack($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereStore($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereExpressDesc($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereSoldDesc($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereLevel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereProductLicense($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereCompany($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereDealer($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereFoodAddiitive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereFoodBurden($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $description 详情
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereDescription($value)
 */
	class GoodsExt extends \Eloquent {}
}

namespace App\Models{
/**
 * Class GoodsGift
 *
 * @property integer $id
 * @property integer $goods_id
 * @property integer $gift_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsGift whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsGift whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsGift whereGiftId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsGift whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsGift whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property integer $spec_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsGift whereSpecId($value)
 */
	class GoodsGift extends \Eloquent {}
}

namespace App\Models{
/**
 * Class GoodsImage
 *
 * @property integer $id
 * @property integer $goods_id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsImage whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsImage whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsImage whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class GoodsImage extends \Eloquent {}
}

namespace App\Models{
/**
 * Class GoodsMaterialBase
 *
 * @property integer $id
 * @property integer $goods_id
 * @property string $content
 * @property integer $like_num
 * @property integer $forward_num
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialBase whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialBase whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialBase whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialBase whereLikeNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialBase whereForwardNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialBase whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialBase whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class GoodsMaterialBase extends \Eloquent {}
}

namespace App\Models{
/**
 * Class GoodsMaterialImage
 *
 * @property integer $id
 * @property integer $material_id
 * @property string $image_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialImage whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialImage whereMaterialId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialImage whereImageName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class GoodsMaterialImage extends \Eloquent {}
}

namespace App\Models{
/**
 * Class GoodsOptLog
 *
 * @property integer $id
 * @property boolean $type 1上架 -1删除 2下架  3添加库存
 * @property integer $uid 操作者id
 * @property integer $gid 商品id
 * @property string $content json
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsOptLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsOptLog whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsOptLog whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsOptLog whereGid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsOptLog whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsOptLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsOptLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class GoodsOptLog extends \Eloquent {}
}

namespace App\Models{
/**
 * Class GoodsSpec
 *
 * @property integer $id
 * @property integer $goods_id
 * @property string $name
 * @property string $pack_num 包装数
 * @property integer $num 数量
 * @property integer $num_sold 已售数量
 * @property float $weight 重量
 * @property float $weight_net 净重
 * @property string $long 长
 * @property string $wide 宽
 * @property string $height 高
 * @property float $price 市场价
 * @property float $price_buying 进价
 * @property float $platform_fee 平台服务费
 * @property float $guide_rate 导游分成
 * @property float $travel_agency_rate 旅行社分成
 * @property boolean $express_fee_mode 1包邮 2设置邮费
 * @property boolean $is_pick_up 1支持自提 2不支持自提
 * @property boolean $state
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec wherePackNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereNumSold($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereWeight($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereWeightNet($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereLong($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereWide($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereHeight($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec wherePriceBuying($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec wherePlatformFee($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereGuideRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereTravelAgencyRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereExpressFeeMode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereIsPickUp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class GoodsSpec extends \Eloquent {}
}

namespace App\Models{
/**
 * Class GuideAudit
 *
 * @property integer $id
 * @property integer $uid
 * @property string $active
 * @property string $content
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideAudit whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideAudit whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideAudit whereActive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideAudit whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideAudit whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideAudit whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class GuideAudit extends \Eloquent {}
}

namespace App\Models{
/**
 * Class GuideBase
 *
 * @property integer $id
 * @property integer $uid user_base id
 * @property integer $ta_id 旅行社id
 * @property integer $sale_id 销售id
 * @property string $avatar 用户头像
 * @property string $store_cover 店铺封面
 * @property string $invite_code 邀请码
 * @property string $real_name
 * @property string $withdraw_name 提现姓名
 * @property string $withdraw_bank 提现银行
 * @property string $withdraw_sub_bank 提现分行
 * @property string $withdraw_card_number 提现银行卡
 * @property string $guide_no 导游证卡号
 * @property string $guide_photo_1
 * @property string $guide_photo_2
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereTaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereSaleId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereAvatar($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereStoreCover($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereInviteCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereRealName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereWithdrawName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereWithdrawBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereWithdrawSubBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereWithdrawCardNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereGuideNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereGuidePhoto1($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereGuidePhoto2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class GuideBase extends \Eloquent {}
}

namespace App\Models{
/**
 * Class GuideBilling
 *
 * @property integer $id
 * @property integer $guide_id
 * @property integer $uid
 * @property string $order_no
 * @property string $trade_no
 * @property boolean $in_out 1入账，2出账
 * @property float $amount 金额
 * @property float $return_amount 实际金额
 * @property float $balance 余额
 * @property string $content
 * @property boolean $state -1删除，0待入账，1成功，2失败 11提现审核中   12提现待打款    13提现已打款   14提现已驳回
 * @property string $remark
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereTradeNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereInOut($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereReturnAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereBalance($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class GuideBilling extends \Eloquent {}
}

namespace App\Models{
/**
 * Class GuideStoreGood
 *
 * @property integer $id
 * @property integer $guide_id
 * @property integer $goods_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideStoreGood whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideStoreGood whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideStoreGood whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideStoreGood whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideStoreGood whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class GuideStoreGood extends \Eloquent {}
}

namespace App\Models{
/**
 * Class GuideTum
 *
 * @property integer $id
 * @property integer $guide_id
 * @property integer $ta_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideTum whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideTum whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideTum whereTaId($value)
 */
	class GuideTum extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Migration
 *
 * @mixin \Eloquent
 * @property string $migration
 * @property integer $batch
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Migration whereMigration($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Migration whereBatch($value)
 */
	class Migration extends \Eloquent {}
}

namespace App\Models{
/**
 * Class OrderBase
 *
 * @property integer $id
 * @property integer $uid
 * @property string $supplier_id
 * @property string $order_no
 * @property float $amount_goods
 * @property float $amount_express
 * @property boolean $pay_type
 * @property string $pay_info
 * @property string $express_type
 * @property string $express_name
 * @property string $express_no
 * @property string $express_time
 * @property string $receiver_info
 * @property integer $ta_id
 * @property float $ta_amount
 * @property integer $guide_id
 * @property float $guide_amount
 * @property string $buyer_message
 * @property boolean $state -1.删除订单, 0.待付款,1.待发货 2.待收货 5.已完成，11系统超时取消, 12用户主动取消, 13.客服关闭订单（缺货客服关闭，退钱
 * @property string $remark
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereSupplier_id($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereAmountGoods($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereAmountExpress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase wherePayType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase wherePayInfo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereReceiverInfo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereTaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereTaAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereAmountReal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereExpressType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereExpressName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereExpressNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereExpressTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereReceiverName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereReceiverMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereHasGift($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereGuideAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereBuyerMessage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereSupplierId($value)
 * @property integer //$uid 用户id
 * @property integer //$uid 用户id
 * @property float $amount_real
 * @property string $receiver_name 收货人姓名
 * @property string $receiver_mobile 收货人手机
 * @property boolean $has_gift 是否有赠品
 * @mixin \Eloquent
 */
	class OrderBase extends \Eloquent {}
}

namespace App\Models{
/**
 * Class OrderGood
 *
 * @property integer $id
 * @property string $order_no
 * @property integer $goods_id
 * @property integer $spec_id
 * @property boolean $is_gift
 * @property integer $price 价格
 * @property integer $num 数量
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereSpecId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereIsGift($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $remark json  商品信息
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereRemark($value)
 */
	class OrderGood extends \Eloquent {}
}

namespace App\Models{
/**
 * Class OrderLog
 *
 * @property integer $id
 * @property string $order_no 订单编号
 * @property integer $uid 操作者id
 * @property string $action 操作描述
 * @property string $content json
 * @property \Carbon\Carbon $created_at 建立时间
 * @property \Carbon\Carbon $updated_at 修改时间
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereAction($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class OrderLog extends \Eloquent {}
}

namespace App\Models{
/**
 * Class OrderPay
 *
 * @property integer $id
 * @property string $order_no
 * @property string $pay_info
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderPay whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderPay whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderPay wherePayInfo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderPay whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderPay whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class OrderPay extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrderReturn
 *
 * @property integer $id
 * @property integer $uid 用户id
 * @property integer $supplier_id 供应商ID
 * @property string $receiver_name
 * @property string $receiver_mobile
 * @property string $order_no 订单编号
 * @property float $amount
 * @property string $return_no 退单编号
 * @property string $return_content 退款说明
 * @property boolean $state 0.待审核 1.审核通过待退款 4.审核驳回 3成功退款
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereSupplierId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereReceiverName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereReceiverMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereReturnNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereReturnContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class OrderReturn extends \Eloquent {}
}

namespace App\Models{
/**
 * Class OrderReturnImage
 *
 * @property integer $id
 * @property integer $return_id
 * @property string $name 图片名
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturnImage whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturnImage whereReturnId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturnImage whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturnImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturnImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class OrderReturnImage extends \Eloquent {}
}

namespace App\Models{
/**
 * Class OrderReturnLog
 *
 * @property integer $id
 * @property string $return_no 退单编号
 * @property integer $uid 操作者id
 * @property string $action 操作描述
 * @property string $content json
 * @property \Carbon\Carbon $created_at 建立时间
 * @property \Carbon\Carbon $updated_at 修改时间
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereReturnNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereAction($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $order_no 订单编号
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturnLog whereOrderNo($value)
 */
	class OrderReturnLog extends \Eloquent {}
}

namespace App\Models{
/**
 * Class OrderWx
 *
 * @property integer $id
 * @property integer $uid
 * @property string $order_sn
 * @property string $order_no
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderWx whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderWx whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderWx whereOrderSn($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderWx whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderWx whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderWx whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class OrderWx extends \Eloquent {}
}

namespace App\Models{
/**
 * Class PasswordReset
 *
 * @mixin \Eloquent
 * @property string $email
 * @property string $token
 * @property \Carbon\Carbon $created_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PasswordReset whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PasswordReset whereToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PasswordReset whereCreatedAt($value)
 */
	class PasswordReset extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Permission
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $parent_id 父级id
 * @property boolean $is_menu 为1时是菜单
 * @property string $icon 图标
 * @property string $name 唯一名称即：路由
 * @property string $display_name 显示名称
 * @property string $description 描述
 * @property integer $display_order 排序
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereIsMenu($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereIcon($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereDisplayName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereDisplayOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereUpdatedAt($value)
 */
	class Permission extends \Eloquent {}
}

namespace App\Models{
/**
 * Class PermissionRole
 *
 * @mixin \Eloquent
 * @property integer $permission_id
 * @property integer $role_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PermissionRole wherePermissionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PermissionRole whereRoleId($value)
 */
	class PermissionRole extends \Eloquent {}
}

namespace App\Models{
/**
 * Class PlatformBilling
 *
 * @property integer $id
 * @property integer $guide_id
 * @property integer $uid
 * @property string $order_no
 * @property string $trade_no
 * @property boolean $in_out 1入账，2出账
 * @property float $amount 金额
 * @property float $return_amount 退款金额
 * @property float $balance 余额
 * @property string $content
 * @property boolean $state -1删除，0待入账，1成功，2失败
 * @property string $remark
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereTradeNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereInOut($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereReturnAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereBalance($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class PlatformBilling extends \Eloquent {}
}

namespace App\Models{
/**
 * Class PlatformNews
 *
 * @property integer $id
 * @property string $title
 * @property string $url
 * @property string $cover
 * @property string $content
 * @property boolean $state 0未发，1已发
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformNews whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformNews whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformNews whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformNews whereCover($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformNews whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformNews whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformNews whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformNews whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $send_time
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformNews whereSendTime($value)
 */
	class PlatformNews extends \Eloquent {}
}

namespace App\Models{
/**
 * Class PlatformSm
 *
 * @property integer $id
 * @property boolean $type 1.商品审核 2.旅行社审核，3.导游审核，4.供应商补交保证金，5.售后订单审核,51.售后打款,6.提现审核，61.提现打款
 * @property string $mobile
 * @property string $code
 * @property boolean $is_valid -1过期无效，0有效
 * @property string $sid 短信运营商的id
 * @property string $ip
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformSm whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformSm whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformSm whereMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformSm whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformSm whereIsValid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformSm whereSid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformSm whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformSm whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformSm whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class PlatformSm extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Role
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $perms
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @mixin \Eloquent
 * @property integer $id
 * @property string $name 角色名
 * @property string $display_name 角色昵称
 * @property string $description 角色描述
 * @property boolean $level 角色等级
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Role whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Role whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Role whereDisplayName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Role whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Role whereLevel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * Class RoleUser
 *
 * @mixin \Eloquent
 */
	class RoleUser extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Session
 *
 * @property integer $id
 * @property string $payload
 * @property integer $last_activity
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Session whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Session wherePayload($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Session whereLastActivity($value)
 * @mixin \Eloquent
 */
	class Session extends \Eloquent {}
}

namespace App\Models{
/**
 * Class SmsVerificationCode
 *
 * @property integer $id
 * @property boolean $type 1.注册，2.忘记密码
 * @property string $mobile
 * @property string $code
 * @property boolean $is_valid -1过期无效，0有效
 * @property string $sid 短信运营商的id
 * @property integer $ip
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SmsVerificationCode whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SmsVerificationCode whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SmsVerificationCode whereMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SmsVerificationCode whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SmsVerificationCode whereIsValid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SmsVerificationCode whereSid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SmsVerificationCode whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SmsVerificationCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SmsVerificationCode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class SmsVerificationCode extends \Eloquent {}
}

namespace App\Models{
/**
 * Class SupplierBase
 *
 * @property integer $id
 * @property string $name
 * @property string $card_id
 * @property string $mobile
 * @property string $salt 盐
 * @property string $password
 * @property string $avatar
 * @property integer $province_id 省
 * @property integer $city_id 市
 * @property float $deposit 保证金
 * @property float $amount 金额
 * @property float $freeze_amount 冻结金额
 * @property string $store_name 供应商名称
 * @property string $store_logo 供应商LOGO
 * @property integer $store_province_id
 * @property integer $store_city_id
 * @property string $withdraw_name 提现姓名
 * @property string $withdraw_bank 提现银行
 * @property string $withdraw_sub_bank 提现分行
 * @property string $withdraw_card_number 提现银行卡
 * @property string $remark 备注
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereCardId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereSalt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereAvatar($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereProvinceId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereCityId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereDeposit($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereFreezeAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereStoreName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereStoreLogo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereStoreProvinceId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereStoreCityId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereWithdrawName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereWithdrawBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereWithdrawSubBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereWithdrawCardNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereRemark($value)
 *  * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property boolean $state 状态:1正常 -1禁用
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase ($value)
 */
	class SupplierBase extends \Eloquent {}
}

namespace App\Models{
/**
 * Class SupplierBilling
 *
 * @property integer $id
 * @property integer $supplier_id
 * @property string $order_no
 * @property string $trade_no
 * @property boolean $in_out 1入账，2出账
 * @property float $amount 金额
 * @property float $balance 余额
 * @property string $content
 * @property boolean $state -1删除，0待入账，1成功，2失败
 * @property string $remark
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereSupplierId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereTradeNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereInOut($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereBalance($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property float $return_amount 退款金额
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereReturnAmount($value)
 */
	class SupplierBilling extends \Eloquent {}
}

namespace App\Models{
/**
 * Class SupplierSm
 *
 * @property integer $id
 * @property boolean $type 2.忘记密码. 3修改密码
 * @property string $mobile
 * @property string $code
 * @property boolean $is_valid -1过期无效，0有效
 * @property string $sid 短信运营商的id
 * @property integer $ip
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierSm whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierSm whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierSm whereMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierSm whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierSm whereIsValid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierSm whereSid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierSm whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierSm whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierSm whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class SupplierSm extends \Eloquent {}
}

namespace App\Models{
/**
 * Class SupplierWithdraw
 *
 * @property integer $id
 * @property integer $uid
 * @property string $withdraw_name
 * @property string $withdraw_bank
 * @property string $withdraw_sub_bank
 * @property string $withdraw_card_number
 * @property float $amount
 * @property float $balance
 * @property boolean $state 0未审核，1已审通过，2已打款，4已驳回
 * @property string $remark
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereWithdrawName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereWithdrawBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereWithdrawSubBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereWithdrawCardNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereBalance($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class SupplierWithdraw extends \Eloquent {}
}

namespace App\Models{
/**
 * Class TaAudit
 *
 * @property integer $id
 * @property integer $uid
 * @property string $active
 * @property string $content
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaAudit whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaAudit whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaAudit whereActive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaAudit whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaAudit whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaAudit whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class TaAudit extends \Eloquent {}
}

namespace App\Models{
/**
 * Class TaBase
 *
 * @property integer $id
 * @property string $mobile
 * @property string $salt
 * @property string $password
 * @property string $ta_name
 * @property string $ta_logo
 * @property integer $ta_province_id
 * @property integer $ta_city_id
 * @property string $withdraw_name 提现姓名
 * @property string $withdraw_bank 提现银行
 * @property string $withdraw_sub_bank 提现分行
 * @property string $withdraw_card_number 提现银行卡
 * @property string $opt_name
 * @property string $opt_mobile
 * @property string $opt_id_card
 * @property string $opt_photo_1
 * @property string $opt_photo_2
 * @property integer $sale_id 销售id
 * @property string $invite_code 邀请码
 * @property float $amount 金额
 * @property float $freeze_amount 冻结金额
 * @property boolean $state -1删除，0未审，1正常，4关停
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereSalt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereTaName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereTaLogo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereTaProvinceId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereTaCityId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereWithdrawName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereWithdrawBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereWithdrawSubBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereWithdrawCardNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereOptName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereOptMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereOptIdCard($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereOptPhoto1($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereOptPhoto2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereSaleId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereInviteCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereFreezeAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class TaBase extends \Eloquent {}
}

namespace App\Models{
/**
 * Class TaBilling
 *
 * @property integer $id
 * @property integer $ta_id
 * @property string $order_no
 * @property string $trade_no
 * @property boolean $in_out 1入账，2出账
 * @property float $amount
 * @property float $balance
 * @property string $content
 * @property boolean $state -1删除，0待入账，1成功，2失败
 * @property string $remark
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereTaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereTradeNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereInOut($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereBalance($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property float $return_amount 退款金额
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereReturnAmount($value)
 */
	class TaBilling extends \Eloquent {}
}

namespace App\Models{
/**
 * Class TaGroup
 *
 * @property integer $id
 * @property integer $ta_id
 * @property integer $guide_id 指派导游uid
 * @property string $title
 * @property string $num
 * @property \Carbon\Carbon $start_time
 * @property \Carbon\Carbon $end_time
 * @property boolean $state 0未开始，1已开始，2已结束
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereTaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereStartTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereEndTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class TaGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * Class TaLog
 *
 * @property integer $id
 * @property integer $type
 * @property integer $uid
 * @property string $content
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaLog whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaLog whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaLog whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class TaLog extends \Eloquent {}
}

namespace App\Models{
/**
 * Class TaSm
 *
 * @property integer $id
 * @property boolean $type 1.注册，2.忘记密码. 3修改密码
 * @property string $mobile
 * @property string $code
 * @property boolean $is_valid -1过期无效，0有效
 * @property string $sid 短信运营商的id
 * @property integer $ip
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaSm whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaSm whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaSm whereMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaSm whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaSm whereIsValid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaSm whereSid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaSm whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaSm whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaSm whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class TaSm extends \Eloquent {}
}

namespace App\Models{
/**
 * Class TaWithdraw
 *
 * @property integer $id
 * @property integer $uid
 * @property string $withdraw_name
 * @property string $withdraw_bank
 * @property string $withdraw_sub_bank
 * @property string $withdraw_card_number
 * @property float $amount
 * @property float $balance
 * @property boolean $state 0未审核，1已审通过，2已打款，4已驳回
 * @property string $remark
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaWithdraw whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaWithdraw whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaWithdraw whereWithdrawName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaWithdraw whereWithdrawBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaWithdraw whereWithdrawSubBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaWithdraw whereWithdrawCardNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaWithdraw whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaWithdraw whereBalance($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaWithdraw whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaWithdraw whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaWithdraw whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaWithdraw whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class TaWithdraw extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property-read \App\Models\Role $role
 * @mixin \Eloquent
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property integer $role_id 角色id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereRoleId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereUpdatedAt($value)
 * @property string $invite_code 邀请码
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereInviteCode($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * Class UserAddress
 *
 * @property integer $id
 * @property integer $uid
 * @property string $name
 * @property string $mobile
 * @property integer $province_id 省
 * @property integer $city_id 市
 * @property integer $district_id 区
 * @property string $address
 * @property boolean $is_default
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereProvinceId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereCityId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereDistrictId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereIsDefault($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class UserAddress extends \Eloquent {}
}

namespace App\Models{
/**
 * Class UserBase
 *
 * @property integer $id
 * @property string $nick_name
 * @property string $mobile
 * @property string $password
 * @property boolean $is_guide
 * @property float $amount
 * @property float $freeze_amount
 * @property boolean $state 0未审，1正常，2审核未通过， 4关停
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereNickName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereSalt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereIsGuide($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereFreezeAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $salt
 * @property string $token token
 * @property boolean $sex 1.男 2.女
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereSex($value)
 */
	class UserBase extends \Eloquent {}
}

namespace App\Models{
/**
 * Class UserCart
 *
 * @property integer $id
 * @property string $open_id
 * @property integer supplier_id
 * @property integer $goods_id
 * @property integer $spec_id
 * @property integer $num
 * @property integer $ta_id
 * @property integer $guide_id
 * @property integer $is_selected
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereOpenId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereSupplierId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereSpecId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereTaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereIsSelected($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property integer
 * @property integer
 */
	class UserCart extends \Eloquent {}
}

namespace App\Models{
/**
 * Class UserFavorite
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $goods_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserFavorite whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserFavorite whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserFavorite whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserFavorite whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserFavorite whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $open_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserFavorite whereOpenId($value)
 */
	class UserFavorite extends \Eloquent {}
}

namespace App\Models{
/**
 * Class UserNews
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $news_id
 * @property integer $is_read 1已读，0未读
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserNews whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserNews whereNewsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserNews whereIsRead($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserNews whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserNews whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class UserNews extends \Eloquent {}
}

namespace App\Models{
/**
 * Class UserWx
 *
 * @property integer $id
 * @property integer $uid 用户id
 * @property string $open_id wx open id
 * @property string $union_id wx union id
 * @property string $code
 * @property string $avatar
 * @property boolean $sex 1.男，2女
 * @property string $userdata
 * @property integer $ta_id
 * @property integer $guide_id
 * @property integer $remark_name
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereOpenId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereUnionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereAvatar($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereSex($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereUserdata($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereTaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideUser whereRemarkName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class UserWx extends \Eloquent {}
}

