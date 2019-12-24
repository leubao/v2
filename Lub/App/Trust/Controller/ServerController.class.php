<?php
namespace Trust\Controller;
use Think\Controller\HproseController;
use Libs\Service\Order;
class ServerController extends HproseController{

	protected $crossDomain =    true;
    protected $P3P         =    true;
    protected $get         =    true;
    protected $debug       =    true;

    public function verify_order($data)
    {
        
    }
    /**
     * 创建定单
     * @Author   zhoujing                 <zhoujing@leubao.com>
     * @DateTime 2019-05-22T14:48:31+0800
     * @param    string                   $value                [description]
     * @return   [type]                                         [description]
     */
    public function create_order($data)
    {
        //构建写入数据
        
    	//创建订单
        $uInfo = ['id' => '-1'];
        

        //判断是否可预订
        $info = D('Order')->where(['order_sn'=>$data['order_sn']])->find();
        if(empty($info)){
            $order = new Order();
            $sn = $order->orderApi($data,'52',$uInfo);
            if($sn){
                $seat = sn_seat($sn['order_sn']);
                $return = array(
                    'code' => 0,
                    'status' => true,
                    'sn' => $sn,
                    'seat'  => $seat,
                );
                return $return;
            }else{
                return showReturnCode(false,1000,'','',$order->error);
            }
        }else{

        }
         //
    }
    
    //查询订单
    public function query_order($data)
    {
    	
    }
    //查询库存
    public function query_sku($data)
    {
    	
    }
    //短信重发
    public function rest_sms($data)
    {
    	
    }
    /**
     * 绑定账号
     * @Author   zhoujing                 <zhoujing@leubao.com>
     * @DateTime 2019-05-22T14:52:45+0800
     */
    public function post_user($data)
    {
    	$map = ['username'	=>	$data['username'], 'status'	=>	1];
    	$user = D('user')->where($map)->field('id,groupid,cid,password,verify')->find();
    	if($user['password'] === hashPassword($data['password'], $user['verify'])){
    		$ugroup = D('CrmGroup')->where(['id'=>$user['groupid']])->field('price_group')->find();
    		$product = D('product')->where(['idCode'=>$data['product']])->getField('id');
    		$where = [];
    		$where =['group_id'=>['in', explode(',', $ugroup['price_group'])],'product_id'=>$product];
    		$where['_string']="FIND_IN_SET(5,scene)";
            $where['status'] = '1';
    		$ticket = D('TicketType')->where($where)->field('id,name,area,price,discount')->select();
    		foreach ($ticket as $k => $v) {
                $rticket[$v['id']] = $v;
            }
    		$return = [
    			'uid'	  => $user['id'],
    			'cid'	  => $user['cid'],//返回价格分组及名称
    			'product' => $rticket
    		];
    		return ['code' => 0,'status' => true,'data' => $return,'msg' => 'ok'];
    	}else{
    		return ['code' => 1000,'status' => false,'msg' => '密码错误~'];
    	}
    	//返回用户信息 仅存 用户id
    }

    public function commonCheck($data)
    {
        if(!in_array($data['type'],['sn','qr','mobile'])){
            $return = [
                'status'=> false,
                'code'  => 1000,
                'data'  => [],
                'msg'   => '核验方式未被支持~'
            ];
            return $return;
        }
        $map = [];
        switch ($data['type']) {
            case 'sn':
                $map = ['order_sn' => $data['code']];
                break;
            case 'qr':
                $qrInfo = \Libs\Service\Encry::getQrData($data['code']);
                if(!$qrInfo){
                    return ['status'=> false, 'code'  => 1000, 'data' => ['count'=>0], 'msg'   => '数据校验失败~'];
                } 
                $map = ['id' => $qrInfo[1]];
                break;
            case 'mobile':
                $map = ['phone' => $data['code']];
                break;
        }
        $map['product_id'] = $data['proId'];
        $order = D('Order')->where($map)->field('id,product_type,order_sn,plan_id,number,status')->order('createtime desc')->find();
        if(empty($order)){
            $return = [ 'status'=> false, 'code'  => 1000, 'data'  => ['count'=>0], 'msg'   => '未找到有效订单信息'];
            return $return;
        }else{
            //获取产品配置
            $proconf = load_redis('get', 'check_proconf_'.$data['proId']);
            if(empty($proconf)){
                $proconfList = D('ConfigProduct')->where(['product_id'=>$data['proId'],'type'=>1])->select();
                foreach ($proconfList as $k => $v) {
                    $proconf[$v['varname']] = $v['value'];
                }
                load_redis('setex', 'check_proconf_'.$data['proId'], json_encode($proconf), 3600);
            }else{
                $proconf = json_decode($proconf, true);
            }
            $order['proconf'] = $proconf;
            return $order;
        }

    }
    //同步销售计划
    public function sync_plan($data)
    {
        $product = M('Product')->where(['idCode'=>$data['product']])->field('id,idCode')->find();
        $map = [
            'product_id' => $product['id'],
            'status'  => 2
        ];
        $list = M('Plan')->where($map)->field('id')->limit(30)->select();
        
        $plan = [];
        $db = D('Plan');
        foreach ($list as $key => $value) {
            $plan[] = $db->get_alizhiyou_plan($value['id']);
        }
        $return = [
            'product'   =>  $product['idcode'],
            'plan'      =>  $plan
        ];
        return ['code' => 0,'status' => true,'data' => $return,'msg' => 'ok'];
    }
    //获取销售计划
    public function get_plan($data)
    {
        switch ($data['type']) {
            case '1':
                //获取当天的场次
                $map = array(
                    'status' => '2',
                    'plantime'=>strtotime(date('Ymd')),
                );
                break;
            case '2':
                //获取指定日期的场次
                $map = array(
                    'status' => '2',
                    'plantime'=>strtotime($data['datetime']),
                );
                break;
            case '3':
                //获取所有可售场次
                $map = array(
                    'status' => '2',
                );
                break;
        }
        if(empty($data['price'])){
            $plan = M('Plan')->where($map)->field('id,plantime,starttime,endtime,product_id,games')->select();
        }else{
            //获取带销售价格的场次
            //构造查询条件
            $proconf = cache('ProConfig');
            $proconf = $proconf[$data['product']]['1'];
            $info = array(
                'scene'    =>  $data['scene'], 
                'product'  =>  $data['product'],
                'group'    =>  array('price_group'=>$proconf['web_price'])
            );
            $plans = \Libs\Service\Api::plans($info);

            foreach ($plans['plan'] as $key => $value) {
                $plan['plan'][] = array(
                    'title' =>  $value['title'],
                    'id'    =>  $value['id'],
                    'num'   =>  $value['num'],
                    'pid'   =>  $value['product_id'],
                );
                $plan['area'][$value['id']] = $value['param'];
            }
        }
        if(empty($plan)){
            return showReturnCode(false,1000);
        }
        return showReturnCode(true,0,$plan);
    }
    //订单查询 返回最近10条定单
    public function get_order($data)
    {
        if(!in_array($data['type'],['sn','qr','mobile'])){
            $return = [
                'status'=> false,
                'code'  => 1000,
                'data'  => [],
                'msg'   => '核验方式未被支持~'
            ];
            return $return;
        }
        switch ($data['type']) {
            case 'sn':
                $map = ['order_sn' => $data['code']];
                break;
            case 'qr':
                $qrInfo = \Libs\Service\Encry::getQrData($data['code']);

                if(!$qrInfo){
                    return ['status'=> false, 'code'  => 1000, 'data' => ['count'=>0], 'msg'   => '数据校验失败~'];
                } 
                $map = ['id' => $qrInfo[1]];
                break;
            case 'mobile':
                $map = ['phone' => $data['code']];
                break;
        }
        $order = D('Item/Order')->where($map)->order('createtime desc')->relation(true)->limit(10)->select();
        if(empty($order)){
            $return = [ 'status'=> false, 'code'  => 1000, 'data'  => ['count'=>0], 'msg'   => '未找到有效订单信息'];
            return $return;
        }else{
            
        }
    }
    //核验获取门票 订单号、二维码、手机号
    public function get_ticket($data)
    {
        $order = $this->commonCheck($data);
        if(isset($order['status']) && !$order['status']){
            return $order;
        }
        $plan = F('Plan_'.$order['plan_id']);
        if($plan){
            $field = ['seat_table','id','plantime','starttime','endtime','product_type'];
            $plan = D('Plan')->where(['id'=>$order['plan_id']])->field($field)->find();
        }
        if(empty($plan)){
            $return = [
                'status'=> false,
                'code'  => 1000,
                'data'  => [
                    'count' =>  0
                ],
                'msg'   => '销售计划已暂停销售...'
            ];
            return $return;
        }
        $table = ucwords($plan['seat_table']);
        if((int)$order['product_type'] === 1){
            $ticket = D($table)->where(['order_sn'=>$order['order_sn']])->field('order_sn,price_id,area,seat,id,checktime,status')->select();
        }else{
            $ticket = D($table)->where(['order_sn'=>$order['order_sn']])->field('order_sn,price_id,ciphertext,plan_id,id,status,checktime')->select();
        }
        $tickets = [];  
        if(!empty($ticket)){
            foreach ($ticket as $k => $v) {
              if(isset($v['ciphertext'])){
                $single = $v['ciphertext'];
                $area = '';
              }else{
                $area = areaName($v['area'], 1);
                $single = seatShow($v['seat'], 1);
              }
              $tickets[] = [
                'sn'    =>  $v['order_sn'],
                'price' =>  ticketName($v['price_id'],1),
                'plan'  =>  planShow($v['plan_id'],2,1),
                'area'  =>  $area,
                'ticket'=>  $single, 
                'id'    =>  $v['id'],
                'status'=>  ticketStatus($v['status']),
                'checktime' => empty($v['checktime']) ? '' : date('Y-m-d H:i:s', $v['checktime'])
              ];
            }
        }
        $return = [
          'status'=> true,
          'code'  => 0,
          'data'  => [
            'sn'    => $order['order_sn'],
            'plan'  => planShow($order['plan_id'], 2, 1),
            'number'=> $order['number'],
            'count' => count($tickets),
            'ticket'=> $tickets
          ],
          'msg' =>  'success'
        ];
        return $return;
    }
    
    //门票核销
    public function post_checkin($data)
    {
        $order = $this->commonCheck($data);
        load_redis('set', 'check1112', json_encode($order));
        if(isset($order['status']) && !$order['status']){
            return $order;
        }
        $plan = F('Plan_'.$order['plan_id']);
        if(!$plan){
            $field = ['seat_table','id','plantime','starttime','endtime','product_type'];
            $plan = D('Plan')->where(['id'=>$order['plan_id']])->field($field)->find();
        }
        if(empty($plan)){
            $return = [
                'status'=> false,
                'code'  => 1000,
                'data'  => [
                    'count' =>  0
                ],
                'msg'   => '销售计划已暂停销售...'
            ];
            return $return;
        }
        //校验是否是否是有效期模式
        $proconf = $order['proconf'];
        //校验是否到了可检票时间 TODO
        if(!$this->timeCheck($plan, (int)$proconf['validity'])){
            $return = [
                'status'=> false,
                'code'  => 1000,
                'data'  => [
                    'count' =>  0
                ],
                'msg'   => '未到检票时间...'
            ];
            return $return;
        }
        $table = ucwords($plan['seat_table']);

        $upTicket = [
            'status'    =>  99,
            'checktime' =>  time()
        ];
        //全部核销
        if($data['way'] == 'all'){
            $where = [
                'order_sn'=> $order['order_sn'],
                'status'  => 2
            ];
        }
        //部分核销
        if($data['way'] == 'small'){
            foreach ($data['info'] as $k => $v) {
              $ticId[] = $v['id'];
            }
            $id = implode(',', $ticId);
            $where = [
                'order_sn'  =>  $data['sn'],
                'status'    =>  2,
                'id'        =>  ['in', $id]
            ];
        }
        //根据票型绑定核销点
        if(isset($data['cavp'])){
            $ticketIdx = $this->channelToTicket($data['cavp']); 
            $where = array_merge($where, ['price_id' => ['in', $ticketIdx]]);
            $whereC = ['order_sn'=>$order['order_sn'],'price_id'=> ['in', $ticketIdx],'status'=>2];
        }else{
            $whereC = ['order_sn'=>$order['order_sn'],'status'=>2];
        }
        //当前订单是否存在未核销的门票
        $count = D($table)->where($whereC)->count();
        if((int)$count === 0){
            $return = [
                'status'=> false,
                'code'  => 1000,
                'data'  => [
                    'count' => 0,
                ],
                'msg'   =>  '核销失败,未找到可核销的门票'
            ];
            return $return;
        }
        $number = D('Order')->where(['order_sn'=>$order['order_sn']])->getField('number');
        //改变门票状态
        $ticket = D($table)->where($where)->setField($upTicket);
        //判断门票是否已核销完成
        if((int)$ticket === (int)$number){
            $status = 9;
        }else{
            $status =10;//部分核销
        }
        //改变订单状态
        $upOrder = [
            'status'    =>  $status,
            'uptime'    =>  time()
        ];
        $orderStatus = D('Order')->where(['order_sn'=>$order['order_sn']])->setField($upOrder);
        $more = $number - $ticket;
        if($ticket && $orderStatus){
          // if((int)$order['product_type'] === 1){
          //   $ticketList = D($table)->where(['order_sn'=>$order['order_sn']])->field('order_sn,price_id,area,seat,plan_id,id,checktime')->select();
          // }else{
          //   $ticketList = D($table)->where(['order_sn'=>$order['order_sn']])->field('order_sn,price_id,ciphertext,plan_id,id,checktime')->select();
          // }
          $return = [
            'status'=> true,
            'code'  => 0,
            'data'  => [
              'count'  => $ticket,
              'sn'     => $order['order_sn'],
              'usetime'=> planShow($order['plan_id'],32,1),
            ],
            'msg'   =>  '成功核销'.$ticket.'张,剩余可核销'.$more."张"
          ];
          return $return;
        }else{
          $return = [
            'status'=> false,
            'code'  => 1000,
            'data'  => [
                'count' => 0,
            ],
            'msg'   =>  '核销失败,请重试'
          ];
          return $return;
        }


        //return \Libs\Service\Checkin::checkSingleTicket($data);
    }
    //读取通道票型
    public function get_channel($data)
    {
       // $product_id = D('Product')->where(['idCode' => $data['proId']])->getField('id');
        $info = D('Terminal')->where(['product_id'=>$data['proId'],'status'=>1])->field('id,name')->select();
        $return = [ 'status'=> true, 'code'  => 0, 'data'  => ['count'=>0], 'msg'   => '未找到有效订单信息'];
        return $return;
    }
    //编码换id
    public function coding_to_id($data)
    {
        $info = D('Product')->where(['idCode' => $data['code']])->field('id,name,type')->find();
        if(empty($info)){
            $return = [
                'status'=> false,
                'code'  => 1000,
                'data'  => [],
                'msg'   =>  '未找到可核销产品~'
              ];
              return $return;
        }
        return  [
            'status'=> true,
            'code'  => 0,
            'data'  => $info,
            'msg'   => 'success'
        ];
    }
    /**
     * 时间场次验证
     * $plan 销售计划
     * $validity 有效期 0表示当天有效
     */
    private function timeCheck($plan, $validity = 0){
        if(empty($plan)){ return false; }
        //获取系统日期
        $datetime = date('Ymd');
        //日期
        $plantime = date('Ymd',$plan['plantime']);
        //检票基准时间
        $starttime = date('H:i',$plan['starttime']);
        $endtime = date('H:i',$plan['endtime']);
        //判断产品类型
        $product = D('ConfigProduct')->where(['product_id'=>$plan['product_id']])->cache('conp',3600)->select();
        $ktime = isset($product['checktimeS']) ? $product['checktimeS'] : '40';
        $jtime = isset($product['checktimeE']) ? $product['checktimeS'] : '30';
            
        //剧场 演出结束前20分钟
        if($plan['product_type'] == '1'){
            //检票时间
            $start = strtotime("$starttime -$ktime minute");
            $ends = strtotime("$endtime -$jtime minute");
            if($datetime === $plantime){
                /*判断日期*/
                if(date('H',$plan['endtime']) == '00'){
                    $totime = strtotime('24'.':'.date('i',$plan['endtime']));
                }else{
                    $totime = strtotime(date('H:i'));
                }
                if($start <= $totime && $totime <= $ends){
                    //判断时间
                    return true;
                }else{
                    return false;
                }
                return true;
            }else{
                return false;
            }
        }
        //景区 开园之前的50分钟   离闭园的30分钟  
        //TODO 门票多日有效的情况
        if($plan['product_type'] == '2'){
            /*计算时间差*/
            if($plantime > $datetime){
                //提前预定，提前去
                $timediff = $plan['plantime'] - strtotime($datetime);
            }elseif($plantime < $datetime){
                $timediff = strtotime($datetime) - $plan['plantime'];
            }else{
                $timediff = 0;
            }

            if($timediff > 0){
                $day = intval($timediff/86400);
                if($day > $validity){
                    return false;
                }else{
                    return true;
                }
            }
            $start = date('H:i',strtotime("$starttime -$ktime minute"));
            $end = date('H:i',strtotime("$endtime -$jtime minute"));
        }
        //漂流
        if($plan['product_type'] == '3'){
            $start = date('H:i',strtotime("$starttime -$ktime minute"));
            $end = date('H',strtotime("$starttime +$jkime minute"));
        }
        if($datetime === $plantime){
            return true;
        }else{
            return false;
        }
    }
    //获取通道可检票型
    private function channelToTicket($channel)
    {
        //获取指定通道
        $info = D('Terminal')->where(['id'=>['in',$channel]])->field('id,ticket')->select();
        $ticketIdx = [];
        foreach ($info as $k => $v) {
            $ticket = json_decode($info['ticket'], true);
            if(!empty($ticket)){
                if(empty($ticketIdx)){
                    $ticketIdx = $ticket;
                }else{
                    $ticketIdx = array_merge($ticket, $ticketIdx);
                }
            }
        }
        return $ticketIdx;
    }

    public function test1($data){
        return $data.'test1';
    }
    public function test2(){
        return 'test2';
    }
}