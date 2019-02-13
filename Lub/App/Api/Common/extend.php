<?php
/**
 * 获取商品编码
 * @Company  承德乐游宝软件开发有限公司
 * @Author   zhoujing      <zhoujing@leubao.com>
 * @DateTime 2017-11-21
 * @return   string
 */
function getGoodsNumber($uuid = '',$prefix = ''){
	if(empty($prefix)){
		$prefix = date('Ymd');
	}
    return $prefix.str_pad($uuid,4,mt_rand(1, 999999), STR_PAD_LEFT). str_pad(mt_rand(1, 999999), 4, '0', STR_PAD_LEFT);
}