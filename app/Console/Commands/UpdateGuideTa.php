<?php
namespace App\Console\Commands;

use App\Models\OrderBase;
use App\Models\UserWx;
use App\Models\GuideTum;
use Illuminate\Console\Command;
class UpdateGuideTa extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updateguideta';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update guide_ta of vistors_num and total_sales';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        self::updateGuideTa();
    
    }
    
    
    //用来定时更新数据表guide_ta里面的vistors_num和total_sales
    static function updateGuideTa(){
        $guideTaInfo = GuideTum::get();
        foreach($guideTaInfo as $info){
            //根据导游的guide_id从usee_wx里面取出bang绑定的游客数
            $vistor_num  = UserWx::whereTaId($info->ta_id)->where('guide_id',$info->guide_id)->count();
            //根据导游的guide_id从order_base表里面获取这个导游的销售额
            $total_sales = OrderBase::whereTaId($info->ta_id)->where('guide_id',$info->guide_id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_goods');
            //取出所有的邮费
            $total_amountexpress = OrderBase::whereTaId($info->ta_id)->where('guide_id',$info->guide_id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_express');
            //当前的总付款金额
            $total_sales = $total_sales + $total_amountexpress;
            //对guide_ta的数据表里面的vistors_num 和 total_sales 进行更新
            GuideTum::whereId($info->id)->update(['vistors_num'=>$vistor_num]);
            GuideTum::whereId($info->id)->update(['total_sales'=>$total_sales]);
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}