<?php
namespace Trust\Service;
/**
 * 门票核销
 * @Author: IT Work
 * @Date:   2019-11-22 21:23:48
 * @Last Modified by:   IT Work
 * @Last Modified time: 2019-11-22 21:43:17
 */
class CheckIn {
	
	//门票核销
    static public function closeTicket($data)
    {
    	try{
	        $order = self::commonCheck($data);
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
	        if(!self::timeCheck($plan, (int)$proconf['validity'])){
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
	            $ticketIdx = self::channelToTicket($data['cavp']); 
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
        
    }


	static public function commonCheck($data)
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
    /**
     * 时间场次验证
     * $plan 销售计划
     * $validity 有效期 0表示当天有效
     */
    static function timeCheck($plan, $validity = 0){
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
    static function channelToTicket($channel)
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
}