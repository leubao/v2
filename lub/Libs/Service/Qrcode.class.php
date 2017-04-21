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
	 * @param  string $logopath //需要显示在二维码中的Logo图像
	 * @return [type]        [description]
	 */
	function createQrcode($data, $name = 'temp',$logopath = '',$level = 'L', $size = '4'){
		//生成二维码
        Vendor('phpqrcode.phpqrcode');
        if (function_exists('png')) { 
			return "存在函数imag_open"; 
		}
        // 下面注释了把二维码图片保存到本地的代码,如果要保存图片,用$fileName替换第二个参数false
        $path = SITE_PATH."d/upload/";
        // 生成的文件名 zj
        $fileName = $path.$name.'.png';
        $qrimg = \QRcode::png($data, $fileName, $level, $size);

        if(!empty($logopath)){
	        //获取二维码
			$qrcode = imagecreatefromstring(file_get_contents($fileName));
			$qrcode_width = imagesx($qrcode);
			$qrcode_height = imagesy($qrcode);
			/*圆角图片*/
			$corner = imagecreatefromstring(file_get_contents(SITE_PATH."static/images/corner.png"));
			$corner_width = imagesx($corner);
			$corner_height = imagesy($corner);
			//计算圆角图片的宽高及相对于二维码的摆放位置,将圆角图片拷贝到二维码中央
			$corner_qr_height = $corner_qr_width = $qrcode_width/5;
			$from_width = ($qrcode_width-$corner_qr_width)/2;
			imagecopyresampled($qrcode, $corner, $from_width, $from_width, 0, 0, $corner_qr_width, $corner_qr_height, $corner_width, $corner_height);
			

			//logo图片
			$logo = imagecreatefromstring(file_get_contents($logopath));
			$logo_width = imagesx($logo);
			$logo_height = imagesy($logo);

			//计算logo图片的宽高及相对于二维码的摆放位置,将logo拷贝到二维码中央
			$logo_qr_height = $logo_qr_width = $qrcode_width/5 - 6;
			$from_width = ($qrcode_width-$logo_qr_width)/2;
			//重新组合图片并调整大小 
			imagecopyresampled($qrcode, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
			imagepng($qrcode,$fileName);
			//echo "string";
			//销毁资源
			imagedestroy($qrcode);
			imagedestroy($corner);
			imagedestroy($logo);
		}
        /*$logo = 'logo.png';//准备好的logo图片 
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
			
			imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, 
			$logo_qr_height, $logo_width, $logo_height);
        }
        imagepng($QR, $fileName); */
        return $qrimg;
	}
}