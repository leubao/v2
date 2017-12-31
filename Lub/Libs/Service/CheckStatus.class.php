<?php
// +----------------------------------------------------------------------
// | LubTMP 状态校验类
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
class CheckStatus extends \Libs\System\Service {
	/** 执行错误消息及代码 */
    public $error = '';
	/**
	 * 全局订单状态校验类
	 * 主要用于打印、退票等操作时判断订单是否允许当前操作
	 * @Author   zhoujing   <zhoujing@leubao.com>
	 * @DateTime 2017-09-09
	 * @param    string     $sn   订单号
	 */
	public function OrderCheckStatus($sn = '',$code = '')
	{
		if(empty($sn)){
			$this->error = "未找到有效订单";
			return false;
		}
		//查看该订单是否在锁定中
		$msg = load_redis('get','marking_'.$sn);
		if(empty($msg)){
			//检查状态时，未标记的马上标记
			$this->markingOrder($sn,$code);
			return true;
		}else{
			$this->error = $msg;
			return false;
		}
	}
	/**
	 * 标记订单状态
	 * 主要在进行  退单 打印时 调用此项方法 方便订单操作中互斥事件造成的影响
	 * 默认锁定期为5分钟
	 * @Author   zhoujing   <zhoujing@leubao.com>
	 * @DateTime 2017-09-09
	 * @param    bigint     $sn                   订单号
	 * @param    int        $code                 状态码
	 * @return   true
	 */
	function markingOrder($sn,$code = ''){
		if(empty($sn)){
			$this->error = "未找到有效订单";
			return false;
		}
		$code = $code ? $code : '0000';
		load_redis('setex','marking_'.$sn,$code,'100');
		return true;
	}
	/**
	 * 删除标记
	 */
	function delMarking($sn = '')
	{
		if(empty($sn)){
			$this->error = "未找到有效订单";
			return false;
		}
		load_redis('delete','marking_'.$sn);
		return true;
	}
	
}