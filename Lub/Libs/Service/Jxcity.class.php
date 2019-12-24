<?php
namespace Libs\Service;
/**
 * @Author: IT Work
 * @Date:   2019-12-18 23:35:56
 * @Last Modified by:   IT Work
 * @Last Modified time: 2019-12-20 16:24:32
 */
class Jxcity extends \Libs\System\Service {
	
	//按天推送
	public function upTodayData($day, int $total = 0)
    {
    	//查询前一天数据
    	if($total === 0){
    		$day = date('Ymd', strtotime($day));
    		$plan = M('Plan')->where(array('plantime'=>$day))->field('id')->select();
    		$total = D('Order')->where(['plan_id'=>['in', array_column($plan, 'id')]])->sum('number');
    	}
    	$body = [[
    		'day'    => $day,
    		'count'	 =>	$total
    	]];
    	
    	$url = '/data/tourist/real-gate-day';
    	
    	$data = $this->setPostData($body);
    	$relust = $this->postUp($url, $data);
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
    	$url = '/data/tourist/real-people-number';
    	$body = [
    		'time'   => date('Y-m-d H:i:s', $date),
    		'devices'=> [
    		  [
    			'device' => '翼天文化旅游城',
    			'tickets'=> [
    			  [
					'ticket' => '成人票',
    				'count'  => (int)$total
    			  ]
    			]
    		  ]
    	    ]
    	];
    	$data = $this->setPostData($body);

    	$relust = $this->postUp($url, $data);
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
    	$url = '/data/tourist/real-exit-people-number';
    	$body = [
    		'time'   => date('Y-m-d H:i:s', $date),
    		'devices'=> [[
    			'device' => '翼天文化旅游城',
    			'count'  => (int)$total
    		]]
    	];
    	$data = $this->setPostData($body);
    	$relust = $this->postUp($url, $data);
    	//记录日志
    	$this->addLog($data, $relust);
    	return;
    }
    public function postUp($url, $postData)
    {
    	$header = [
    		'Content-Type:application/json'
		];
		$url = 'https://openapi.all4tour.cn/zjxtdj/'.$url;
		//$url = 'https://test.openapi.all4tour.cn/zjxtdj/'.$url;
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
        }echo $data;
        return $data;
    }

    public function addLog($request, $response)
    {
    	$data = [
    		'push_id'   	=>  1003,
    		'name'	    	=>	"上饶旅游监管平台",
    		'request'		=>	json_encode($request),
    		'response'		=> 	$response,
    		'create_time'	=>	date('Y-m-d H:i:s')
    	];
    	$state = D('PushLog')->add($data);
    	return;
    }
    //构造请求数据
    public function setPostData($body)
    {
    	$time = $this->getMillisecond();
    	$data = [
    		'header' => [
    			'scenicCode' => 'wuyuanyitiancheng',
    			'time'       => $time,
    			'sign'		 => $this->setSign($body, $time)
    		],
    		'body' => json_encode($body)
    	];
    	return json_encode($data);
    }
    //签名
    public function setSign($body, $time, $secret = '')
    {
    	if(empty($secret)){
    		$secret  = '77lHTEGldL6HtIMxfXiJfYO2ioJSia0z';
    	}
    	//$secret = 'n7J0D9S8vLUBKU3Qxre0fCPbuDNiZlZe';
    	$signStr = $secret.'&'.json_encode($body).'&'.$time;
    	return md5($signStr);
    }
    public function getMillisecond() { 
        list($t1, $t2) = explode(' ', microtime());     
        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000); 
    }
}