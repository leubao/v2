<?php
// +----------------------------------------------------------------------
// | LubTMP 接口服务 Hprose  服务端
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Api\Controller;
use Think\Controller;
use Api\Service\Api;

class ApiController extends Controller{
	
    function _initialize(){
       
	}
    /**
     * 景区门票信息
     */
    function scenic(){
    	$appid  = I("post.appid");
        $appkey = I("post.appkey");
        $curtime = strtotime(date('Y-m-d'));
        /*$appid = "65535";
        $appkey= "cGk0ND8W";*/
        //根据appid与appkey查询
        $num = M('App')->where(array('appid'=>$appid,'appkey'=>$appkey))->count();
        if($num>0){  //表示匹配成功，获取相应的产品id
            $result = M('App')->where(array('appid'=>$appid,'appkey'=>$appkey))->find();
            $productid = $result["product"];
            $products = explode(",", $productid);
            foreach ($products as $key => $value) {
                $data = M("Product")->where(array('id'=>$value,'type'=>2))->find();
                if($data){
                    //根据时间查询销售计划
                    $condition["starttime"] = array("gt",$curtime);
                    $condition["productid"] = array("eq",$value);
                    $plan = M("Plan")->where($condition)->select();
                    //细节细化
                    foreach ($plan as $k => $v) {
                        $plan[$k]["createtime"] = date('Y-m-d',$v["createtime"]);
                        $plan[$k]["starttime"] = date('Y-m-d',$v["starttime"]);
                        $plan[$k]["endtime"] = date('Y-m-d',$v["endtime"]);
                        //操作员
                        $user = M("User")->where(array('id'=>$v["user_id"]))->find();
                        $plan[$k]["nickname"] = $user["nickname"];
                    }
                    $data["plan"] = $plan;
                    $datas[] = $data;       
                }
            }
            return $datas;
        }
    }
    /**
     * 剧院票务信息
     * 获取销售计划  及票型
     */
    function theatre(){
        $info = $_POST;
        //验证app权限
        $appInfo = $this->check_app($info);
        if($appInfo != false){
            //TODO  目前是单产品
            $plan = $this->plan($appInfo['product']);
            $return = array(
                'msg' => "OK",
                'code'=>'200',
                'info'=>unserialize($info),
            ); 
        }else{
            $return = array(
                'msg' => "非授权应用!",
                'code'=>'400001',
                'info'=>unserialize($info),
            );
        }
        if(IS_POST){
            $info = I('post.');
            $return = array(
                'msg' => "OK",
                'code'=>'201',
                'info'=>unserialize($info),
            ); 
        }
        $return = json_encode($return);
        echo $return;	
    }
    /*
    * 获取销售计划
    @param $productid int 
    */
    function plan($productid){
        $curtime = strtotime(date('Y-m-d'));
        $map = array(
            'product_id'=>$this->pid,
            'status'=>2,//状态必为售票中
        );
        //取得今天计划的ID
        $plan = M('Plan')->where($map)->select();
        dump($plan);
    }
    //获取区域销售情况
    function area(){

    }
    //获取价格政策
    function price(){

    }
    /**
     * 订单信息
     */
    function orders(){
        $order_sn = I("post.order_sn");   //订单流水号
        //$order_sn = "14091734149564";
        $num = M("Order")->where(array('order_sn'=>$order_sn))->count();
        if($num > 0){
            $data = M("Order")->alias('o')->join('left join lub_order_data od ON o.order_sn = od.order_sn')->where(array('o.order_sn'=>$order_sn))->select();   
            foreach ($data as $key => $value) {
                //操作员
                $user = M("User")->where(array('id'=>$value["user_id"]))->find();
                $data[$key]["nickname"] = $user["nickname"];
                $data[$key]["info"] = unserialize($value["info"]);      
            }
            return $data;            
        }
    }
    /**
     * 返佣信息
     */
    function commission(){
        $guide_id = I("post.guide_id");      //导游id
        $qd_id    = I("post.qd_id");         //渠道id
        $status   = I("post.status");        //状态
        /*$guide_id = "5";         //导游id
        $qd_id    = "5";         //渠道id
        $status   = "1";         //状态*/
        
        $data = M("TeamOrder")->where(array('guide_id'=>$guide_id,'qd_id'=>$qd_id,'status'=>$status))->select();
        foreach ($data as $key => $value) {
            $total += $value["money"];
        }
        return $total;        	
    }
    /**
     * 余票查询
     */
    function more_ticket(){
        $plan = I("post.plan");
        $product_id = I("post.product_id");  //产品id
        /*$product_id = "37";
        $plan = "1416982286-1";*/
        if($plan){
            $info = explode('-', $plan);
            $todaya = date("ymd",$info[0])."_".$info[1];
            $seat_table = "Seat_".$product_id."_".$todaya;
        }else{
            //将当前日期格式化
            $today = date('ymd');
            $todaya = $today."_1";
            $seat_table = "Seat_".$product_id."_".$todaya;
        }
        
        $num = M($seat_table)->where(array('status'=>0))->count();  //求得余票总数
        return $num;

    }

    /**
     * 外部订单写入
     * $pt 产品类型
     * 产品id 销售计划id 票型及数量
     */
    function inst_order($pinfo = null){
    	if(empty($pinfo)){
    		return "400001";
    	}
    	
    	if($pinfo['pt'] == 1){
    		
    	}else{
    		$info = unserialize($pinfo);
    		//景区门票
    		$data = array(
    			//'order_sn' => ,
    			
    		);
    		//事物处理
    		$sn = get_order_sn($pinfo['product_id'],1);
    		//写入检票信息
    		foreach ($info['ticket'] as $k=>$v){
    			$t_info[] = array(
    				'ticket_type'	=>	$v['ptype'],
    				'status'		=>	'1',
    				'order_sn'		=>	$sn,
    			);
    		}
    		$ticket = $model->tavle('scenic_ticket')->addAll($t_info);
    		//写入定单信息
    		$scenic = $model->table('scenic_order')->add($data);
    		if($ticket && $scenic){
    			return $sn;
    		}else{
    			return '400002';
    		}
    	}
    }

    /*应用验证
    * @param $data array  post 过来的信息
    */
    function check_app($data){
        $map =  array('appid'=>$data['appid'],'status'=>1);
        $info = M('App')->where($map)->find();
        $code = $data['appid'].$info['appkey'];
        $code = md5($code);
        if($data['appkey'] == $code){
            return $info;
        }else{
            return false;
        }
    }
  /*  public function test2(){
        return 'test2';
    }
    错误代码 400001 参数错误
    	400002 定单创建失败
    */
}