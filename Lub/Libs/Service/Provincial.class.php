<?php

/**
 * 江西省旅游平台对接
 * @Author: IT Work
 * @Date:   2019-12-12 22:02:04
 * @Last Modified by:   IT Work
 * @Last Modified time: 2019-12-19 15:52:26
 */
namespace Libs\Service;
class Provincial extends \Libs\System\Service {

	//按天推送
	public function upTodayData($day, int $total = 0)
    {
    	//查询前一天数据
    	//$product = M('Product')->field('id')->select();
    	if($total === 0){
    		$day = date('Ymd', strtotime($day));
    		$plan = M('Plan')->where(array('plantime'=>$day))->field('id')->select();
    		$total = D('Order')->where(['plan_id'=>['in', array_column($plan, 'id')]])->sum('number');
    	}
    	//组合数据
    	//上传至省局
    	$url = 'https://wljg.dct.jiangxi.gov.cn/upload-data/tourist/real-gate-day';

    	$data = [[
    		'scenicCode' =>	'e66ae0e5-f67a-11e9-9034-7cd30adbac64',
    		'count'		 =>	$total,
    		'day'        =>	$day
    	]];
    	$relust = $this->postUp($url, json_encode($data));
    	//记录日志
    	if($relust['code'] === '200'){

    	}
    	$this->addLog($data, $relust);
    	return;
    }
    //实时推送
    public function upRealData($date, int $count = 0)
    {
    	//查询15分钟内新增订单数据
    	$map = array(
    		'createtime' => array(
    			array('EGT', strtotime(date('Y-m-d', $date))), 
    			array('ELT', $date), 
    			'AND' 
    		),
    		'status' => ['in', ['1','9']]
    	);
    	$total = D('Order')->where($map)->sum('number');
    	//组合数据
    	//上传至省局
    	$url = 'https://wljg.dct.jiangxi.gov.cn/upload-data/tourist/real-people-number';
    	$data = [
    		'scenicCode' =>	'e66ae0e5-f67a-11e9-9034-7cd30adbac64',
    		'count'		 =>	(int)$total,
    		'upTime'     =>	date('Y-m-d H:i', $date)
    	];
    	$relust = $this->postUp($url, json_encode($data));
    	//记录日志
    	$this->addLog($data, $relust);
    	return;
    }
    //出口闸机
    public function upExitData($date, int $count = 0)
    {
    	//查询15分钟内新增订单数据
    	$map = array(
    		'createtime' => array(
    			array('EGT', strtotime(date('Y-m-d', $date))), 
    			array('ELT', $date), 
    			'AND' 
    		),
    		'status' => ['in', ['9']]
    	);
    	$total = D('Order')->where($map)->sum('number');
    	$url = 'https://wljg.dct.jiangxi.gov.cn/upload-data/tourist/real-exit-people-number';
    	$data = [
    		'scenicCode' =>	'e66ae0e5-f67a-11e9-9034-7cd30adbac64',
    		'count'		 =>	(int)$total,
    		'upTime'     =>	date('Y-m-d H:i', $date)
    	];
    	$relust = $this->postUp($url, json_encode($data));
    	//记录日志
    	$this->addLog($data, $relust);
    	return;
    }
    
    public function postUp($url, $postData)
    {
    	$appkey = 'Xr-DYKjdAFqAwf-lcvXcaVpG5WUMkKPwqPRorgA-3aQ';
    	$header = [
    		'appKey:'.$appkey,
    		'Content-Type:application/json'
		];
    	try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30); //30秒超时
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            $curlPost = is_array($postData) ? http_build_query($postData) : $postData;
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            //curl_setopt($ch, CURLOPT_HTTPHEADER, );
            $data = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
        	return $e;
            //$data = null;
        }
        return $data;
    }





    public function addLog($request, $response)
    {
    	$data = [
    		'push_id'   	=>  1001,
    		'name'	    	=>	"江西省旅游监管平台",
    		'request'		=>	json_encode($request),
    		'response'		=> 	$response,
    		'create_time'	=>	date('Y-m-d H:i:s')
    	];
    	$state = D('PushLog')->add($data);
    	return;
    }

}