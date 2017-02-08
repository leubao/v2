<?php
// +----------------------------------------------------------------------
// | LubTMP 接口服务 Hprose  客户端
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Api\Controller;
use Common\Controller\ApiBase;
use Libs\Service\Api;
use Libs\Service\Order;
use Common\Model\Model;
use Libs\Service\Report;
class IndexController extends ApiBase {
    //获取场次信息
    function api_plan(){
    	if(IS_POST){
    		$pinfo = $_POST['data'];
        $pinfo = json_decode($pinfo,true);
    		$appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
			if($appInfo != false){
				//获取销售计划
        $info = Api::plans($appInfo);
				$return = array(
	    			'code'	=> 200,
	    			'info'	=> $info,
	    			'msg'	=> 'OK',
	    		);
	    	}else{
	    		$return = array('code' => 401,'info' => '','msg' => '认证失败');
	    	}
    	}else{
    		$return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
    	}
      $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
    	echo json_encode($return);
    }
    
    //api 订单写入
    function api_order(){
        if(IS_POST){
            $pinfo = $_POST['data'];
            $pinfo = json_decode($pinfo,true);
            $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']); 
            if($appInfo != false){
                if(!empty($pinfo['sn'])){
                  //判断是否已下单
                  $sn = $this->query_order(array('sn'=>$pinfo['sn'],'type'=>1));//dump($sn);
                  if($sn != false){
                    //已经下单直接返回
                    $return = array(
                        'code'  => 201,
                        'info'  => $sn,
                        'seat'  => sn_seat($sn),
                        'msg'   => 'OK',
                      );
                  }else{
                    //组合订单数据
                    $info = $this->order_info($pinfo,$appInfo);
                    if($info != false){
                      //TODO API团队和API散客暂时按照支付方式来分  只记录不付费的51 API散客 52 API团队
                      if($appInfo['is_pay'] == '1'){
                        $scena = '51';
                      }else{
                        $scena = '52';
                      }
                      $sn = Order::orderApi($info,$scena,$appInfo);
                      if($sn){
                        $return = array(
                          'code'  => 200,
                          'info'  => $sn,
                          'seat'  => sn_seat($sn),
                          'msg'   => 'OK',
                        );
                      }else{
                        $return = array('code' => 403,'info' => '','msg' => '订单提交失败');
                      }
                    }else{
                      $return = array('code' => 406,'info' => '','msg' => '金额校验失败');
                    }
                  }
                }else{
                  $return = array('code' => 409,'info' => '','msg' => '终端标识不存在');
                }
            }else{
                $return = array('code' => 401,'info' => '','msg' => '认证失败');
            }
        }else{
            $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
        }
        $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
        echo json_encode($return);
    }
    //库存查询
    function api_sku(){
      if(IS_POST){
          $pinfo = $_POST['data'];
          $pinfo = json_decode($pinfo,true);
          $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
          if($appInfo != false){
            $info = sku($pinfo['plan'],$pinfo['area']);
            if($info != false){
              $return = array('code' => 200,'info' => $info,'msg' => 'OK');
            }else{
              $return = array('code' => 407,'info' => '','msg' => '查询失败');
            }
          }else{
            $return = array('code' => 401,'info' => '','msg' => '认证失败');
          }
      }else{
        $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
      }
      $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
      echo json_encode($return);
    }
    /*订单查询
    参数 appid appkey sn type 1 终端sn 2 系统SN 3 纯系统取票
    */
    function api_query_order(){
        if(IS_POST){
            $pinfo = $_POST['data'];
            $pinfo = json_decode($pinfo,true);
            $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
            if($appInfo != false){
              //订单查询 不存在返回false  存在返回订单详细信息
              $sn = $this->query_order(array('sn'=>$pinfo['sn'],'type'=>$pinfo['type']));
              if($sn != false){
                $return = array('code' => 200,'info' => $sn,'seat'=>sn_seat($sn),'msg' => 'OK');
              }else{
                $return = array('code' => 407,'info' => '','msg' => '查询失败');
              }
            }else{
              $return = array('code' => 401,'info' => '','msg' => '认证失败');
            }
        }else{
          $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
        }
        $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
        echo json_encode($return);
    }
    /*
    * 短信重发  只支持系统订单号
    *
    */
    function api_sms(){
      if(IS_POST){
          $pinfo = $_POST['data'];
          $pinfo = json_decode($pinfo,true);
          $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
          if($appInfo != false){
            if(Order::repeat_sms($pinfo['sn']) != false){
              $return = array('code' => 200,'info' => '','msg' => '发送成功');
            }else{
              $return = array('code' => 408,'info' => '','msg' => '发送失败');
            }
          }else{
            $return = array('code' => 401,'info' => '','msg' => '认证失败');
          }
      }else{
          $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
      }
      $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
      echo json_encode($return);
    }
    /*
    *API订单查询
    */
    function query_order($data = null){
      if(empty($data)){return false;}
      switch ($data['type']) {
        case '1':
          //通过APP_SN 查询
          $order_sn = M('ApiOrder')->where(array('app_sn'=>$data['sn']))->find();
          if(!empty($order_sn)){
            return $order_sn['order_sn'];
          }else{
            return false;
          }
          break;
        case '2':
          //通过票务系统订单号查询
          $order_sn = M('ApiOrder')->where(array('order_sn'=>$data['sn']))->find();
          if($order_sn){
            return $order_sn['order_sn'];
          }else{
            return false;
          }
          break;
        case '3':
          //通过票务系统订单号查询
          $order_sn = M('Order')->where(array('order_sn'=>$data['sn']))->find();
          if($order_sn){
            return $order_sn['order_sn'];
          }else{
            return false;
          }
          break;
        default:
          
          break;
      }
    }
    /**
     * API接口退票 
     * @param $type int 1 整单退 2退其中几张
     * @param $sn  订单号
     * @param $sns 客户端订单号
     * @param $seat string 多个用‘,’分开 
     * @return true|false
     */
    function api_refund(){
      if(IS_POST){
        $pinfo = $_POST['data'];
        $pinfo = json_decode($pinfo,true);
        $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
        if($appInfo != false && $pinfo['type'] != false){
          switch ($pinfo['type']) {
            case '1':
              
              break;
            case '2':
              
              break;
          }
        }else{
          $return = array('code' => 401,'info' => '','msg' => '认证失败');
        }
      }else{
          $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
      }
      $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
      echo json_encode($return);
    }
    /*
    *订单取票
    *$type 取票方式 1 手机号码+订单号 2身份证 3微信
    */
    function api_print(){
      if(IS_POST){
        $pinfo = $_POST['data'];
        $pinfo = json_decode($pinfo,true);
        $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
        if($appInfo != false && $pinfo['type'] != false){
          switch ($pinfo['type']) {
            case '1':
              if($this->print_check($pinfo['sn'],$pinfo['phone']) != false){
                $ticket_info = $this->ticket_info($pinfo['sn'],$appInfo['id'],'1');
                if($ticket_info != false){
                  $return = array('code' => 200,'info' => $ticket_info,'msg' => '门票信息获取成功');
                }else{
                  $return = array('code' => 411,'info' => '','msg' => '门票信息获取失败');
                }
              }else{
                $return = array('code' => 410,'info' => '','msg' => '取票密码错误');
              }
              break;
            case '2':
              $ticket_info = $this->ticket_info($pinfo['card'],$appInfo['id'],'2');
              if($ticket_info != false){
                $return = array('code' => 200,'info' => $ticket_info,'msg' => '门票信息获取成功');
              }else{
                $return = array('code' => 411,'info' => '','msg' => '门票信息获取失败');
              }
              break;
          }
        }else{
          $return = array('code' => 401,'info' => '','msg' => '认证失败');
        }
      }else{
          $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
      }
      $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
      echo json_encode($return);
    }
    /*
    * 身份证取票
     */
    function api_print_card(){
      if(IS_POST){
        $pinfo = $_POST['data'];
        $pinfo = json_decode($pinfo,true);
        $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
        if($appInfo != false && $pinfo['type'] != false){
          switch ($pinfo['type']) {
            case '2':
              $order_list = $this->order_list($pinfo['card']);
              if($order_list != false){
                $return = array('code' => 200,'info' => $order_list,'msg' => '订单列表获取成功');
              }else{
                $return = array('code' => 411,'info' => '','msg' => '订单列表获取失败');
              }
              break;
          }
        }else{
          $return = array('code' => 401,'info' => '','msg' => '认证失败');
        }
      }else{
          $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
      }
      $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
      echo json_encode($return);
    }
    /*
    * 根据订单号返回要打印信息 禁止取政企订单
    * @param $sn 订单号 
    * @param $type 1、手机号+订单号 2、身份证取票
    * @param $card 身份证取票
    */
    function ticket_info($sn = null,$channel_id = null,$type = '1'){
      switch ($type) {
        case '1':
          if(empty($sn) || sn_length($sn) == false){return false;}
          if(empty($channel_id)){
            $map = array('order_sn'=>$sn,'status'=>'1','type'=>array('neq',6));
          }else{
            $map = array('order_sn'=>$sn,'status'=>'1','type'=>array('neq',6));
          }
          break;
        case '2':
          //TODO  身份证号码校验
          if(checkIdCard($sn) != false){
            $map = array('id_card'=>$sn,'status'=>'1','type'=>array('neq',6));
          }else{
            return false;
          }
          break;
      }
      $info = M('Order')->where($map)->field('plan_id,order_sn')->find();
      $plan = F('Plan_'.$info['plan_id']);
      if(empty($plan)){return false;}
      $list = M(ucwords($plan['seat_table']))->where(array('status'=>2,'order_sn'=>$info['order_sn'],'print'=>array('eq',0)))->select();
      foreach ($list as $k=>$v){
        $info[] = re_print($plan['id'],$plan['encry'],$v);
      }
      return $info;
    }
    /*
    * 根据身份号码获取订单列表
    */
   function order_list($sn = null){
      if(empty($sn) || checkIdCard($sn) == false){error_insert('400019');return false;}
      $map = array('id_card'=>$sn,'status'=>'1','type'=>array('neq',6),'plan_id'=>array('in',normal_plan()));
      $list = M('Order')->where($map)->field('order_sn,phone,number,plan_id')->select();
      if(!empty($list)){
        foreach ($list as $key => $value) {
          $data[] = array(
            'order_sn' => $value['order_sn'],
            'phone' => $value['phone'],
            'number'=>$value['number'],
            'title'=>planShow($value['plan_id'],1,1), 
          );
        }
        return $data;
      }else{
        error_insert('400020');
        return false;
      } 
      
   }
    /* 更新座椅状态 自助取票机打印门票
    *  $plan 计划id
    *  $seat 座位ID
    *  $priceid 价格id 
    *  $sn 订单号码
    *  $type 1 更新单张状态  2 整单打印完成
    */
    function api_seat_status(){
      if(IS_POST){
        $pinfo = $_POST['data'];
        $pinfo = json_decode($pinfo,true);//dump($pinfo);
        $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
        if($appInfo != false){
          $plan = F('Plan_'.$pinfo['plan']);
          if(empty($plan)){
            $return = array('code' => 413,'info' => '','msg' => '场次已过期');
            return false;
          }
          //更新门票打印状态
          $model = new Model();
          $model->startTrans();
          $sn =  $pinfo['sn'];
          $type = $pinfo['type'];
          //判断订单类型
          $order_type = order_type($sn);
          if($type == '1'){
            $map = array('order_sn'=>$sn,'id'=>$pinfo['id']);
            $up_print = $model->table(C('DB_PREFIX'). $plan['seat_table'])->where($map)->setInc('print',1); 
            $up_order = true;
            $remark = "打印".$pinfo['seat']."单号".$sn;
          }else{
            //更新订单状态
            $up_order = $model->table(C('DB_PREFIX'). order)->where(array('order_sn'=>$sn))->setField('status',9);
            $up_print = true;
            $remark = "打印定单".$sn."完结";
          }
          if($up_print && $up_order){
            //记录打印日志
            print_log($sn,$appInfo['id'],$type,$order_type['channel_id'],$remark,6);
            $model->commit();//提交事务
            $return = array('code' => 200,'info' => $ticket_info,'msg' => '状态更新成功');
          }else{
            $model->rollback();//事务回滚
            $return = array('code' => 412,'info' => '','msg' => '状态更新失败');
          }
        }else{
          $return = array('code' => 401,'info' => '','msg' => '认证失败');
        }
      }else{
        $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
      }
      $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
      echo json_encode($return);
    }
    //验证取票密码 取票凭证 手机号+订单号
    function print_check($sn,$phone){
      if(empty($sn) && empty($phone)){
        return false;
      }else{
        $info = M('Order')->where(array('order_sn'=>$sn,'phone'=>$phone))->field('order_sn,phone')->find();
        if($info){
          $pwd = $this->than_pwd($sn,$phone);
          $pwds = $this->than_pwd($info['order_sn'],$info['phone']);
          if($pwd == $pwds){
            return true;
          }else{
            return false;
          }
        }else{
          return false;
        }
      }
    }
    function than_pwd($sn,$phone){
      $phone = substr($phone,4,6);
      $sn    = substr($sn,7);
      $pwd = md5(($sn+$phone)%256);
      return $pwd;
    }
    //构造订单请求
    //$pinfo1 = '{"subtotal":288,"checkin":1,"data":[ {"areaId":21,"priceid":27,"price":288,"num":"1"} ],"param":[{"guide":"测试","qditem":"爱上大声地","phone":18631451216,"contact":"啊实打实"},{"cash":288,"card":0,"alipay":0}]}';
    /*
    array(5) {
  ["subtotal"] => int(814)
  ["checkin"] => int(1)
  ["data"] => array(3) {
    [0] => array(4) {
      ["areaId"] => int(88)
      ["priceid"] => int(1)
      ["price"] => int(218)
      ["num"] => string(1) "1"
    }
    [1] => array(4) {
      ["areaId"] => int(89)
      ["priceid"] => int(10)
      ["price"] => int(298)
      ["num"] => string(1) "1"
    }
    [2] => array(4) {
      ["areaId"] => int(89)
      ["priceid"] => int(4)
      ["price"] => int(298)
      ["num"] => string(1) "1"
    }
  }
  ["crm"] => array(1) {
    [0] => array(4) {
      ["guide"] => int(2)
      ["qditem"] => int(10)
      ["phone"] => int(18634151216)
      ["contact"] => string(2) "sa"
    }
  }
  ["param"] => array(1) {
    [0] => array(2) {
      ["tour"] => int(18)
      ["remark"] => string(11) "sadadadasda"
    }
  }
} */ 
    function order_info($pinfo,$appInfo){
        /*$seat = $this->format_seat($pinfo,$appInfo);
        if($seat){
          $info = array(
            'subtotal'  =>  $pinfo['money'],
            'plan_id'   =>  $pinfo['plan'],
            'checkin'   => '1',
            'app_sn'    =>  $pinfo['sn'],
            'data'      =>  $seat,
            'crm'       =>  array('0'=>array('guide'=>$appInfo['id'],'qditem'=>$appInfo['crm_id'],'phone'=>$pinfo['crm']['phone'],'contact'=>$pinfo['crm']['contact'])),
            'param'     =>  array('0'=>array('tour'=>'0','remark'=>$pinfo['param']['remark'])),
          );
          return $info;
        }else{
          return false;
        }*/
        $info = array(
          'subtotal'  =>  $pinfo['money'],
          'plan_id'   =>  $pinfo['plan'],
          'checkin'   => '1',
          'app_sn'    =>  $pinfo['sn'],
          'data'      =>  $pinfo['oinfo'],
          'crm'       =>  array('0'=>array('guide'=>$appInfo['id'],'qditem'=>$appInfo['crm_id'],'phone'=>$pinfo['crm']['phone'],'contact'=>$pinfo['crm']['contact'])),
          'param'     =>  array('0'=>array('tour'=>'0','remark'=>$pinfo['param']['remark'])),
        );
        return $info;
    }

    //校验传递过来的数据
    function format_seat($pinfo,$appInfo){
        $plan = F('Plan_'.$pinfo['plan']);
        //重组座位
        $seat = Order::area_group($pinfo['oinfo'],$plan['product_id'],$appInfo['group']['settlement']);
        
        $ticketType = F("TicketType".$plan['product_id']);
        foreach ($seat['area'] as $k => $v) {
          foreach ($v['seat'] as $ke => $va) {
            $price = $ticketType[$va['priceid']];
            $money += $va['num']*$price['price'];
            $moneys += $va['num']*$price['discount'];
          }
        }
        //dump($pinfo['money']);
       // dump($moneys);
        if(bccomp((float)$pinfo['money'],(float)$moneys,2) == 0){
          return $seat;
        }else{
          return false;
        }
    }
    //测试计划接入
    function c_plan(){
      $url = "http://new.leubao.com/api.php?a=api_plan";
      $post = array(
        'appid' => '26628',
        'appkey'=> '8613f25b1f2691c8a1db85f1cb095d29',
      );
      $post['data'] = json_encode($post);
      $aa = $this->curl_server($url,$post);
      dump($aa);
    }
    //测试order
    function c_order(){
      $url = "http://new.leubao.com/api.php?a=api_order";
      $post = array(
        'appid' => '26628',
        'appkey'=> '8613f25b1f2691c8a1db85f1cb095d29',
        'money' =>  '0.1',
        'plan'  =>  '2960',
        'sn'    =>  '162291129ss11',
        'oinfo' =>  array('0'=>array('areaId'=>'151','priceid'=>'34','price'=>'0.1','num'=>'1')),
        'crm'   =>  array('contact'=>'联系人','phone'=>'18631451216'),
        'param' =>  array('remark'=>'备注..')
      );
      $post['data'] = json_encode($post);
      $aa = $this->curl_server($url,$post);
      dump($aa);
    }
    //测试库存查询
    function c_sku(){
      $url = "http://tickets.leubao.com/api.php?a=api_sku";
      $url = "http://new.leubao.com/api.php?a=api_plan";
      $post = array(
        'appid' => '26628',
        'appkey'=> '8613f25b1f2691c8a1db85f1cb095d29',
        'plan'  =>  '86',
        'area' =>  '89',
        );
      $post['data'] = json_encode($post);
      $aa = $this->curl_server($url,$post);
      dump($aa);

    }
    //测试订单查询 type 1 order_sn 票务系统订单号查询 2 app_sn 查询  3 根据order_sn 查询订单
    function c_query_order(){
      $url = "http://tickets.leubao.com/api.php?a=api_query_order";
      $url = "http://new.leubao.com/api.php?a=api_plan";
      $post = array(
        'appid' => '39989',
        'appkey'=> 'c922b084221663d43ef62e54142923a7',
        'type'  =>  '3',
        'sn' =>  '50824141140608',
      );
      $post['data'] = json_encode($post);
      $aa = $this->curl_server($url,$post);
      dump($aa);
    }
    //短信重发
    function c_tosms(){
      $url = "http://tickets.leubao.com/api.php?a=api_sms";
      $url = "http://new.leubao.com/api.php?a=api_plan";
      $post = array(
        'appid' => '39989',
        'appkey'=> 'c922b084221663d43ef62e54142923a7',
        'sn' =>  '50701141140620',
        );
      $post['data'] = json_encode($post);
      $aa = $this->curl_server($url,$post);
      dump($aa);
    }
    //自助机dayin
    function c_print(){
     // $url = "http://www.yx513.net/api.php?a=api_print";
      $url = "http://new.leubao.com/api.php?a=api_plan";
      $post = array(
        'appid' => '65535',
        'appkey'=> 'a646ce13e4c01f42b8ac2a0ca879069',
        'sn' =>  '51111143165',
        'phone'=>'18631451216',
       // 'card'  => '4',
        'type' => '1',
        );
      $post['data'] = json_encode($post);
      $aa = $this->curl_server($url,$post);
      dump($aa);
      dump(json_decode($aa));
    }
    //查询花费和返佣不匹配的订单
    function with_fill(){
      //查询所有渠道订单
      $list = M('Order')->where(array('addsid'=>array('in','2,4'),'type'=>array('in','2,4'),'status'=>array('in','1,9,7,8')))->limit('1,200')->field('order_sn')->order('id DESC')->select();
      //匹配返佣订单
     // dump($list);
      dump(count($list));
      foreach ($list as $k => $v) {
        $status = M('TeamOrder')->where(array('order_sn' => $v['order_sn']))->find();
        if(!$status){
          $data[] = $v['order_sn'];
          //$this->sqlshow($v['order_sn']);
        }
      }
      //echo "string";
      dump($data);
    }
    
    //生成sql语句
    function sqlshow($sn){


      $map = array(
        'order_sn' => $sn,
        //'status' => '9',
        'type'  => array('in','2,4'),
        //'subtract' => '1',
      );
      $info = D('Item/Order')->where($map)->relation(true)->find();
      $info['info'] = unserialize($info['info']);
      //dump($info);
      $rebate = $this->rebate($info['info']['data'],$info['product_id']);
      $teamData = array(
        'order_sn'    => $sn,
        'plan_id'     => $info['plan_id'],
        'product_type'  => $info['product_type'],//产品类型
        'product_id'  => $info['product_id'],
        'user_id'     => $info['user_id'],
        'money'     => $rebate,
        'guide_id'    => $info['info']['crm'][0]['guide'],
        'qd_id'     => $info['info']['crm'][0]['qditem'],
        'status'    => '1',
        'number'    => $info['number'],
        'type'      => $info['type'],//窗口团队时可选择，渠道版时直接为渠道商TODO 渠道版导游登录时
        'createtime'  => time(),
        'uptime'    => time(),
      );
      $in_team = D('TeamOrder')->add($teamData);
      return $in_team;

   /*   $sn = "60303141105156,60303141162397,60303141150913";
      
    //  $sn = "60306141189367,60306141131724,60306141130886,60306141152431,60306141198810,60306141174687";
      $sns = explode(',', $sn);
      //dump($sns);
      foreach ($sns as $key => $value) {
        $map = array(
          'order_sn' => $value,
          //'status' => '9',
          'type'  => array('in','2,4'),
          //'subtract' => '1',
        );
        $info = D('Item/Order')->where($map)->relation(true)->find();
        $info['info'] = unserialize($info['info']);
        //dump($info);
        $rebate = $this->rebate($info['info']['data'],$info['product_id']);
        $teamData = array(
          'order_sn'    => $value,
          'plan_id'     => $info['plan_id'],
          'product_type'  => $info['product_type'],//产品类型
          'product_id'  => $info['product_id'],
          'user_id'     => $info['user_id'],
          'money'     => $rebate,
          'guide_id'    => $info['info']['crm'][0]['guide'],
          'qd_id'     => $info['info']['crm'][0]['qditem'],
          'status'    => '1',
          'number'    => $info['number'],
          'type'      => $info['type'],//窗口团队时可选择，渠道版时直接为渠道商TODO 渠道版导游登录时
          'createtime'  => time(),
          'uptime'    => time(),
        );
        $in_team = D('TeamOrder')->add($teamData);
        dump($in_team);
      }
      //dump($info);*/
    }
    //计算补贴金额
    function rebate($seat,$product_id){
      $ticketType = F("TicketType".$product_id);
      foreach ($seat as $k=>$v){
        //计算订单返佣金额
        $rebate += $ticketType[$v['priceid']]['rebate'];
      }
      return $rebate;
    }
   /*
  	向服务端发送验证请求
  	@param $url string 服务器URL
  	@param $post_data array 需要提交的数据
  	*/
    private function curl_server($url,$post_data){
	    $ch = curl_init();
	    curl_setopt($ch,CURLOPT_URL,$url);
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	    curl_setopt($ch,CURLOPT_POST,1);
	    curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    return $output;
	}
}