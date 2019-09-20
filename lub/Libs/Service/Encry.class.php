<?php
// +----------------------------------------------------------------------
// | LubTMP 订单加密
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
class Encry extends \Libs\System\Service {
	/*
	订单号加密 新规则 
    @param $plan_id int 计划id
	@param $sn int 订单号 
	@param $encry int 每日加密常量
	@param $area int 区域
	@param $seat int 座位
    @param $print int 打印次数
    @param $id int 座位id
	*/
	function encryption($plan_id, $sn, $encry, $area, $seat, $print='1',$id){
        $seat = Encry::seat_fold($seat);
		$string = $plan_id.$sn.$encry.$area.$seat.$id;
        $string = $print*$string;
        $crc32 = Encry::reader($string,$encry);
        $code_data = $plan_id."^".$id."^".$crc32;
     //   $code_data = $plan_id."/".$id."/".$crc32;
        return $code_data;
	}
    /*一单一票订单号转16进制*/
    function sn_to_16($sn){
        
    }
/*
    解密规则
    @param $plan_id int 计划id
    @param $sn int 订单号 
    @param $encry int 每日加密常量
    @param $area int 区域
    @param $seat int 座位
    @param $print int 打印次数
    @param $id int 座位id
    @param $crc32 string 校验码
    */
    function decryption($plan_id, $sn, $encry, $area, $seat, $print='1',$id,$crc32){
        $seat = Encry::seat_fold($seat);
        $string = $plan_id.$sn.$encry.$area.$seat.$id;
        $string = $print*$string;
        $crc = Encry::reader($string,$encry);
        if($crc32 == $crc){
            return true;
        }else{
            return false;
        }
        
    }

    /*座位号合并
    @param $seat string 座位号
    return int 合并座位号*/
    function seat_fold($seat){
        if(empty($seat)){
            return false;
        }
        $seat_arr = explode('-',$seat);
        return $seat_arr['0'].$seat_arr['1'];
    }
	//校验
    function reader($info,$encry){
    	$header = substr($info,0,8);
    	$data = substr($info,10,15);
        $footer = substr($info,-5);
        $encry = substr($encry,2,5);
    	$info = $header.$data.$footer;
    	$len = strlen($info);
    	for ($i=0; $i < $len; $i++) { 
    	 	$code = $code+(int)ord($info[$i]);
    	}
    	$code = $code%(int)$encry;
    	$crc32 = dechex($code);
    	return $crc32;
    }

    static public function toQrData($id,$orderid,$plan_id,$print='1')
    {
        $string = $id.$orderid.$plan_id.$print;

        //计算校验位
        $digit = creatCheckDigit($string);
        $code = [
            $id,
            $orderid,
            $plan_id,
            $print,
            $digit
        ];
        $sn = putIdToCode($code, 12);
        return $sn;
    }
    //获取二维码加密解密
    static public function getQrData($value)
    {
        //解密
        $qrInfo = getCodeToId($value, 12);
        $position = array_key_last($qrInfo);
        $string = '';
        foreach ($qrInfo as $k => $v) {
            if($k < $position){
                $string .= $v;
            }
        }
        //校验
        if((int)$qrInfo[$position] === (int)creatCheckDigit($string)){
            return $qrInfo;
        }else{
            return false;
        }
    }
}