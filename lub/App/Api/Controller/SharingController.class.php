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
        //$time = strtotime('20180301');
       // D('TeamOrder')->where(['createtime'=>['gt',$time]])->delete();
        /**
        $list = D('Order')->where(['createtime'=>['gt',$time]])->field('id')->select();
        foreach ($list as $key => $value) {
            D('Order')->where(['id'=>$value['id']])->delete();
            D('OrderData')->where(['oid'=>$value['id']])->delete();
        }**/
        $work = 'a:8:{s:5:"appid";s:5:"14127";s:6:"appkey";s:32:"df50da2a0ac925733f739dd9b4aa34c5";s:4:"plan";s:3:"287";s:2:"sn";s:32:"e63c9fbebe7c834d40a60df3eac279c2";s:5:"money";s:4:"0.10";s:5:"oinfo";a:1:{i:0;a:4:{s:5:"price";s:4:"0.10";s:6:"areaId";s:2:"10";s:7:"priceid";s:3:"115";s:3:"num";s:1:"1";}}s:3:"crm";a:2:{s:5:"phone";s:11:"18631451216";s:7:"contact";s:6:"周靖";}s:5:"param";a:1:{s:6:"remark";s:8:"官网PC";}}';
        $return = unserialize($work);
        dump($return);
    }
    /**
     * 系统更新
     * http://ticket.leubao.com/api.php?m=Sharing&a=upsystem
     */
    function upsystem(){
        //更新产品识别码
        $plist = D('Product')->field('id,createtime')->select();
        foreach ($plist as $k => $v) {
           $idCode = getGoodsNumber($v['id'],date('Ymd',$v['createtime']));
           D('Product')->where(['id'=>$v['id']])->setField('idCode',$idCode);
        }
        //更新商户识别码
        $clist = D('Crm')->field('id,create_time')->select();
        foreach ($clist as $k => $v) {
           $incode = date('Ymd',$v['create_time']).str_pad($v['id'],4,mt_rand(1, 999999), STR_PAD_LEFT);
           D('Crm')->where(['id'=>$v['id']])->setField('incode',$incode);
        }
    }
    public function rebate()
    {   
        echo U('Api/Sharing/seat_auto_group',['plan'=>402]);
        /************crm param json 转换 start**********
        $list = D('Crm')->field('id,param,f_agents')->select();
        foreach ($list as $k => $v) {
           $param = unserialize($v['param']);
           if(!empty($param)){
                D('Crm')->where(['id'=>$v['id']])->setField('param',json_encode($param));
           }else{
                //读取父及设置参数
                $tlevel = M('Crm')->where(array('id'=>$v['f_agents']))->field('param')->find();
                D('Crm')->where(['id'=>$v['id']])->setField('param',$tlevel['param']);
           }
          
        }
        /************crm param json 转换 end**********/
        /*
        $h = D('CrmRecharge')->where(['crm_id'=>3,'type'=>2])->sum('cash');
        dump($h);
        $c = D('CrmRecharge')->where(['crm_id'=>3,'type'=>1])->sum('cash');
        dump($c);
        $r = D('CrmRecharge')->where(['crm_id'=>3,'type'=>5])->sum('cash');
        dump($r);

        $payLink = crm_level_link(43);
        //判断链条中所有人余额充足
        
        //统一扣除订单金额，每天返利
        $info['money'] = '99';
        //渠道商客户
        $db = M('Crm');
        $payWhere = [
            'id'    =>  ['in', implode(',',$payLink)],
            'cash'  =>  ['EGT',$info['money']]
        ];
        $balanceCount = $db->where($payWhere)->field('id')->count();
        
        if((int)$balanceCount === (int)count($payLink)){
            echo count($payLink).'bak';
        }else{
            echo $balanceCount;
        }*/
        //$crm = D('Crm')->where(array('id'=>['in',implode(',',['1','43'])],'cash'=>array('EGT',$info['money'])))->field('id')->count();
        //dump($crm);
        /*
        $url = 'https://www.iesdouyin.com/share/video/6533460370089053448/';  //这儿填页面地址
        $info=file_get_contents($url);
        //preg_match('|<title>(.*?)<\/title>|i',$info,$m);
        //echo $m[1];
        dump($info);
        //preg_match_all('|<script>(.*?)<\/script>|i',$info,$m);
        //dump($m);var data = 
        //preg_match_all("/(?<=contacts\":)\s*\[\s*\{(.*?)\]/", $info, $matches);
        preg_match('/var data = "(\d+)"/',$info,$m);
        dump($m);
        //session('user','12');
        //dump(session('user'));

        //$provinces = json_decode($b,true);
        $province = D('province')->select();
        foreach ($province as $o => $e) {
            if($e['fid'] > 0){
               $city[$e['fid']][$e['id']] = $e; 
            }
            
            /*
            $data[] = [
                'id'  => $e['id'],
                'name'=> $e['name'],
                'city'=> $city
            ]; *
        }
        foreach ($province as $o => $e) {

            if($e['fid'] == 0){
                if(empty($city[$e['id']])){
                    echo "string";
                    $city[$e['id']] = [
                        'id'  => $e['id'],
                        'name'=> $e['name']
                    ];
                }
                $data[$e['id']] = [
                    'id'  => $e['id'],
                    'name'=> $e['name'],
                    'city'=> $city[$e['id']]
                ];
            }
        }
        /*dump($f);
        foreach ($provinces['provinces'] as $key => $value) {
            
            foreach ($value['city'] as $k => $v) {
                $fid = $f[$value['id']];//dump($fid);
                $data[] = [
                    'countries' => 1,
                    'fid'  => $fid['id'],  
                    'code' => $v['id'],
                    'name' => $v['name']
                ];
            }
            
        }*/
        //D('province')->addAll($data);
        //dump($data);//dump($city);
       // dump(json_decode($b,true));



        /*
        D('Cash')->where(['status'=>3,'datetime'=>'20180408'])->setField('status',1);
        $list = D('Cash')->where(['status'=>3,'datetime'=>'20180409'])->field('openid,money')->select();
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
        echo $mon;*/
    }
    public function seat_auto_group()
    {
        //写入分组信息
        $ginfo = I('get.');
        $plan = D('Plan')->where(['id'=>$ginfo['plan'],'status'=>['in','2,3']])->find();
        if(empty($plan)){
            die('销售计划不可用');
        }
        $product = D('Product')->where(['id'=>$plan['product_id']])->find();
        $data = unserialize($plan['param']);
        $auto_group = implode(',',$data['auto_group']);
        $group_map = array(
            'id'    =>  array('in',$auto_group),
            'status'=>  '1',
            'product_id'=>get_product('id'),
            'template_id'=>$product['template_id'],
        );
        $group  = M('AutoSeat')->where($group_map)->field('id,sort,seat')->select();
        foreach ($group as $ke=>$va){
            $group_seat[$ke] = unserialize($va['seat']);
            //按排遍历
            foreach ($group_seat[$ke] as $ka=>$ve){
                if(!empty($ve['seat'])){
                    $map = array(
                        'area'  =>  $ve['id'],
                        'seat'  =>  array('in',$ve['seat']),
                    );
                    $up_seat = D(ucwords($plan['seat_table']))->where($map)->setField(array('group'=>$va['id'],'sort'=>$va['sort']));
                    if($up_seat == false){
                        echo '座位分组规则写入有误';
                    }
                }   
            }
        }
    }
}