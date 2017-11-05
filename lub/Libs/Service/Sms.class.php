<?php
// +----------------------------------------------------------------------
// | LubTMP 短信服务
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
use Libs\Service\Lublogs;
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
		 switch ($type) {
		 	case '1':
		 		$message = urlencode("您已购买".$title.",又见五台山门票".$remark.",订单号".$sn.",请凭订单号到售票厅取票窗口兑换门票【又见五台山】");

		 		//$message = urlencode("您已购买".$title."鼎盛王朝康熙大典演出门票".$remark."，订单号".$sn."，请凭订单号到景区指定窗口兑换门票。【康熙大典】";
		 		break;
		 	case '3':
		 		$datetime = date('Y年m月d日H：i：s');
		 		$datetime = substr($datetime,2);
		 		$message = urlencode("您购买的".$title."又见五台山门票，单号为".$sn."，于".$datetime."出票。【又见五台山】");
		 		break;
		 	case '4':
		 		//整场退票
		 		$message = urlencode("您购买".$title."又见五台山门票".$num."张，因天气等不可抗力因素取消，请联系您的购买渠道办理退票。详询5208888。【又见五台山】");
		 		break;
		 	case '6':
		 		//代订订单短信模板
		 		$message = urlencode("您已预订".$title.",又见五台山演出门票".$num."张，订单号".$sn."，请凭订单号到景区指定窗口付款兑换门票【又见五台山】");
		 		break;
		 	case '7':
		 		//领导短信
		 		$area = $info['area'];
		 		$channel = $info['channel'];
		 		$message = urlencode($title."共".$num."张，其中".$area."。".$channel."。【又见五台山】");
		 		break;
		 	default:
		 		$message = urlencode("您已购买".$title."又见五台山出门票".$num."，订单号".$sn."，请凭订单号到景区指定窗口兑换门票。【又见五台山】");
		 		break;
		 }
		 
		 $parameter = "mobile=".$info['phone']."&content=".$message;
		 $json = Sms::SAEsendmsg($parameter);
		 $arr = json_decode($json,true);
		 Sms::local_sms($info['phone'],$sn, urldecode($message),$arr['result'],$type);
		 return $arr['result'];
	}
	/**
	 * @Company  承德乐游宝软件开发有限公司
	 * @Author   zhoujing      <zhoujing@leubao.com>
	 * @DateTime 2017-11-05
	 * @param    int        $tplId   模板ID
	 * @param    array      $data    发送数据 phone 手机号 
	 * @return   [type]                              [description]
	 */
	function toSms($tplId, $data)
	{
		
	}
	/*系统错误报警短息*/
	function err_msg($info){
		 $title=$info['title'];//产品名称
		 $ream = $info['rema'];//错误说明
		 $code = $info['code'];//错误代码
		 //$message = urlencode("【数字承德】警告".$title."门票".$num."张订单号".$sn."，请您凭订单号在景区售票处兑换纸质门票！");
		 $message = urlencode("警告".$title."执行".$rema."时出错，错误代码".$code."【又见五台山】");
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
	/*sae bechtech
	 * accesskey ：用户接入KEY 
	 * secretkey ：用户接入密钥 
	 * mobile ：目的手机号，多条请用英文逗号隔开，最多 100 个号码 
	 * content ：发送内容，如果含有空格，百分数等特殊内容，请用编码进行传送，最多 67 个文字（ 1 个英文或数字也算 1 个文字）
	 * http://sms.bechtech.cn/Api/send/data/json?accesskey=xxx&secretkey=yyy&mobile=您的手机号码&content=abc 
	 * */
	private function SAEsendmsg($parameter){
		$url ="http://sms.bechtech.cn/Api/send/data/json?accesskey=1086&secretkey=50676d0bd89d243740038699c6921218323b3fa6&".$parameter;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		$res = curl_exec( $ch );
		curl_close( $ch );
		//$res  = curl_error( $ch );
		//var_dump($res);
		return $res;	
	}
}