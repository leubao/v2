<?php


namespace Home\Controller;


use Common\Controller\Base;
use Home\Service\Partner;
use Wechat\Service\Wticket;
class WechatController extends Base
{
    //当前用户可售的票型
    function index(){
        //读取当前用户所属分组
        $uinfo = Partner::getInstance()->getInfo();
        $map = ['group_id'=>['in',$uinfo['group']['price_group']],'status'=>1,'type'=>2];
        $ticket = D('TicketType')->where($map)->field('id,product_id,name,area,price,discount')->select();
        $product = D('Product')->where(['id'=>['in', array_column($ticket, 'product_id')]])->field('id,name')->select();
        $product = array_column($product, 'name', 'id');
        
        foreach ($ticket as $k => $v) {
            $v['product'] = $product[$v['product_id']];
            $tickets[] = $v;
        }
        $this->assign('ticket',$tickets);
        $this->assign('uinfo', $uinfo);
        $this->display();
    }
    //产品详情
    function show(){
        $ginfo = I('get.');
        if(!isset($ginfo['pid']) || empty($ginfo['pid'])){
            $this->error('参数错误~', U('Home/Wechat/index'));
        }
        if(!isset($ginfo['tid']) || empty($ginfo['tid'])){
            $this->error('参数错误~', U('Home/Wechat/index'));
        }
        $info = D('TicketType')->where(['status'=>1,'id'=>$ginfo['tid']])->field('id,name,price,discount')->find();
        if(empty($info)){
           $this->error('产品已下架~');
        }
        $product = D('Product')->where(['id'=>$ginfo['pid']])->field('id,name')->cache('product_'.$ginfo['pid'], 3600)->find();

        $uinfo = Partner::getInstance()->getInfo();
        $plan = $this->getplan($ginfo['pid'],$uinfo,[$ginfo['tid']]);
        //dump($plan);
        $global = array_merge($plan,$uinfo);dump($global);
        $this->assign('product', $product)->assign('info', $info)->assign('global', json_encode($global))->display();
    }
    //创建订单
    function order(){
        if(IS_POST){
        
        }else{
            $this->display();
        }
    }
    //支付订单
    function payment(){
        $pinfo = I('post.');
        if($pinfo['way']){

        }
        if($pinfo['way'] === ''){

        }
        if($pinfo['way'] === 'wxpay'){

        }
    }
    //订单列表
    function orderlist(){
        $this->display();
    }
    function orderinfo(){
        $this->display();
    }

    /*获取销售计划
    * @Author   zhoujing                 <zhoujing@leubao.com>
    * @DateTime 2019-11-15T13:44:31+0800
    * @param    inr                   $pid                  产品id
    * @param    array                    $ticket               返回票型
    * @return   [type]                                         [description]
    */
    function getplan($pid, $user, $ticket = array()){
        $product = M('Product')->where(array('status'=>1,'id'=>$pid))->field('id')->select();
        $info['product'] = arr2string($product,'id');
        //根据当前用户判断   若为散客  读取配置项  微信的价格政策
        $info['group']['price_group'] = $user['group']['price_group'];
        $info['scene'] = '2';
        $plan = \Libs\Service\Api::plans($info, '', '', $ticket);
        foreach ($plan['plan'] as $key => $value) {
            $plans['plan'][] = array(
                'title' =>  $value['title'],
                'id'    =>  $value['id'],
                'num'   =>  $value['num'],
            );
            if(empty($value['param'])){
                $plans['area'][$value['id']] = [];
            }else{
                $plans['area'][$value['id']] = $value['param'];
            }
            
        }
        return $plans;
    }
    /**
     * 授权绑定
     */
    public function auth()
    {
        
    }
}