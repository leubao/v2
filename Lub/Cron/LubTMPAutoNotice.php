<?php
// +----------------------------------------------------------------------
// | LubTMP  取票自动确认
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace CronScript;
use Libs\Service\ArrayUtil;
class LubTMPAutoNotice {
	/*计划任务错误代码
	*130001 订单查询失败
	*/
    //任务主体
    public function run($cronId) {
        $date = date('Ymd');
        $datetime = strtotime($date);
        //获取API下单的订单号
        $olist = D('Order')->where(['createtime'=>['gt', $datetime], 'status'=>9, 'addsid'=>5])->field('id,order_sn,number')->order('id desc')->select();
        //二维数组转一维数组
        $nSnArr = array_column($olist,'order_sn');
        //获取今天已通知的sn
        $snList = load_redis('lrange','notice_sn_'.$date,0,-1);
        if(empty($snList)){
            $diffSn = $nSnArr;
            //判断
            $hdate = date('Ymd', strtotime('-1 day'));
            $hSn = load_redis('lsize', 'notice_sn_'.$hdate);
            if($hSn > 0){
              load_redis('delete', 'notice_sn_'.$hdate);
            }
        }else{
            //取得差集
            $diffSn = array_diff($nSnArr,$snList);
        }
        
        if(!empty($diffSn)){
            //向阿里智游推送
            $this->toTnci($olist, $diffSn);
            //记录本次查询的最大id
            //load_redis('set', 'noticeId', $olist[0]['id']);
        }
    }
    public function toTnci($data, $snArr)
    {
        foreach ($data as $k => $v) {
            if(in_array($v['order_sn'], $snArr)){
                $api_sn = $this->getTnciSn($v['order_sn']);
                if($api_sn){
                    $postData = [
                        'orderId'   =>  $api_sn,//天时单号，
                        'outOrderId'=>  $v['order_sn'],//云鹿单号
                        'tickets'   =>  $v['number'],//验票数
                        'method'    =>  'ConsumeNotice',
                        'appkey'    =>  '7a773e4aa7f84ee6669dc92e4f8fe6be'
                    ];
                    $postData = $this->setSign($postData);
                    $url = 'http://jygl.sjdzp.com/Api/LocalYunluPush/api.json?g_cid=19526';
                    $rest = getHttpContent($url,'POST',json_encode($postData));
                    load_redis('lpush','notice_sn_'.date('Ymd'), $v['order_sn']);
                    load_redis('lpush','tnci_notice',json_encode($rest));
                } 
            }
        }
    }
    public function toAlizhiyou($data)
    {
        $url = 'https://api.alizhiyou.com/not';
    }

    public function getTnciSn($sn)
    {
        $sn = D('ApiOrder')->where(['order_sn'=>$sn])->getField('app_sn');
        if(!empty($sn)){
            return $sn;
        }else{
            return false;
        }
    }
    /**
     * 设置签名
     * @author helei
     */
    public function setSign($data)
    {
        $values = ArrayUtil::removeKeys($data, ['sign','appkey']);

        $values = ArrayUtil::arraySort($values);

        $signStr = ArrayUtil::createLinkstring($values);

        $values['sign'] = $this->makeSign($signStr,$data['appkey']);
        return $values;
    }
    /**
     * 签名算法实现 目前签名算法支持md5
     * @param string $signStr 签名字符串
     * @param string $appeky app秘钥
     * @return string
     */
    protected function makeSign($signStr,$appkey)
    {
        $signStr .= '&key=' . $appkey;
        $sign = md5($signStr);
        return strtoupper($sign);
    }
}