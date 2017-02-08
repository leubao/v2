<?php
// +----------------------------------------------------------------------
// | LubTMP 二维码生成
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>2014-12-19
// +----------------------------------------------------------------------
namespace Libs\Service;
class Qrcode{
	/**
	 * 生成二维码
	 * @param  string $data  二维码数据
	 * @param  string $level 纠错级别：L、M、Q、H
	 * @param  string $size  点的大小：1到10,用于手机端4就可以了
	 * @return [type]        [description]
	 */
	function createQrcode($data, $name = 'temp',$level = 'L', $size = '4'){
		//生成二维码
        Vendor('phpqrcode.phpqrcode');
        if (function_exists('png')) { 
			return "存在函数imag_openn"; 
		}
        // 下面注释了把二维码图片保存到本地的代码,如果要保存图片,用$fileName替换第二个参数false
        $path = SITE_PATH."d/upload/";
        // 生成的文件名
        $fileName = $path.$name.'.png';//dump($fileName);
        return \QRcode::png($data, $fileName, $level, $size);
	}

}