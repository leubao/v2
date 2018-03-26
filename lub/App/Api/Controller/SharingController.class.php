<?php
// +----------------------------------------------------------------------
// | LubTMP 接口服务
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Api\Controller;
use Think\Controller;
class SharingController extends Controller {
	//
    public function index(){
        $return = array(
	    		'code'	=> 404,
	    		'info'	=>	'',
	    		'msg'	=> '你太淘气了，快回去....',
	    );
	    echo json_encode($return);
    }
    function temp(){
    	$starttime = '2017-04-10 16:23:09';
    	$endtime = date('Y-m-d H:i:s');
    	$res = timediff($starttime,$endtime,'hour');
    	echo $res['hour']/48;
    	echo "<br/>";
    	echo 96%48;
    	$where = ['status'=>1,'level'=>16];
    	$list = D('Crm/Crm')->where($where)->field('id,cash,product_id')->select();
    	dump($list);
    	dump($res);
    }
    //导出用户
    public function user()
    {
        $list = D('Order')->where(['type'=>4])->field('id,take,phone')->select();
        foreach ($list as $key => $value) {
            $data[$value['phone']] = $value;
        }
        $headArr = array(
            'id'        =>  'id',
            'take'  =>  '姓名',
            'phone'     =>  '手机号码'
        );
        $filename = "年卡信息";
        return \Libs\Service\Exports::getExcel($filename,$headArr,$data);
    }
    //微信支付手动查询
    public function pay_status()
    {
        //14770962598
    }
    public function rebate()
    {   
        D('Cash')->where(['status'=>3,'datetime'=>'20180324'])->setField('status',1);
        $list = D('Cash')->where(['status'=>3,'datetime'=>'20180325'])->field('openid,money')->select();
        foreach ($list as $key => $value) {
            if((int)$value['money'] > 200){
                //大于200拆分多个红包
                $redNum = 1;
                $redInfo = [];
                $redInfo[] = [
                    'money' => $value['money']%200,
                    'sn'    => $value['sn'],
                    'openid'=> $value['openid'],
                ];
                $num = (int)floor($value['money']/200);//dump($num);
                for ($i = $num; $i <> 0; $i--) {
                    $redInfo[] = [
                        'money' => '200',
                        'sn'    => $value['sn'].'-'.$redNum,
                        'openid'=> $value['openid'],
                    ];
                    $redNum += 1;
                }//dump($redInfo);
                //构建红包基础数据,并发送红包
                foreach ($redInfo as $k => $v) {
                    echo $v['openid'].' '.(int)$v['money'].'<br/>';
                }
            }else{
                echo $value['openid'].' '.(int)$value['money'].'<br/>';
            }
            $mon += $value['money'];
        }
        echo $mon;
    }
}