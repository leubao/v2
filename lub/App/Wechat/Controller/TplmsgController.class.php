<?php
// +----------------------------------------------------------------------
// | LubTMP 微信前台
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>2015-8-25 
// +----------------------------------------------------------------------
namespace Wechat\Controller;
use Common\Controller\LubTMP;
use Wechat\Service\Wechat;
use WeChat\Service\Wxpay;
use Wechat\Service\Api;


//微信支付
use Wechat\Service\Wxpay\WxPayApi;
use Wechat\Service\Wxpay\JsApiPay;
use Wechat\Service\Wxpay\WxPayConfig;
use Wechat\Service\Wxpay\WxPayUnifiedOrder;
use Wechat\Service\Wxpay\WxPayOrderQuery;
use Wechat\Service\Wxpay\WxPayException;
use Wechat\Service\Wxpay\WxPayNotify;
use Wechat\Controller\PayNotifyCallBackController;
class IndexController extends LubTMP {
    /**
     * 实现的TplMsg钩子方法，对模板消息进行处理
     * @params string $params   参数数组中必须包含mp_id,template_id,touser三个参数，其他变量可自由添加,如参数中有url，将优先使用url参数作为详情url
     * @return void      hook函数木有返回值
     * 注意：乱发模板消息会狗带！
     */
    public function TplMsg($param){

        $params = $param;  //保存原始参数
        if(empty($param['mp_id']) || empty($param['template_id']) || empty($param['touser'])){ //数据不正确
            return ;
        }
        $mp = get_mpid_appinfo($param['mp_id']);

        $model = M('Tplmsg');
        $tmap['mp_id'] = $param['mp_id'];
        $tmap['template_id'] = $param['template_id'];
        $tplmsg = $model->where($tmap)->find();
        if(empty($tplmsg)){ //数据不正确
            return ;
        }
        $fmodel = M('Tplmsg_field');
        $tplmsg_field = $fmodel->where($tmap)->select();   //获取到模板字段

        unset($param['mp_id']);
        unset($param['template_id']);
        unset($param['touser']);              //不用于替换的参数先去掉
        //组装模板字段
        $data = array();
        foreach ($tplmsg_field as $key => &$val){
            foreach ($param as $k => $v){  //用$params中的参数替换字段中的{$ }变量
                $val['value'] = str_replace('{$'.$k.'}', $v, $val['value']);
            }
            $data[$val['name'] ] = array("value" => $val['value'],"color" => $val['color'],);
        }

        //发送模板消息
        $TMArray = array(
            "touser" => $params['touser'],
            "template_id" => $params['template_id'],
            "url" => isset($params['url']) ? $params['url'] : $tplmsg['url'],
            "topcolor" => $tplmsg['topcolor'],    //这个参数好像已废弃
            "data" => $data
        );
        trace(json_encode($TMArray)."AAA",'TplMsg:','DEBUG',true);
        $options['appid'] = $mp['appid'];    //初始化options信息
        $options['appsecret'] = $mp['secret'];
        $options['encodingaeskey'] = $mp['encodingaeskey'];
        $weObj = new TPWechat($options);
        $res = $weObj->sendTemplateMessage($TMArray);
    }
}