<?php
// +----------------------------------------------------------------------
// | LubTMP  系统扩展函数
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
/** =============================================================票务系统设置===========================================================================*/
/**
 * 根据产品ID获取产品名称
 */
function itemName($param, $type = ''){
    if(!empty($param)){
         $name = M('Item')->where(array('id'=>$param))->getField('name');
         if($type){
            return $name;
         }else{
            echo $name;
         }
    }else{
        echo "未知";
    }
}
/**
 * 根据分组ID获取票型分组名称
 */
function groupName($param){
    echo M('TicketGroup')->where(array('id'=>$param))->getField('name');
}
/*客户分组名称 crm_group*/
function crmgroupName($param){
    echo M('CrmGroup')->where(array('id'=>$param))->getField('name');
}
/**
 * 获取场景类型
 */
function scene($param){
    switch ($param){
        case 1 :
            echo "窗口";
            break;
        case 3 :
            echo "渠道版";
            break;
        case 4 :
            echo "运营平台";
            break;
        case 5 :
            echo "API";
            break;
        case 7 :
            echo "自助机";
            break;
    }
}
/**
 * 获取票型类型
 * @param $param int 类型参数
 */
function ticket_type($param){
    switch ($param){
        case 1 :
            echo "散客票";
            break;
        case 2 :
            echo "团队票";
            break;
        case 3 :
            echo "散客、团队票";
            break;
        case 4 :
            echo "政企渠道票";
            break;
        case 5 :
            echo "活动票";
            break;
        case 6 :
            echo "儿童票";
            break;
        case 7 :
            echo "老人票";
            break;
        case 8 :
            echo "军人票";
            break;
    }
}
/**
 * 获取产品名称
 * @param $param int 产品ID
 */
function product_name($param,$type=NULL){
    if(!empty($param)){
         $name = M('Product')->where(array('id'=>$param))->getField('name');
         if($type){
            return $name;
         }else{
            echo $name;
         }
    }else{
        echo "未知";
    }   
}

/**
 * 获取操作员名称  窗口售票
 * @param $param int 操作员ID
 */
function userName($param,$scene = '1',$type=NULL){
    if($param == '0'){
        $name = '阿里智游';
    }
    if(!empty($param) && $param > 0){
        switch ($scene) {
            case '1':
                $table = 'User';
                $field = 'nickname';
                break;
            case '5':
                //api时
                $table = 'App';
                $field = 'name';
                break;
            default:
                $table = 'User';
                $field = 'nickname';
                break;
        }
        $name = M($table)->where(array('id'=>$param))->cache('nickname'.$param,3600)->getField($field);
    }
    if($type){
        return $name ? $name : "未知";
    }else{
        echo $name;
    }
}
/**
 * 获取导游身份证号码
 * @param  [type] $param [description]
 * @param  [type] $type  [description]
 * @return [type]        [description]
 */
function userCard($param,$type=NULL){
    if(!empty($param)){
        $name = M('User')->where(array('id'=>$param))->getField('legally');
    }else{
        $name = "未知";
    }
    if($type){
        return $name ? $name : "未知";
    }else{
        echo $name;
    }
}
/**
 * 获取角色名称
 *  @param $param int 角色ID
 */
function roleName($param){
    if(!empty($param)){
        echo M('Role')->where(array('id'=>$param))->getField('name');
    }else{
        echo "角色未知";
    }
}
/**
 * 区域名称
 *  @param $param int 区域ID
 *  @param $type int 数据返回方式 
 */
function areaName($param,$type=NULL){
    if(!empty($param)){
        $area = F('Area');
        if(!empty($area)){
            $name = $area[$param]['name'];
        }else{
            $name = M('Area')->where(array('id'=>$param))->cache('area_name'.$param, 86400)->getField('name');
        }
        if($type){
          return $name;
        }else{
          echo $name;
        }
    }else{
        echo "区域未知";
    }
}
/*根据区域id获取座位数
 *  @param $param int 区域ID
 *  @param $type int 数据返回方式 
 */
function areaSeatCount($param,$type=NULL){
    if(!empty($param)){
        $area = F('Area');
        if(!empty($area)){
            $num = $area[$param]['num'];
        }else{
            $num = M('Area')->where(array('id'=>$param))->cache('area_num'.$param, 86400)->getField('num');
        }
        if($type){
          return $num;
        }else{
          echo $num;
        }
    }else{
        echo "区域未知";
    }
}
/**
 * 票型名称
 * @param $param
 */
function ticketName($param,$type=NULL){
    if(!empty($param)){
        $name = M('TicketType')->where(array('id'=>$param))->cache('ticketName'.$param, 86400)->getField('name');
        if($type){
            return $name;
         }else{
            echo $name;
         }
    }else{
        echo "票型未知";
    }
}
/*单票名称*/

function ticket_single($param,$type=NULL){
    if(!empty($param)){
        $name = M('TicketSingle')->where(array('id'=>$param))->cache('signleName'.$param, 86400)->getField('name');
        if($type){
            return $name;
         }else{
            echo $name;
         }
    }else{
        echo "票型未知";
    }
}
/**
 * 漂流工具类型
 * @param  int $param id
 * @param  int $type  类型
 * @return [type]        [description]
 */
function tooltype($param,$type = null){
    if(!empty($param)){
        $name = M('ToolType')->where(array('id'=>$param))->getField('title');
        if($type){
            return $name;
         }else{
            echo $name;
         }
    }else{
        echo "未知";
    }
}
/*
*获取所有票型
*@param $param 产品id
*/
function getPrice($param){
    if(!empty($param)){
        $list = F('TicketType'.$param);
        if($list){
            $list = M('TicketType')->where(array('status'=>'1'))->select();
        }
        return $list;
    }else{
        return false;
    }
}
/*模板名称*/
function templateName($param){
    if(!empty($param)){
        $name = M('TemplateList')->where(array('id'=>$param))->getField('name');
        echo $name;
    }else{
        return false;
    }
}
/**
 * 座椅显示处理
 * @param $param 座椅iD
 */
function seatShow($param,$type=NULL){
    if(!empty($param)){
        $seta = explode('-', $param);
        $name = $seta['0']."排".$seta['1']."号";
        if($type){
            return $name; 
         }else{
            echo $name;
         }
    }else{
        echo "未知";
    }
}
/*查询座椅所属的订单
* @param $param 座椅iD
* @param $plan_id 计划id
* @param $areaid 区域ID
*/
function seatOrder($param, $plan_id, $area_id = '', $type = NULL){
    if(!empty($param) && !empty($plan_id)){
        $plan = F('Plan_'.$plan_id);
        if(empty($plan)){
            $plantime = strtotime(" -7 day ",strtotime(date('Y-m-d')));
            $plan = M('Plan')->where(array('plantime'=>array('egt',$plantime),'id'=>$plan_id))->field('id,product_type,seat_table')->cache('plan_7'.$plan_id,86400)->find();
        }
        if(empty($plan)){
            $name = "订单已过期";
        }else{
            switch ($plan['product_type']) {
                case '1':
                    $map = array('seat'=>$param,'area'=>$area_id);
                    $table = $plan['seat_table'];
                    break;
                case '2':
                case '4':
                    $map = array('id'=>$param);
                    $table = 'scenic';
                    break;
                case '3':
                    $map = array('id'=>$param);
                    $table = 'drifting';
                    break;
            }
            $info = M(ucwords($table))->where($map)->field('id,group,sort,number,soldtime,sale,middle,price_id',true)->find();
            $checktime = !empty($info['checktime']) ? date('Y-m-d H:i:s',$info['checktime']) : "未检票";
            $idcard = !empty($info['idcard']) ? '<i class="fa fa-cc" data-toggle="tooltip" data-placement="bottom" title="'.$info['idcard'].'"></i> ' : '';
            $name = $idcard.$info['order_sn'].' / '.seat_status($info['status'],1).' / '.$info['print'].' / '.$checktime;
        }
        if($type){
            return $name;
         }else{
            echo $name;
         }
    }else{
        echo "未知";
    }
}
/**
 * 根据ID显示销售计划信息
 * @param $param 计划ID
 * @param $stype 显示方式
 */
function planShow($param,$stype = 1,$type=NULL){
    if(!empty($param)){
        $plan = F('Plan_'.$param);
        if(!empty($plan)){
            $info = $plan;
        }else{
           $info = M('Plan')->where(array('id'=>$param))->field('plantime,games,starttime,endtime,product_type')->cache('plan_show'.$param, 259200)->find(); 
        }
        //判断产品类型
        switch ($info['product_type']) {
            case '1':
                $types = '1'.$stype;
                break;
            case '4':
            case '2':
                $types = '2'.$stype;
                break;
            case '3': 
                $types = '3'.$stype;
                break;
        }
        switch ($types) {
            case '11':
            //完全展示 剧场
                $name = date('Y-m-d',$info['plantime'])."(".get_chinese_weekday($info['plantime']).")". "&nbsp;&nbsp;第".$info['games']."场&nbsp;&nbsp;".date('H:i',$info['starttime'])."-".date('H:i',$info['endtime']);
                break;
            case '12':
                //不显示场次 剧场
                $name = date('Y-m-d',$info['plantime'])."(".get_chinese_weekday($info['plantime']).")".date('H:i',$info['starttime'])."-".date('H:i',$info['endtime']);
                break;
            case '13':
                //不现实场次且简短日期显示 2014-12-16 19:00 剧场 
                $name = date('Y-m-d',$info['plantime'])."&nbsp;&nbsp;".date('H:i',$info['starttime']);
                break;
            case '14':
                //不显示场次 和结束时间 剧场
                $name = date('Y-m-d',$info['plantime'])."(".get_chinese_weekday($info['plantime']).")".date('H:i',$info['starttime']);
                break;
            case '15':
                //简单短信
                $name = date('m月d日',$info['plantime']).date('H:i',$info['starttime']);
                break;    
            case '21':
                //不显示场次 和结束时间
                $name = date('Y-m-d',$info['plantime'])."(".get_chinese_weekday($info['plantime']).")".date('H:i',$info['starttime']);
                break;
            case '24':
                //不显示场次 和结束时间 景区
                $name = date('Y-m-d',$info['plantime'])."(".get_chinese_weekday($info['plantime']).")".date('H:i',$info['starttime']);
                break;
            case '25':
                //只显示日期
                $name = date('Y-m-d',$info['plantime'])."(".get_chinese_weekday($info['plantime']).")";
                break;
            case '26':
                //只显示日期
                $name = date('Y-m-d',$info['plantime'])."(".get_chinese_weekday($info['plantime']).")".date('H:i',$info['starttime'])."-".date('H:i',$info['endtime']);;
                break;
            case '31':
                //不显示场次 和结束时间
                //$name = date('Y-m-d',$info['plantime'])."(".get_chinese_weekday($info['plantime']).")".date('H:i',$info['starttime'])."-".date('H:i',$info['endtime']);
                $starttime = date('H:i',$info['starttime']);
                $start_time = date('H:i',strtotime("$starttime -30 minute"));
                $endtime = date('H:i',$info['endtime']);
                $end_time = date('H:i',strtotime("$endtime -30 minute"));
                $name = date('Y-m-d',$info['plantime'])."(".get_chinese_weekday($info['plantime']).")".$start_time."-".$end_time;
                break;
            case '22':
                //短信发送
                $name = date('m-d',$info['plantime'])."(".get_chinese_weekday($info['plantime']).")";
                break;
            case '32':
                //不显示场次 和结束时间
                $name = date('Y-m-d',$info['plantime'])."(".get_chinese_weekday($info['plantime']).")".date('H:i',$info['starttime']);
                break;
        }
    }else{
        $name = "场次未知";
    }
    if($type){
        return $name;
    }else{
        echo $name;
    }
}
/**
 * 短信发送，演出计划显示
 */
function planShows($param){
    if(!empty($param)){
        $plan = F('Plan_'.$param);
        if(empty($plan)){
            $plan = M('Plan')->where(array('id'=>$param))->field('product_id,plantime,starttime,games,product_type')->cache('plan_show'.$param,259200)->find();
        }
        $proconf = cache('ProConfig');
        $proconf = $proconf[$plan['product_id']][1];
        switch ($plan['product_type']) {
            case '1':
                if($proconf['plan_start_time'] == '1'){
                    $name = date('m月d日',$plan['plantime'])."第".$plan['games']."场,开演时间".date('H:i',$plan['starttime']);
                }else{
                    $name = date('m月d日',$plan['plantime']).date('H:i',$plan['starttime']);
                }
                break;
            case '2':
                $name = date('m月d日',$plan['plantime']);
                break;
            case '3':
                $name = date('m月d日',$plan['plantime']);
                break;
        }
        return $name;
    }else{
        return "场次未知";
    }
}
/**
 * 汉化星期
 */
function get_chinese_weekday($datetime){
    $weekday  = date('w', $datetime);
    $weeklist = array('日', '一', '二', '三', '四', '五', '六');
    return '周' . $weeklist[$weekday];
}

/*
 * 获取取票人名称
 * @param $param int 操作员ID
 */
function crmName($param,$type=NULL){
    if(!empty($param)){
        $name = M('Crm')->where(array('id'=>$param))->getField('name');
    }else{
        $name = "渠道商";
    }
    if($type){
        return $name;
    }else{
        echo $name;
    } 
}
/****================================状态=======================================*******/
    /*状态码
     * 产品状态（0,1）、计划状态(1，2)、订单状态(0,2,3,4,5,6)
     * 0 禁用    作废
     * 1 可用  未授权
     * 2 售票中 未出票 
     * 3 已出票
     * 4 已过期 
     */
    function status($param){
        switch ($param) {
            case 0:
                echo "<span class='label label-danger'>已作废</span>";
                break;
            case 1:
                echo "<span class='label label-info'>正常</span>";
                break;
            case 3:
                echo "<span class='label label-success'>待审核</span>";
                break;
        }
    }
    /*
    * 销售计划状态
    * 1未授权2售票中3暂停销售4已过期
    */
    function plan_status($param){
        switch ($param) {
            case 0:
                echo "<span class='label label-danger'>已作废</span>";
                break;
            case 1:
                echo "<span class='label label-info'>未授权</span>";
                break;
            case 2:
                echo "<span class='label label-success'>售票中</span>";
                break;
            case 3:
                echo "<span class='label label-warning'>暂停中</span>";
                break;
            case 4:
                echo "<span class='label label-default'>已过期</span>";
                break;
        }
    }
    /*
     * 客户分组属性
     * @param $param int 属性id
     */
    function crm_group_type($param){
        switch ($param) {
            case 0:
                echo "未知";
                break;
            case 1:
                echo "企业";
                break;
            case 4:
                echo "个人";
                break;
            case 3:
                echo "政府";
                break;
        }
    }
    
    /*座位状态*/
    function seat_status($param,$type = null){
        switch ($param) {
            case 0:
                $return = "待售";
                break;
            case 2:
                $return = "已售";
                break;
            case 66:
                $return = "预定";
                break;
            case 99:
                $return = "完成";
                break;
        }
        if($type == 1){
            return $return;
        }else{
            echo $return;
        }
    }

    /*
    *@param $cid int 渠道商ID
    *echo  路径信息 
    */
    function itemnav($cid){
        if(!empty($cid)){
            $crm = M('Crm')->where(array('id'=>$cid))->field('id,name,level,f_agents,product_id')->find();
            $Config = cache("Config");
            switch ($crm['level']){
                case $Config['level_1'] :
                    //一级渠道商
                    $return = $crm['name'];
                    break;
                case $Config['level_2'] :
                    //二级级渠道商
                    $return = $crm['name']."/".crmName($crm['f_agents'],1);
                    break;
                case $Config['level_3'] :
                    //三级渠道商  获取二级的上一级ID  
                    $ccid = Libs\Service\Operate::do_read('Crm',0,array('id'=>$crm['f_agents']),'',array('f_agents'));
                    $return = $crm['name']."/".crmName($crm['f_agents'],1)."/".crmName($ccid,1);
                    break;
            }
            echo $return;
        }else{
            echo "未知";
        }
    }
    /**
     * 1正常2为渠道版订单未支付情况3已取消5已支付但未排座6政府订单
     * @param $param
     */
    function order_status($param,$type = null){
        switch ($param) {
            case 0:
                $msg = "已作废";
                $status = "danger";
                break;
            case 1:
                $msg = "预定成功";
                $status = "success";
                break;
            case 2:
                $msg = "待支付";
                $status = "warning";
                break;
            case 3:
                $msg = "已撤销";
                $status = "danger";
                break;
            case 4:
                $msg = "已过期";
                $status = "default";
                break;
            case 5:
                $msg = "待审核";
                $status = "warning";
                break;
            case 6:
                $msg = "待排座";
                $status = "info";
                break;
            case 7:
                $msg = "取消中";
                $status = "primary";
                break;
            case 9:
                $msg = "完结";
                $status = "default";
                break;
            case 10:
                $msg = "部分核销";
                $status = "info";
                break;
            case 11:
                $msg = "窗口待完成";
                $status = "default";
                break;
            
        }
        if($type){
            return $msg;
        }else{
            $return = "<span class='label label-".$status."'>".$msg."</span>";
            echo $return;
        }
    }
    /**
     * 操作类型（1：充值；2：花费3:返佣4：退票 5:退款）
     */
    function operation($param,$type = null){
        switch ($param) {
            case 1:
                $msg = "充值";
                $status = "success";
                break;
            case 2:
                $msg = "花费";
                $status = "info";
                break;
            case 3:
                $msg = "补贴";
                $status = "warning";
                break;
            case 4:
                $msg = "退票";
                $status = "danger";
                break;
            case 5:
                $msg = "退款";
                $status = "primary";
                break;
            
        }
        if($type){
            return $msg;
        }else{
            $return = "<span class='label label-".$status."'>".$msg."</span>";
            echo $return;
        }
    }
    /**
     * 返利状态（1写入2出票成功3财务审核成功4返佣发放成功）
     */
    function rebate($param){
        switch ($param) {
            case 4:
                echo "<span class='label label-success'>补贴成功</span>";
                break;
            case 2:
                echo "<span class='label label-info'>出票成功</span>";
                break;
            case 3:
                echo "<span class='label label-warning'>审核成功</span>";
                break;
            case 1:
                echo "<span class='label label-default'>下单成功</span>";
                break;
            
        }
    }
    /*退票状态*/
    function refund_status($param,$type = null){
        switch ($param) {
            case 1:
                $msg = "申请成功";
                $status = "success";
                break;
            case 2:
                $msg = "驳回";
                $status = "primary";
                break;
            case 3:
                $msg = "退票成功";
                $status = "warning";
                break;       
        }
        if($type){
            return $msg;
        }else{
            $return = "<span class='label label-".$status."'>".$msg."</span>";
            echo $return;
        }
    }
    /*剧院产品根据场次获取各区域可售座位数
    *@param $table string 表名称
    *@param $map array 查询条件
    *@param $type 
    */
    function area_count_seat($table,$map,$type = null){  
        if(!empty($table) && !empty($map)){
            $num = M(ucwords($table))->where($map)->count();
            if($type){
                return $num;
            }else{
                echo $num;
            }
        }else{
            return false;
        }
    }
   /* 返回场次*/
    function games($param,$type=NULL){
        if(!empty($param)){
            $games = M('Plan')->where(array('id'=>$param))->getField('games');
            if($type){
                return $games;
            }else{
                echo "第".$games."场";
            }
        }else{
            echo "未知";
        }
    }
    /**
     * 小商品名称
     * @param  int $param 商品ID
     * @param  int $type  返回类型
     * @return [type]        [description]
     */
    function goodsInfo($product_id, $field = '', $param, $type = NULL){
        $goodsList = F('Goods_'.$product_id);
        if(empty($goodsList)){
            D('Item/Goods')->goods_cache($product_id);
        }
        if(empty($field)){
            $return = $goodsList[$param];
        }else{
            $return = $goodsList[$param][$field];
        }
        if($type){
            return $return;
        }else{
            echo $return;
        } 
    }
    /**==========================================================用于系统内部回调==========================================================================****/
    /**
     * 得到新订单号
     * @param $planid int 销售计划id
     * @param $checkType int 检票类型 1 一人一票 2一团一票
     * @return  string
     */
    function get_order_sn($planid,$checkType = 1,$ticket_type = 1){
        $sn = load_redis('rPop','sn_library');
        if(!$sn){
            $date = substr(date('Ymd'),3);
            if((int)substr($date, 1, 1) === 0){
                $date = '8'.substr(date('Ymd'),4);
            }
            $sn = $date.$checkType. $planid. str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        }
        return $sn;
    }
    /**
     * 根据订单号判断订单有效性
     * @param $sn char 订单号
     */
    function check_sn($sn,$plan_id){
        if(empty($sn)){
            return false;
        }
        $sns = substr($sn,0,5);
        if($sns <= '60504'){
            return 1;
        }else{
            //获取场次id
            $planid =  substr($sn,6,-5);
            //验证场次状态
            $plan = M('Plan')->where(array('id'=>$plan_id))->field('product_id,status')->find();
            $proconf = cache('ProConfig');
            $proconf = $proconf[$plan['product_id']]['1'];
            if($proconf['print_overdue'] == '1'){
                $map = ['2','3','4'];
            }else{
                $map = ['2','3'];
            }
            
            if(in_array($plan['status'],$map)){
                return true;
            }else{
                return false;
            }
        }
    }
    /**
     * 获取产品名称
     * @param $param int 产品ID
     */
    function productName($param){
        if(!empty($param)){
            return M('Product')->where(array('id'=>$param))->getField('name');
        }else{
            return "未知";
        }   
    }
    /**
     * 获取订单创建场景
     * @param $param int 
     */
    function addsid($param,$type = null){
        switch($param){
            case 1:
                $return = "窗口";
                break;
            case 2:
                $return = "渠道版";
                break;
            case 3:
                $return = "网站";
                break;
            case 4:
                $return = "微信";
                break;
            case 5:
                $return = "API";
                break;
            case 6:
                $return = "窗口";
                break;
            case 7:
                $return = "自助设备";
                break;
            default:
                $return = "未知场景";
                break;
        } 
        if($type){
            return $return;
        }else{
            echo $return;
        }   
    }
    /*
    * 校验订单号长度
    * @param $sn 单号 长度为14
    * return true false
    */
    function sn_length($sn = ''){
        if(empty($sn)){return false;}
        if(strlen($sn) > '6'){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 支付方式
     * @param $param int 支付方式
     */
    function pay($param,$type = NULL){
        switch($param){
            case 1:
                $return = "现金";
                break;
            case 2:
                $return = "授信额";
                break;
            case 3:
                $return = "签单";
                break;
            case 4:
                $return = "支付宝";
                break;
            case 5:
                $return = "微信支付";
                break;
            case 6:
                $return = "划卡";
                break;
            case 7:
                $return = "汇款";
                break;
            case 8:
                $return = "支票";
                break;
            case 9:
                $return = "转账";
                break;
            default:
                $return = "未知";
                break;
        }
        if($type){
            return $return;
        }else{
            echo $return;
        }
        
    }
    /**
     * 企业付款方式 1打卡2支付宝转账3财务取现4微信企业转账
     * @param  [type] $param [description]
     * @param  [type] $type  [description]
     * @return [type]        [description]
     */
    function pay_type($param,$type = null){
        switch($param){
            case 1:
                $return = "银行卡转账";
                break;
            case 2:
                $return = "支付宝转账";
                break;
            case 3:
                $return = "财务取现";
                break;
            case 4:
                $return = "微信企业转账";
                break;
        }
        if($type){  
            return $return;
        }else{
            echo $return;
        }
    }
     /**
     * 销售渠道
     * @param $param int 销售渠道
     */
    function channel_type($param,$type = null){
        switch($param){
            case 1:
                $return = "散客";
                break;
            case 2:
                $return = "团队";
                break;
            case 4:
                $return = "渠道商";
                break;
            case 6:
                $return = "政企";
                break;
            case 7:
                $return = "预约";
                break;
            case 8:
                $return = "全员";
                break;
        }
        if($type){  
            return $return;
        }else{
            echo $return;
        }
    }
    /**
     * 金额扣除条件
     * @param $param 渠道商ID
     * @param $type 2公司客户8全员销售
     * @param $channel_id int 渠道商
     */
    function money_map($param,$type = '1'){
        if(!empty($param)){
            if($type == '1'){
                $param = D('Crm')->where(array('id'=>$param))->field('id,level,f_agents')->find();
                /*
                 * 读取上级渠道商的ID
                 */
                $Config = cache("Config");
                switch ($param['level']){
                    case $Config['level_1'] :
                        //一级渠道商
                        return $param['id'];
                        break;
                    case $Config['level_2'] :
                        //二级级渠道商
                        return $param['f_agents'];
                        break;
                    case $Config['level_3'] :
                        //三级渠道商  获取二级的上一级ID  
                        $cid = Libs\Service\Operate::do_read('Crm',0,array('id'=>$param['f_agents']),'',array('f_agents'));
                        return $cid['f_agents'];
                        break;
                }
            }
            if($type == '4'){
                $db = D('User');$field = 'id';
                $param = D('User')->where(array('id'=>$param))->field('id')->find();
                return $param['id'];
            }
        }else{
            return false;
        }        
   }
   /*根据任一渠道id  获取该渠道商完整的层级关系
    *@param $param int 客户id
   */
   function hierarchy($param,$type = null){
        if(!empty($param)){
            $db = M('Crm');
            $param = $db->where(array('id'=>$param))->field('id,f_agents,level,name')->cache(true)->find();
            /*
             * 读取上级渠道商的ID
             */
            $Config = cache("Config");
            switch ($param['level']){
                case $Config['level_1'] :
                    //一级渠道商
                    $return = $param['name'];
                    break;
                case $Config['level_2'] :
                    //二级级渠道商
                    $return = crmName($param['f_agents'],1)." -> ".$param['name'];
                    break;
                case $Config['level_3'] :
                    //三级渠道商  获取二级的上一级ID  
                    $cid = $db->where(array('id'=>$param['f_agents']))->field('f_agents,name')->find();
                    $return = crmName($cid['f_agents'],1)." -> ".$cid['name']." -> ".$param['name'];
                    break;
            }
        }else{
            $return = "未知";
        }
        if($type){
            return $return;
        }else{
            echo $return;
        } 
   }
   /*获取渠道商余额
    *@param $param int 渠道商ID 1企业 4个人
   */
    function balance($param,$type = '1'){
        if(empty($param)){return '0.00';}
        if(in_array($type, ['1','3'])){
            //渠道商客户
            $db = M('Crm');
        }
        if($type == '4'){
            //个人客户
            $db = M('User');
        }
        $return = $db->where(['id'=>$param])->getField('cash');
        //判断是否达到最低额
        $config = cache('Config');
        if(bccomp($return, $config['money_low']) > 0){
            //写入待处理事项
            load_redis('lpush','preEvent','money_low|'.$param);
        }
        return $return;
    }
   /**
    * 获取渠道商集合
    * @param $param int id
    * @param $type int 1 员工ID  2 渠道商id
    */
   function channel_set($param,$type = 1){
        if($type == '1'){
            $crm_id = M('User')->where(array('id'=>$param))->getField('cid');
        }else{
            $crm_id = $param;
        }
        $crm = M('Crm')->where(array('id'=>$param))->find();
        $Config = cache("Config");
        switch ($crm['level']){
            case $Config['level_1'] :
                //一级渠道商
                $channel = M('Crm')->where(array('f_agents'=>$crm['id'],'status'=>'1'))->field('id')->select();
                $channel_id = implode(',', array_column($channel, 'id'));
                return $channel_id;
                break;
            case $Config['level_2'] :
                //二级级渠道商
                $channel = M('Crm')->where(array('f_agents'=>$crm['id'],'status'=>'1'))->field('id')->select();
                $channel_id = implode(',', array_column($channel, 'id'));
                return $channel_id;
                break;
            case $Config['level_3'] :
                //三级渠道商  获取二级的上一级ID
                return $crm['id'];
                break;
        }
   }
   /*在开启代理商制度时，根据一级渠道商查询所有渠道商id集合
   * @param $param int id
    * @param $type int 1 员工ID  2 渠道商id
    *
   */
   function agent_channel($param,$type = 1){
        if($type == '1'){
            $crm_id = M('User')->where(array('id'=>$param))->getField('cid');
        }else{
            $crm_id = $param;
        }
        $crm = M('Crm')->where(array('id'=>$param))->find();
        $u_1 = $crm['id'];
        $u_2 = M('Crm')->where(array('f_agents'=>$crm['id']))->field('id')->select();
        if($u_2){
            $arr_map_2 = implode(',',array_column($u_2,'id'));//转换为一维数组
            $u_3 = M('Crm')->where(array('f_agents'=>array('in',$arr_map_2)))->field('id')->select();
            $arr_map_3 = implode(',',array_column($u_3,'id'));//转换为一维数组
        }
        if(empty($u_2)){
            $arr_map = $u_1;
        }
        if (!empty($u_2) && empty($u_3)) {
            $arr_map = $u_1.','.$arr_map_2;
        }elseif(!empty($u_2) && !empty($u_3)){
            $arr_map = $u_1.','.$arr_map_2.','.$arr_map_3;
        }
        return $arr_map;
   }
   /**
     * 获取渠道商
     * @param $channel_id int 渠道商ID
     * @param $level int 代理商级别
     */
    function channel($channel_id,$level){
        //$Config = cache("Config");
        switch ($level){
            case '16' :
                //一级渠道商
                //获取一级渠道商所有人员ID
                $u_1 = $channel_id;
                $u_2 = M('Crm')->where(array('f_agents'=>$channel_id,'status'=>1))->field('id')->select();
                if($u_2){
                    $arr_map_2 = implode(',',array_column($u_2,'id'));//转换为一维数组
                    $u_3 = M('Crm')->where(array('f_agents'=>array('in',$arr_map_2),'status'=>1))->field('id')->select();
                    $arr_map_3 = implode(',',array_column($u_3,'id'));//转换为一维数组
                }
                if(empty($u_2)){
                    $arr_map = $u_1;
                }
                if (!empty($u_2) && empty($u_3)) {
                    $arr_map = $u_1.','.$arr_map_2;
                }elseif(!empty($u_2) && !empty($u_3)){
                    $arr_map = $u_1.','.$arr_map_2.','.$arr_map_3;
                }
                return $arr_map;
                break;
            case '17' :
                //二级级渠道商
                $u_2 = $channel_id;
                $u_3 = M('Crm')->where(array('f_agents'=>$channel_id,'status'=>1))->field('id')->select();
                $arr_map_3 = implode(',',array_column($u_3,'id'));//转换为一维数组
                if(empty($arr_map_3)){
                    $arr_map = $u_2;
                }else{
                    $arr_map = $u_2.','.$arr_map_3;
                }
                return $arr_map;
                break;
            case '18' :
                //三级渠道商  获取二级的上一级ID  
                $arr_map = $channel_id;
                return $arr_map;
                break;
        }
    }
    /*获取渠道商所有员工
    @param $channel_id array 渠道商id集合
    return $user_id array 用户id*/
    function channel_user($channel_id,$level){
        $Config = cache("Config");
        switch ($level){
            case $Config['level_1'] :
                //一级渠道商
                //获取一级渠道商所有人员ID
                $u_1 = $channel_id;
                $u_2 = M('Crm')->where(array('f_agents'=>$channel_id,'status'=>1))->field('id')->select();
                if($u_2){
                    $arr_map_2 = implode(',',array_column($u_2,'id'));//转换为一维数组
                    $u_3 = M('Crm')->where(array('f_agents'=>array('in',$arr_map_2),'status'=>1))->field('id')->select();
                    $arr_map_3 = implode(',',array_column($u_3,'id'));//转换为一维数组
                }
                $arr_map = $u_1.','.$arr_map_2.','.$arr_map_3;
                $user_l = M('User')->where(array('cid'=>array('in',$arr_map),'status'=>1))->field('id')->select();
                $user = implode(',',array_column($user_l,'id'));
                return $user;
                break;
            case $Config['level_2'] :
                //二级级渠道商
                $u_2 = $channel_id;
                $u_3 = M('Crm')->where(array('f_agents'=>$channel_id,'status'=>1))->field('id')->select();
                $arr_map_3 = implode(',',array_column($u_1,'id'));//转换为一维数组
                $arr_map = $u_2.','.$arr_map_3;
                $user_l = M('User')->where(array('cid'=>array('in',$arr_map),'status'=>1))->field('id')->select();
                $user = implode(',',array_column($user_l,'id'));
                return $user;
                break;
            case $Config['level_3'] :
                //三级渠道商  获取二级的上一级ID  
                $arr_map = $channel_id;
                $user_l = M('User')->where(array('cid'=>array('in',$arr_map),'status'=>1))->field('id')->select();
                $user = implode(',',array_column($user_l,'id'));
                return $user;
                break;
        }
    }
    /**
     * @Company  承德乐游宝软件开发有限公司
     * @Author   zhoujing      <zhoujing@leubao.com>
     * @DateTime 2018-05-02
     * @param    int        $channel_id           获取指定渠道商下级
     * @return   获取下级代理商
     */
    function getChild($channel_id)
    {
        $crmid = M('Crm')->where(['f_agents'=>$channel_id])->field('id')->select();
        return $crmid;
    }
    /**
     * 根据级别查询所属级别的渠道商
     * @param  int $level 级别
     * @return 返回渠道商集合 16-5-25
     */
    function channel_level($level){
        if(empty($level)){return false;}
        $list = M('Crm')->where(array('level'=>$level,'status'=>1))->field('id')->select();
        return array_column($list,'id');
    }
    //递归查找上级分销商链条
    function crm_level_link($id)
    {
        $arr[] = $id;
        $crm = getChannel($id);
        while ($crm['f_agents'] > 0) {
            $crm = getChannel($crm['f_agents']);
            $arr[] = $crm['id'];
        }
        return $arr;
    }
    //获取单体渠道商信息
    function getChannel($param ='')
    {
        if(!empty($param)){
            return D('Crm')->where(array('id'=>$param))->field('id,level,f_agents')->find();
        }else{
            return false;
        }

    }
   /*获取二次打印授权人
   * @param 
    */
    function pwd_name($param,$type = ''){
        if(!empty($param)){
            $name = M('Pwd')->where(array('id'=>$param))->getField('name');
            if($type){
                return $name;
            }else{
                echo $name;
            }
        }else{
            echo "未知";
        }
    }
    //价格政策
    function price_group($param,$type = null){
        if(!empty($param)){
            $name = M('TicketGroup')->where(array('id'=>$param))->getField('name');
            if($type){
                return $name;
            }else{
                echo $name;
            }
        }else{
            echo "未知";
        }
    }
    /*
    * 库存查询
    * @param $plan_id int 计划id
    * @param $area int 区域id
    */
    function sku($plan_id = null, $area = null){
        if(empty($plan_id) || empty($area)){return false;}
        $plan = F('Plan_'.$plan_id);
        if(empty($plan)){return false;}
        $count = M(ucwords($plan['seat_table']))->where(array('area'=>$area,'status'=>'0'))->count();
        return $count;
    }
    /*根据订单号获取座位信息
    * @param $sn 订单SN*/
    function sn_seat($sn){
      if(empty($sn)){return false;}
      $plan_id = M('Order')->where(array('order_sn'=>$sn))->getField('plan_id');
      $plan = F('Plan_'.$plan_id);
      if(empty($plan)){return false;}
      $list = M(ucwords($plan['seat_table']))->where(array('order_sn'=>$sn))->field('area,seat')->select();
      foreach ($list as $k => $v) {
        $info[] = areaName($v['area'],1).seatShow($v['seat'],1);
      }
      return $info;
    }
    /*根据计划id获取场次详情
    *@param $param int  计划ID
    *@param $type 返回类型 0 echo 1 return
    */
    function plan_info($param,$type = null){
        if(empty($param)){return false;}
        $plan = F('Plan_'.$param);
        $area = unserialize($plan['param']);
        foreach ($area['seat'] as $k => $v) {
            $return = areaName($v,1)."剩余";
        }
        if($type){
            return $name;
        }else{
            echo $name;
        }  
    }
    /*
    * 从订单详情中获取座位区域信息
    * @param $info string 订单详情
    */
    function order_area($info){
        if(empty($info)){return false;}
        $data = unserialize($info);
        return $data['data'];
    }
    /*获取重组区域重组价格 
    * @param $param array 参数
    * @param $price_group int 所属价格分组
    * @param $table string 座位表名称
    * @param $scene int 销售场景 根据销售场景设置销售数量
    */
    function area_price($param,$table,$price_group,$scene,$ticket = array()){
        if(empty($ticket)){
            $ticket = $param['ticket'];
        }
        foreach ($param['seat'] as $key => $value) {
            $price = price($value,$price_group,$scene,$ticket);
            if($price){
                foreach ($price as $k => $v) {
                    $remark = print_remark($v['remark'],$v['product_id']);
                    $seat[] = array(
                        'area'=>$value,
                        'name'=>areaName($value,1),
                        'pricename'=>$v['name'],
                        'priceid'=>$v['priceid'],
                        'money'=>$v['money'],
                        'moneys'=>$v['moneys'],
                        'remark'=>$remark == '0' ? '' : $remark['remark'][0],
                        'num' => area_count_seat($table,array('area'=>$value,'status'=>array('in','0')),1),
                    );
                }
            }
        }
        
        return $seat;
    }
    /*
    * 获取价格政策
    * @param $area 区域id
    * @param $price_group int价格分组
    * @param $type int 渠道类型,$type TODO  可根据当前用户类型  选择票型类型'type'=>$type,
    * @param $ticket array  当前场次允许的票型
    */
    function price($area,$price_group,$scene,$ticket){
        $map = array('status'=>'1','area'=>$area, 'group_id'=>array('in', $price_group),'id'=>array('in',implode(',',$ticket)));
        $map['_string']="FIND_IN_SET(".$scene.",scene)";
        $list = M('TicketType')->where($map)->field(array('id'=>'priceid','price'=>'money','discount'=>'moneys','name','product_id','remark'))->select();
        return $list;
    }
    /*格式化备注 用于打印
    *@param $remark string 
    *return array
    */
    function print_remark($remark,$product_id){
        if(empty($remark)){$return = 0;}
        $proconf = cache('ProConfig');
        $proconf = $proconf[$product_id][1];
        if($proconf['print_remark'] == '1'){
            $data = explode('|',$remark);
            foreach ($data as $k => $v) {
                if($k == 0){
                    $return['remark_type'] = $v ? $v : '';
                }else{
                    $return['remark'][] = $v ? $v : '';
                }
            }
        }else{$return = '0';}
        return $return;
    }
    /*判断演出场次是否有效
     * 时间场次验证
     */
    function check_plan(){
        //获取系统日期
        $datetime = date('Ymd');
        $model = D('Item/Plan');
        //获取要检测的场次
        $list = $model->where(array('status'=>array('in','2,3'),'plantime'=>array('elt',strtotime($datetime))))->select();
        $itemConf = cache('ItemConfig');
        
        foreach ($list as $k => $v) {
            $itemid = D('Product')->where(['id'=>$v['product_id']])->cache(true)->getField('item_id');
            
            load_redis('set','check_plan',date('Y-m-d H:m:i').'['.$itemConf[$itemid]['1']['send_msg'].']'.json_encode($itemConf));
            //计划日期
            $plantime = date('Ymd',$v['plantime']);
            if($plantime < $datetime){
                //发送演出销售信息
                F('Plan_'.$v['id'],NULL);
                //停用已过期场次
                $status = $model->where(array('id'=>$v['id']))->setField('status',4);
                if((int)$itemConf[$itemid]['1']['send_msg'] === 1){
                    send_sms($v['id']);
                }
                //销毁过期的数据
                $model->destroyed($v['product_id'],$v['id']);
                //TODO   写入重要提醒队列 如未取票
                return $status;
            }else{
                //判断时间
                if(date('H',$v['endtime']) == '00'){
                    $etime = '24'.date('i',$v['endtime']);
                }else{
                    $etime = date('Hi');
                }
                /*演出结束时间
                if(date('H',$v['endtime']) == '00'){
                    $endtime = '24'.date('i',$v['endtime']);
                }else{
                    $endtime = date('Hi',$v['endtime']);
                }*/
                $endtime = date('Hi',$v['endtime']);
                if($etime > $endtime){
                    //停用已过期场次
                    F('Plan_'.$v['id'],NULL);
                    $status = $model->where(array('id'=>$v['id']))->setField('status',4);

                    if((int)$itemConf[$itemid]['1']['send_msg'] === 1){
                        send_sms($v['id']);
                    }
                    //销毁过期的数据
                    $model->destroyed($v['product_id'],$v['id']);
                    return $status;
                }
            }
        }
    }
    /*
    * 判断该场次是否在可售时间内  开始演出停止渠道商售票
    * @param $plan_id int 演出计划id
    */
    function if_plan($plan_id = null){
        $plan = F('Plan_'.$plan_id);
        if(empty($plan_id) || empty($plan)){return false;}
        $datetime = date('Ymd');
        $proconf = cache('ProConfig');
        $proconf = $proconf[$plan['product_id']][1];
        if(!empty($proconf['channel_time'])){
            $time = date('Hi',strtotime($proconf['channel_time']." minute"));
            if(date('H') == '00'){
                $time = '01'.date('i');
            }
        }else{
            $time = date('H');
            if($time == '00'){
                $time = '00'.date('i');
            }else{
                $time = date('Hi',time());
            }
        }
        $plantime = date('Ymd',$plan['plantime']);
        if($plantime == $datetime){
            if($plan['product_type'] == '1'){
                $starttime = date('Hi',$plan['starttime']);
            }else{
                $starttime = date('Hi',$plan['endtime']);
            }
            if($time >= $starttime){
                return false;
            }else{
                return true;
            }
        }else{
            return true;
        }
    }
    /**
     * 根据日期获取场次信息
     * @param  string $date 日期
     * @param  int $games 场次
     * @param  int $type 类型 1 全部列表 2 指定场次 
     * @param  int $status 是否可用
     * @return TODO 获取列表
     */
    function get_date_plan($date, $games = '1', $status = '', $product_id = '', $type = '1'){
        $datetime = strtotime($date);
        //构造条件
        $map = array();
        $map['plantime'] = $datetime;
        if(!empty($product_id)){
            $map['product_id'] = $product_id;
        }
        if(!empty($status)){
            $map['status'] = $status;
        }
        if($type == '1'){
            $plan = M('Plan')->where($map)->field('id')->select();
        }else{
            $map['games'] = $games;
            $plan = M('Plan')->where($map)->field('id')->find(); 
        }
        return $plan;
    }
    /**
     * 根据票型获取区域
     * 但一票型只属于单一区域
     * @param  int $ticket 票型
     * @param  int $type 类型
     * @param  int $product 产品id
     * @return int         
     */
    function get_ticket_area($ticket, $product, $type = ''){
        $ticket_type = F("TicketType".$product);
        $tType = $ticket_type[$ticket];
        return $tType['area'];
    }
    /*
    * 演出结束发送该场次销售情况
    * @param $plan_id int 演出计划id
    */
    function send_sms($plan_id = null){
        //查询销售计划
        if(!empty($plan_id)){
            $plan = M('Plan')->where(array('id'=>$plan_id))->field('id,plantime,seat_table,param,product_type,product_id')->find();
            \Libs\Service\Leadersms::send_sms($plan);
        }
    }
    /*
    *获取订单最后一位打票员
    *@param $sn  订单号
    *return 出票员以及出票时间
    */
    function print_ticket_user($sn = null){
        if(empty($sn)){echo "未找到订单";}else{
            $info = M('PrintLog')->where(array('order_sn'=>$sn))->field('uid,user_id,type')->order('createtime DESC')->find();
            if(empty($info)){
                echo "未找到订单";
            }else{
                switch ($info['type']) {
                    case '1':
                        echo userName($info['uid'],1,1);
                        break;
                    case '2':
                        echo userName($info['uid'],1,1)." || 授权用户:".pwd_name($info['user_id'],1);
                        break;
                    case '3':
                    //TODO  自助设备编号
                        echo '自助设备';
                        break;
                }
            }
        }
    }
    /* 查询当前用户所属渠道商的分组类型
    *当渠道商不为空时，按渠道商算，渠道商为空是按导游算
    *@param $product_id int 产品ID
    * $crm_id 渠道商id
    * $gudie 导游id  全民销售
    */
    function google_crm($product_id,$crm_id = '',$gudie = ''){
        $crm = F('Crm');$crmGroup = F('CrmGroup');
        if(!empty($crm_id)){
            $return = $crm[$crm_id];
        }else{
            $return['groupid'] = M('User')->where(array('id'=>$gudie))->getField('groupid');
        }
        $return['group'] = $crmGroup[$return['groupid']];
        return $return;
    }
    /*
    * @param $sn 单号
    * 判断打印订单类型
    */
    function order_type($sn){
        $type = M('Order')->where(array('order_sn'=>$sn))->field('status,pay,type,user_id,plan_id,channel_id,is_print')->find();
        return $type;
    }
    /*
    返回打印数据
    $plan_id 计划id
    $encry 加密常量
    $data 待处理的数据
    */
   /**
    * @Author   zhoujing                 <zhoujing@leubao.com>
    * @DateTime 2020-01-19T17:58:37+0800
    * @param    int                   $plan_id              销售计划
    * @param    string                   $encry                加密常量
    * @param    array                   $data                 门票数据
    * @param    int                   $orderId              订单id
    * @param    integer                  $team                 1一人一票2一单一票
    * @return   array
    */
    function re_print($plan_id, $encry, $data, $product_id = '', $orderId = '', $team = 1){
        $plan = F('Plan_'.$plan_id);
        $proconf = cache('ProConfig');
        $proconf = $proconf[$plan['product_id']][1];
        $print = $data['print']+1;
        $sn = \Libs\Service\Encry::toQrData($data['id'],$orderId,$plan_id,$print,$team);
        
        //是否启用qr_url
        if(!empty($proconf['qr_url'])){
            $sn = $proconf['ticket_url'].$sn;
        }
        //条码号
        if($proconf['barcode'] == '1'){
            $barcode = $data['area'].\Libs\Service\Encry::seat_fold($data['seat']).$plan_id;
            $barcode = "A".str_pad($barcode,12,0,STR_PAD_LEFT);
            $code = array('id' => $data['id'],'plan' => $plan_id,'sn' => $sn,'barcode'=>$barcode,'sns'=>$data['order_sn']);
        }else{
            $code = array('id' => $data['id'],'plan' => $plan_id,'sn' => $sn,'sns'=>$data['order_sn']);
        }
        //打票员名称
        if($proconf['print_user'] == '1'){
            $info['user'] = \Manage\Service\User::getInstance()->username; 
        }
        //渠道商简码
        if($proconf['print_channel_code'] == '1'){

        }
        //入场时间
        if($proconf['print_field'] == '1'){
            $statrtime = date('H:i',$plan['starttime']);
            $end = date('H:i',strtotime("$statrtime -5 minute"));
            $start = date('H:i',strtotime("$end -30 minute"));
            $info['field'] = $start .'-'. $end;
        }else{
            $info['operating'] = date('H:i',$plan['starttime']) .'-'. date('H:i',$plan['endtime']);
        }
        //运营时间
        
        $info['number'] = 1;
        if(empty($info)){
            $info = array_merge($code,unserialize($data['sale']));
        }else{
            $info = array_merge($code,unserialize($data['sale']),$info);
        }
        return $info;
    }
    /**
     * 门票打印日志
     * @param $sn int 订单号
     * @param $user int 二次授权员工
     * @param $type int 1一次打印2二次打印
     * @param $channel_id 渠道商ID
     * @param $remark 备注
     * @param $num 打印门票数量
     * @param $scene 打印场景
    */
    function print_log($sn,$user = null,$type = '1',$channel_id = null,$remark = null,$num = null,$scene = null){
        M('PrintLog')->add(array('order_sn'=>$sn,
            'uid'=>get_user_id(),
            'ip'=>get_client_ip(),
            'user_id'=>$user ? $user : '0',
            'type' => $type,
            'createtime'=>time(),
            'status'=>'1',
            'channel_id'=>$channel_id ? $channel_id : 0,
            'remark' => $remark,
            'number' => $num,
            'scene' =>  $scene));
        return true;
    }
    /**
     * 列表时间格式化
     * @param $param 待处理数据
     * @param $type 1 时间戳转日期 2日期转时间戳
    */
    function datetime($param,$type = '1'){
        if($type == '1'){
            echo date('Y-m-d H:i:s',$param);
        }else{
            return strtotime($param);
        }
    }
   /*####################################报表*/
    /*返回当前登录用户*/
    function get_user_id(){
        $userid = \Libs\Util\Encrypt::authcode($_SESSION['lub_userid'], 'DECODE');
        if(empty($userid)){
            $userid = \Libs\Util\Encrypt::authcode($_SESSION['lub_uid'], 'DECODE');
        }
        return $userid;
    }
    /**
     * 返回当前用户的商户信息或根据产品ID 返回商户详情
     * @param  string $type       id  或 info
     * @param  int $product_id 产品id
     * @return int || array 
     */
    function get_item($type = 'id',$item_id = '')
    {
        if(empty($item_id)){
           $item = \Libs\Util\Encrypt::authcode($_SESSION['lub_imid'], 'DECODE');
            if($type == 'info'){
                $item = M('Item')->where(array('id'=>$item))->find();
            }
        }else{
            $item = M('Item')->where(array('id'=>$item_id))->find();
        }    
        return $item;
    }
    /**
     * 获取当前商户配置信息
     * @Company  承德乐游宝软件开发有限公司
     * @Author   zhoujing      <zhoujing@leubao.com>
     * @DateTime 2018-03-07
     * @param    int        $param                配置类型
     * @param    int        $itemid               商户ID
     * @return   array
     */
    function get_item_conf($param = '1',$item_id = '')
    {
        if(empty($item_id)){
            $item_id = get_item('id');
        }
        $itemCof = cache('ItemConfig');
        if(empty($itemCof)){D('Common/Config')->config_cache();}
        return $itemCof[$item_id][$param];
    }
    /**
     * 返回当前产品或根据产品ID 返回产品详情
     * @param  string $type       id  或 info
     * @param  int $product_id 产品id
     * @return int || array 
     */
    function get_product($type = 'id',$product_id = ''){
        if(empty($product_id)){
            $product = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');
            if($type == 'info'){
                $product = M('Product')->where(array('id'=>$product))->find();
                $product['param'] = unserialize($product['param']);
            }
        }else{
            $product = M('Product')->where(array('id'=>$product_id))->find();
            $product['param'] = unserialize($product['param']);
        }
        /* 默认读取缓存 TODO
        $product = F('Product')
        if(){

        }*/
        return $product;
    }
    /*
    * @param $param int 根据管理员获取所管理的商户和导游
    */
    function get_crm_guide($param){
        //获取商户
        $channel = M('Crm')->where(array('salesman'=>$param))->field('id')->select();
        $info['channel'] = implode(array_column($channel,'id'), ',');
        //获取导游
        $guide = M('User')->where(array('salesman'=>$param))->field('id')->select();
        $info['guide'] =implode(array_column($guide,'id'), ',');
        return $info;
    }
    /*记录错误日志*/
    function error_insert($code){
        $status = M('Error')->add(array(
                'code' => $code,
                'user_id' =>  get_user_id(), 
                'createtime' => time(),
            ));
        return true;
    }
    //获取0元票票型
    function zero_ticket(){
        $list = M('TicketType')->where(array('discount'=>'0'))->field('id')->select();
        return arr2string($list,'id');
    }
    /**
     * 获取当前系统中正常售票的销售计划 
    */
    function normal_plan(){
        $list = M('Plan')->where(array('status'=>2))->field('id')->order('plantime ASC,games ASC')->select();
        return arr2string($list,'id');
    }
    /**
     * 获取当天的演出场次
     */
    function get_today_plan(){
        $today = strtotime(date('Ymd'));
        $plan = M('Plan')->where(array('plantime'=>array('egt',$today)))->field('id')->select();
        return $plan;
    }
    //返回默认搜索日期
    function getSearchToday()
    {
        $today = array(array('EGT', strtotime(date('Y-m-d'))), array('ELT', strtotime(date('Y-m-d', strtotime('+1 day')))), 'AND');
        return $today;
    }
    /*通用价格拉取
     * @param $planid 销售计划
     * @param $type int 读取票型类型 1、获取散客票 2、获取团队 3、散客团队 4、获取政企  6、活动票型 9、获取所有票型
     * @param $area int 区域
     * @apram $scene int 销售场景
     * @param $group string 票型分组 支持多个分组
     * @param $seale string 1根据区域获取可售票型,2获取当前计划所有可售票型 注意 只在剧场模式下有效
     * @param $sealeTicket array 特殊方式限定销售票型 比如活动
    */
    function pullprice($planid, $type, $areaId, $scene, $group = null, $seale = '1', $sealeTicket = []){
        switch ($type) {
            case '1':
                $types = array('in','1,3,4,5,6,7,8');
                break;
            case '2':
                $types = array('in','2,3,4,5,6,7,8');
                break;
            case '3':
                $types = array('in','1,2,3');
                break;
            case '4':
                $types = array('in','3,4');
                break;
            case '5':
                //联票支持
                $types = array('in','1,3,4');
                break;
            case '9':
                $types = array('in','1,2,3,4,5,6,7,8');
                break;
            default:
                $types = array('in','1,3,4');
                break;
        }
        $plan = F('Plan_'.$planid);
        $map = []; $area = [];
        if(empty($plan)){return false;}
        if($plan['product_type'] <> '1'){
            $where = array('plan_id'=>$planid,'product_id'=>$plan['product_id'],'status'=>array('in','2,99,66'));
        }
        //可售区域 及授权票型
        //TODO  当销售场景超过9种时存在问题，更正模糊搜索
        $param = unserialize($plan['param']);
        switch ($plan['product_type']) {
            case '1':
                //获取当前可售数量
                $table = ucwords($plan['seat_table']);
                if($seale == '1'){
                    $area_num = area_count_seat($table,['area'=>$va['area'],'status'=>'0'],1);;
                    $area_nums = area_count_seat($table,['area'=>$va['area'],'status'=>'2'],1);;
                    $area = array('area' => $areaId);
                }
                break;
            case '2':
                $number = D('Scenic')->where($where)->count();
                $area_num = $plan['quotas'] - $number;
                $area_nums = $number;
                break;
            case '3':
                $number = D('Drifting')->where($where)->count();
                //获取当前可售数量
                $area_num = $plan['quotas'] - $number;
                $area_nums = $number;
                break;
            case '4':
                $number = D('Scenic')->where($where)->count();
                $area_num = $plan['quotas'] - $number;
                $area_nums = $number;
                break;
        }
        $map = [
            'status'    =>  1,
            'type'      =>  $types,
            'product_id'=>  $plan['product_id']
        ];
        //计划可销售票型
        $planTicket = implode(',',$param['ticket']);//dump($param['ticket']);
        //是否限制票型
        if(empty($sealeTicket)){
            $map['activity'] = '0';
            $map['_string']="FIND_IN_SET(".$scene.",scene)";
            //指定票型ID 忽略销售场景
            if($scene <> '1'){
                $map['group_id']  =  array('in',$group);
            }
        }else{
            $confineTicket = array_intersect($param['ticket'], $sealeTicket);
            if(!empty($confineTicket)){
                $map['id'] = ['in',implode(',',$confineTicket)];
            }else{
                return false;
            }
        }
        if(!empty($area)){
            $map = array_merge($area,$map);
        }
        //获取价格信息
        $tickets = M('TicketType')->where($map)->field('id,name,area,price,discount')->select();
        if($plan['product_type'] == '1' && $seale == '2'){
            foreach ($tickets as $va){
                if(in_array($va['id'],$param['ticket'])){
                    $va['area_num'] = area_count_seat($table,['area'=>$va['area'],'status'=>'0'],1);
                    $va['area_nums'] = area_count_seat($table,['area'=>$va['area'],'status'=>'2'],1);
                    $va['area_id'] = $va['area'];
                    $va['area'] = areaName($va['area'],1);
                    //判断是否开启多级扣款 TODO  后期优化
                    if((int)$scene === (int)2){
                        //获取当前用户
                        $uinfo = Home\Service\Partner::getInstance()->getInfo();
                        $ticketLevel = F('TicketLevel');
                        if(!$ticketLevel){
                            D('Home/TicketLevel')->ticke_level_cache();
                            $ticketLevel = F('TicketLevel');
                        }
                        $itemConf = cache('ItemConfig');
                        //一级代理商直接显示景区结算价格 指定票型ID时，使用景区指定价格
                        if($itemConf[$uinfo['crm']['itemid']]['1']['level_pay'] && $uinfo['crm']['level'] > 16 && empty($sealeTicket)){
                            $ticket_level = $ticketLevel[$uinfo['crm']['f_agents']];
                            $va['discount'] = $ticket_level[$va['id']]['discount'];/*结算价格*/
                        }
                    }
                    if($va['discount']){
                        $price[] = $va;
                    }
                }
            }
        }else{

            foreach ($tickets as $va){
                if(in_array($va['id'],$param['ticket'])){
                    $va['area_num'] = $area_num;
                    $va['area_nums']= $area_nums;
                    $va['area_id'] = 0;
                    //判断是否开启多级扣款 TODO  后期优化 指定票型ID时，使用景区指定价格
                    if((int)$scene === (int)2){
                        //获取当前用户
                        $uinfo = Home\Service\Partner::getInstance()->getInfo();
                        $ticketLevel = F('TicketLevel');
                        if(!$ticketLevel){
                            D('Home/TicketLevel')->ticke_level_cache();
                            $ticketLevel = F('TicketLevel');
                        }
                        $itemConf = cache('ItemConfig');
                        //一级代理商直接显示景区结算价格
                        if($itemConf[$uinfo['crm']['itemid']]['1']['level_pay'] && $uinfo['crm']['level'] > 16 && empty($sealeTicket)){
                            $ticket_level = $ticketLevel[$uinfo['crm']['f_agents']];//d//ump($ticket_level);
                            $va['discount'] = empty($ticket_level[$va['id']]['discount']) ? $va['discount'] : $ticket_level[$va['id']]['discount'];/*结算价格*/
                        }
                    }
                    if($va['discount']){
                        $price[] = $va;
                    }
                }
            }
        }
        return $price;
    }
    /**
     * 返回客源地
     * @param  int $param 客源地id
     * @return string        
     */
    function region($param){
        $province = F('Province');
        $title = $province[$param]['name'];
        echo $title;
    }
    /*判断订单是否过期
    * @param $param 订单所属销售计划
    */
    function is_order_plan($param, $type = null){
        //获取当前可售销售计划
        $plan = F('planlist');
        if(in_array($param,explode(',',$plan))){
            //正常
            return true;
        }else{//已过期
            return false;
        }
    }
    /**
     *  作用：array转xml
     */
    function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val))
            {
                $xml.="<".$key.">".$val."</".$key.">";
            }
            else
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
        $xml.="</xml>";
        return $xml;
    }
    /**
     *  作用：将xml转为array
     */
    function xmlToArray($xml)
    {
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }
    //对象转数组,使用get_object_vars返回对象属性组成的数组
    function objectToArray($obj){
        $arr = is_object($obj) ? get_object_vars($obj) : $obj;
        if(is_array($arr)){
            return array_map(__FUNCTION__, $arr);
        }else{
            return $arr;
        }
    }
    //数组转对象
    function arrayToObject($arr){
        if(is_array($arr)){
            return (object) array_map(__FUNCTION__, $arr);
        }else{
            return $arr;
        }
    }
    /**
     * 手续费类型
     * @param $param int 1 退票手续费
     * @return string 名称
     */
    function poundage($param,$type = null){
        switch ($param) {
            case '1':
                $return = "退票手续费";
                break;
        }
        if($type){
            echo $return;
        }else{
            return $return;
        }
    }
    /*根据用户id获取用户电话*/
    function get_phone($param){
        if(empty($param)){
            return false;
        }
        $phone = M('User')->where(array('id'=>$param))->getField('phone');
        return $phone;
    }
/**
 * 生成二维码后通过base64处理后返回
 * @param  string $data 二维码数据
 * @param  string $name 图片名称
 * @param  string $header_logo 生成带logo 2维码
 * @return 返回图片base64 地址
 */
function qr_base64($data,$name,$logo = '',$level = 'L',$size = '4'){
    $image_file = SITE_PATH."d/upload/".$name.'.png';
    /*二维码是否已经生成*/
    if(!file_exists($image_file)){
        //生成二维码
        \Libs\Service\Qrcode::createQrcode($data,$name,$logo,$level,$size);
    }
    $image_info = getimagesize($image_file);
    $base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($image_file)));
    return $base64_image_content;
}
/**
 * 获取分销二维码
 * @param  [type] $openid openID
 * @return [type]         [description]
 */
function get_up_fxqr($openid, $pid){
    //$pid = get_product('id') ? get_product('id') : '43';
    $model = D('WxMember');
    $info = $model->where(['openid'=>$openid])->field('openid,user_id,headimgurl')->find();
    $logo_path = SITE_PATH."d/upload/viplogo/";
    $logo = $logo_path.'u-logo-'.$info['user_id'].'.png';
    if(!file_exists($logo)){
        if(!$info['headimgurl']){
            $user = & load_wechat('User', $pid, 1);
            $result = $user->getUserInfo($openid);
            if(!empty($result['headimgurl'])){
                $model->where(['openid'=>$v['openid']])->setField('headimgurl',$result['headimgurl']);
                $logo_path = \Libs\Util\Upload::getImage($result['headimgurl'],$logo_path,'u-logo-'.$info['user_id'].'.png');
                $logo = $logo_path.'u-logo-'.$info['user_id'].'.png';
            }else{
                $logo = $logo_path."delogo.jpg";
            }
        }else{
            $logo_path = \Libs\Util\Upload::getImage($info['headimgurl'],$logo_path,'u-logo-'.$info['user_id'].'.png');
            $logo = $logo_path.'u-logo-'.$info['user_id'].'.png';
        }
    }
    //生成新的二维码
    $param = $pid."&".$info['user_id']."&qrcode";
    $param = \Libs\Util\Encrypt::authcode($param,'ENCODE');
    $url = U('Wechat/Index/show',array('u'=>$info['user_id'],'pid'=>$pid,'param'=>$param));
    //$url = U('Wechat/Activity/act',['pid'=>$pid,'act'=>'131159','u'=>$info['user_id'],'param'=>$param]);
    return qr_base64($url,'u-'.$info['user_id'],$logo,'M','6');
}
/**
 * 记录网银支付日志
 * @param  string $money 金额
 * @param  string $sn    系统单号
 * @param  int    $scene 创建场景 1、窗口当面付2在线支付
 * @param  int    $type  1支付宝2微信
 * @param  int    $pattern 支付类型1收款2付款3退款
 * @param  array  $data  提交数据包
 */
function payLog($money,$sn,$scene,$type,$pattern,$data){
    if($type == '2'){
       //处理微信支付的金额对100取余 ,原因你懂得
       $money = $data['total_fee']/100;
    }
    //记录微信支付
    $pay_log = array(
        'out_trade_no' =>   '0', 
        'money'        =>   $money,
        'order_sn'     =>   $sn,
        'param'        =>   serialize($data),
        'status'       =>   2,
        'type'         =>   $type,
        'pattern'      =>   $pattern,
        'scene'        =>   $scene,
        'create_time'  =>   time()
    );
    //快速缓存 缓存有效期300秒
    S('pay'.$sn,'400',300);
    return D('Manage/Pay')->add($pay_log);
}
/**
 * 返回支付类型
 * @param  string $chane 支付类
 */
function pay_pattern($chane){
    $collection = array('ali_app','ali_wap','ali_web','ali_qr','ali_bar','ali_charge','wx_app','wx_pub','wx_qr','wx_bar','wx_lite','wx_wap','wx_charge');
    $payment = array('ali_red','ali_transfer','wx_red','wx_transfer');
    $refund = array('ali_refund','wx_refund');
    if(in_array($chane,$collection)){
        //收款
        return '1';
    }
    if(in_array($chane,$payment)){
        return '2';
    }
    if(in_array($chane,$refund)){
        return '3';
    }
}
//订单售票
function print_buttn_show($type,$pay,$sn,$plan_id,$money,$view = '1',$act = 0,$genre = 1){
    if(in_array($pay, array('1','3')) && $type == '6' && check_collection_pay($sn) && $money > 0){
        $title = "网银支付";
        $width = '600';
        $height = '400';
        $pageId = 'payment';
        $url = U('Item/Order/public_payment',array('plan'=>$plan_id,'sn'=>$sn,'is_pay'=>$pay,'money'=>$money,'order_type'=>3,'act'=>$act,'genre'=>$genre));
    }else{
        $pageId = 'print';
        $width = '213';
        $height = '208';
        $title = '门票打印';
        $url = U('Item/Order/drawer',array('sn'=>$sn,'plan_id'=>$plan_id,'act'=>$act,'genre'=>$genre));
    }
    if($view == '2'){
        //return 返回
        $return = [
            'title' =>  $title,
            'width' =>  $width,
            'height'=>  $height,
            'pageId'=>  $pageId,
            'url'   =>  $url
        ];
        return $return;
    }
    if($view == '1'){
        //echo 输出
        $viewbut = "<a data-toggle='dialog' data-id='".$pageId."' data-width='".$width."' data-height='".$height."' data-title='".$title."' href='".$url."' data-resizable='false' data-maxable='false' data-mask='true'>出票</a>";
        echo $viewbut;
    }
    
}
/*判断是否已经收款*/
function check_collection_pay($sn){
    $status = D('Collection')->where(array('order_sn'=>$sn))->field('id')->find();
    if(empty($status)){
        return true;
    }else{
        return false;
    }
}
/**
 * 行业类型
 * 导游，运输，餐饮，商户，住宿，其他
 * @return [type] [description]
 */
function industry($param,$type = ''){
    switch ($param) {
        case '导游':
            $return = "1";
            break;
        case '运输':
            $return = "2";
            break;
        case '餐饮':
            $return = "3";
            break;
        case '商户':
            $return = "4";
            break;
        case '住宿':
            $return = "5";
            break;
        case '其它':
            $return = "6";
            break;
        case '1':
            $return = "导游";
            break;
        case '2':
            $return = "运输";
            break;
        case '3':
            $return = "餐饮";
            break;
        case '4':
            $return = "商户";
            break;
        case '5':
            $return = "住宿";
            break;
        case '6':
            $return = "其它";
            break;
        default:
            $return = $type ? "0" : "未知";     
    }

    if($type == '1'){
        return $return;
        
    }else{
       echo $return;
    }   
}
/**
 * 客源地 
 */
function provinces($param = null){
    $provinces = F('Province');
    echo $provinces[$param]['name'];
}
/*
*预约团队类型
*/
function teamtype($param = null)
{
    switch ($param) {
        case 1:
            echo "含行程";
            break;
        case 2:
            echo "自费加点";
            break;
        case 3:
            echo "直通车";
            break;
    }
}
//根据日期计算生日
function getAgeByID($id){ 
    //过了这年的生日才算多了1周岁 
    if(empty($id)) return ''; 
    $date = strtotime(substr($id,6,8));
    //获得出生年月日的时间戳 
    $today = strtotime('today');
    //获得今日的时间戳 111cn.net
    $diff = floor(($today-$date)/86400/365);
    //得到两个日期相差的大体年数 
    //strtotime加上这个年数后得到那日的时间戳后与今日的时间戳相比 
    $age = strtotime(substr($id,6,8).'+'.$diff.'years') > $today ? ($diff+1) : $diff;
    echo $age; 
}
/**
 * 模拟请求
 * @param  string $url  访问地址
 * @param string $method 请求方式
 * @param array  $postData
 *
 * @return mixed|null|string
 */
function getHttpContent($url, $method = 'GET', $postData = array()){
    $data = '';
    if (!empty($url)) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30); //30秒超时
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);
            if (strtoupper($method) == 'POST') {
                $curlPost = is_array($postData) ? http_build_query($postData) : $postData;
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
            }
            $data = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            $data = null;
        }
    }
    return $data;
}
/**
 * 任意值转加密常量
 * @Author   zhoujing                 <zhoujing@leubao.com>
 * @DateTime 2019-04-29T16:55:29+0800
 * @param    int|array|string     $value            加密值
 * @param    integer                  $length      长度
 * @param    string                   $authCode    颜值
 * @return   string
 */
function putIdToCode($value, $length = 6, $authCode = '')
{
    if(empty($authCode)){
        $authCode = C('AUTHCODE');
    }
    $hashids = new Hashids\Hashids($authCode, $length);
    $hashID = $hashids->encode($value);
    return $hashID;
}
/**
 * 解密
 * @Author   zhoujing                 <zhoujing@leubao.com>
 * @DateTime 2019-04-29T16:56:51+0800
 * @param    string                   $value             解密常量
 * @param    string                   $authCode        颜值
 * @return   int|array|string
 */
function getCodeToId($value='', $length = 6, $authCode = '')
{
    if(empty($authCode)){
        $authCode = C('AUTHCODE');
    }
    $hashids = new Hashids\Hashids($authCode, $length);
    $decodeResult = $hashids->decode($value);
    return $decodeResult;
}
/**
 * 判断奇偶数
 * @param $n
 * @return int
 */
function isOdd($n){
    // $a & $b  And（按位与）    将把 $a 和 $b 中都为 1 的位设为 1。
    return $n & 1;
}
/**
 * 校验身份证
 * @Author   zhoujing                 <zhoujing@leubao.com>
 * @DateTime 2020-06-04T16:38:19+0800
 * @param    array                   $info                 
 * @param    integer                  $type                 1活动限制2场次限制
 * @return   [type]                                         [description]
 */
function verifyIdCard($info, $type = 1){
    if($type === 1){
        $actInfo = D('Item/Activity')->getActInfo($info['actid']);
        $number = (int)$actInfo['param']['info']['number'];
        if((int)$actInfo['param']['info']['limit'] === 1){
            $map = ['idcard'=>trim($info['idcard']),'activity_id'=>$info['actid']];
        }else{
            $map = ['idcard'=>trim($info['idcard']),'plan_id'=>$info['plan']];
        }
    }else{
        $number = 1;
        $map = ['idcard'=>trim($info['idcard']),'plan_id'=>$info['plan']];
    }
    
    $count = (int)D('IdcardLog')->where($map)->count();
    if($count > 0){
        //读取活动
        if($number > 0){
            if($count >= $number){
                $return = false;
            }else{
                $return = true;
            }
        }else{
            $return = true;
        }
    }else{
        $return = true;
    }
    return $return;
}
function getPrePlanShow($plan, $type, $status)
{
    if((int)$type === 2 && (int)$status <> 1){
        echo date('Y-m-d', $plan);
    }elseif((int)$type === 2 && (int)$status === 1){
        echo planShow($plan, 1, 1);
    }elseif((int)$type === 1) {
        echo planShow($plan, 1, 1);
    }
}