<?php
// +----------------------------------------------------------------------
// | LubTMP 短信服务
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
class Sms extends \Libs\System\Service {
	/*订票订单短信
	 @param $info 短信内容
	 @param $type 模板类型
	*/
	function order_msg($info,$type = '1'){
		 $title=$info['title'];//订单信息
		 $num = $info['num'];
		 $sn=$info['sn'];//10位$remark订单详情
		 $remark = $info['remark'];
		 $crminfo = $info['crminfo'];
		 switch ($type) {
		 	case '1':
		 		//$message = urlencode("您已购买".$title.",".$info['product']."门票".$remark.",订单号".$sn.",请凭单号到景区售票处兑换门票【".$info['product']."】");
		 		$message = urlencode("您已购买".$title."印象普陀门票".$remark."，订单号".$sn."，请凭订单号换票。地点印象普陀大剧场，请按规定时间检票入场。咨询电话05803807777 6206666【印象普陀】");
		 		break;
		 	case '3':
		 		$datetime = date('Y年m月d日H：i：s');
		 		$datetime = substr($datetime,2);
		 		$message = urlencode("您购买的".$title."印象承德帝苑梦华门票，单号为".$sn."，于".$datetime."出票。【帝苑梦华】");
		 		break;
		 	case '4':
		 		//整场退票
		 		$message = urlencode("您购买".$title."印象承德帝苑梦华演出门票".$num."张，因天气等不可抗力因素取消，请联系您的购买渠道办理退票。详询4006630930。【帝苑梦华】");
		 		break;
		 	case '6':
		 		//代订订单短信模板
		 		$message = urlencode("您已购买".$title.",印象承德帝苑梦华门票".$remark.",订单号".$sn.",请凭单号到剧场售票处兑换门票【帝苑梦华】");
		 		break;
		 	case '7':
		 		//领导短信
		 		$area = $info['area'];
		 		$channel = $info['channel'];
		 		$message = urlencode($title."共".$num."张，其中".$area."。".$channel."。【帝苑梦华】");
		 		break;
		 	case '8':
		 		//分销账号审核
		 		$message = urlencode($title."您申请的《帝苑梦华》销售账号已经通过审核,请遵守相关销售协议【帝苑梦华】");
		 		break;
		 	case '9':
		 		//微信企业支付
		 		$message = urlencode("您的提现申请已通过,订单:".$sn."提现金额:".$info['money'].",元,已存入您的微信钱包中，请注意查收【帝苑梦华】");
		 		break;
		 	default:
		 		$message = urlencode("您已购买".$title."帝苑梦华演出门票".$num."张，订单号".$sn."，请凭单号到剧场售票处兑换门票。【帝苑梦华】");
		 		break;
		 }
		 $parameter = "mobile=".$info['phone']."&content=".$message;
		 $status = Sms::SAEsendmsg($parameter);
		 Sms::local_sms($info['phone'],$sn, urldecode($message),$status,$type);
		 return $status;
	}	/*系统错误报警短息*/
	function err_msg($info){
		 $title=$info['title'];//产品名称
		 $ream = $info['rema'];//错误说明
		 $code = $info['code'];//错误代码
		 //$message = urlencode("【数字承德】警告".$title."门票".$num."张订单号".$sn."，请您凭订单号在景区售票处兑换纸质门票！");
		 $message = urlencode("警告".$title."执行".$rema."时出错，错误代码".$code."【帝苑梦华】");
		 $parameter = "mobile=".$info['phone']."&content=".$message;
		 return Sms::SAEsendmsg($parameter);
	}
	/*短信发送本地记录
	* @param $phone 目标号码
	* @param $content 短信内容
	* @param $status 短信发送状态
	* @param $type 短信类型
	*/
	function local_sms($phone,$sn = null,$content,$status,$type){
		M('SmsLog')->add(array(
			'order_sn'=> $sn,
			'phone' => $phone,
			'content'=>$content,
			'status'=>$status,
			'type'=>$type,
			'createtime'=>time(),
			));
		return true;
	}
	/*微信模板消息发送模板消息 TODO
     * {{first.DATA}}
     * 订单号：{{OrderID.DATA}}
     * 产品名称：{{PkgName.DATA}}
     * 使用日期：{{TakeOffDate.DATA}}
     * {{remark.DATA}}
    */
    function to_tplmsg($info,$tplmsgid){
        $attach = unserialize($info['attach']);
        $template = array(
            'touser'=>$info['openid'],//指定用户openid
            'template_id'=>$tplmsgid,
            'url'   =>  $info['url'],
            'data'=>array(
                'first'=>array('value'=>'您好，您的门票订单已预订成功。'."\n"),
                'OrderID' =>array('value'=>$info['out_trade_no'],'color'=>'#5cb85c'),
                'PkgName'=>array('value'=>$attach['product_name'],'color'=>'#5cb85c'),
                'TakeOffDate'=>array('value'=>$attach['plan']."\n",'color'=>'#5cb85c'),
                'remark'=>array('value'=>$this->tplremark), 
            )
        );
        //发送模板消息
        $res = $this->api->sendTemplateMessage($template);
    }
	/*sae bechtech
	 * accesskey ：用户接入KEY 
	 * secretkey ：用户接入密钥 
	 * mobile ：目的手机号，多条请用英文逗号隔开，最多 100 个号码 
	 * content ：发送内容，如果含有空格，百分数等特殊内容，请用编码进行传送，最多 67 个文字（ 1 个英文或数字也算 1 个文字）
	 * http://sms.bechtech.cn/Api/send/data/json?accesskey=xxx&secretkey=yyy&mobile=您的手机号码&content=abc 
	 * */
	private function SAEsendmsg($parameter){
		$url ="http://sms.bechtech.cn/Api/send/data/json?accesskey=3489&secretkey=56397d952c6ba7d29e04e3da5625885cd6d24162&".$parameter;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		$res = curl_exec( $ch );
		curl_close( $ch );
		//var_dump($res);
		//var_dump($url);
		return $res;	
	}
}