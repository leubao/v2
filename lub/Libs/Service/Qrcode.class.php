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
	 * @param  string $logo //需要显示在二维码中的Logo图像
	 * @return [type]        [description]
	 */
	function createQrcode($data, $name = 'temp',$logo = '',$level = 'L', $size = '4'){
		//生成二维码
        Vendor('phpqrcode.phpqrcode');
        if (function_exists('png')) { 
			return "存在函数imag_openn"; 
		}
        // 下面注释了把二维码图片保存到本地的代码,如果要保存图片,用$fileName替换第二个参数false
        $path = SITE_PATH."d/upload/";
        // 生成的文件名 zj
        $fileName = $path.$name.'.png';
        $qrimg = \QRcode::png($data, $fileName, $level, $size);
       // $logo = 'logo.png';//准备好的logo图片 
		$QR = $fileName;//已经生成的原始二维码图 
        if(!empty($logo)){
			$QR = imagecreatefromstring(file_get_contents($QR)); 

			$logo = imagecreatefromstring(file_get_contents($logo)); 

			$QR_width = imagesx($QR);//二维码图片宽度 

			$QR_height = imagesy($QR);//二维码图片高度 

			$logo_width = imagesx($logo);//logo图片宽度 

			$logo_height = imagesy($logo);//logo图片高度 

			$logo_qr_width = $QR_width / 5; 

			$scale = $logo_width/$logo_qr_width; 

			$logo_qr_height = $logo_height/$scale; 

			$from_width = ($QR_width - $logo_qr_width) / 2; 

			//重新组合图片并调整大小 

			imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, 

			$logo_qr_height, $logo_width, $logo_height);
        }
        imagepng($QR, $fileName); 
        return $qrimg;
	}
}