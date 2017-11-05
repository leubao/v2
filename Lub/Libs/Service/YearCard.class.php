<?php
// +----------------------------------------------------------------------
// | LubTMP  年卡处理
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;

use Common\Model\Model;
class YearCard extends \Libs\System\Service {
	/** 执行错误消息及代码 */
    public $error = '';
	/**
     * 判断是否已经存在年卡
     * @Author   zhoujing   <zhoujing@leubao.com>
     * @DateTime 2017-11-04
     * @param    string     $param                openID  或身份证号码
     * @param    string     $type                 openid 或 idcard
     * @return   [type]                           [description]
     */
    public function check_wechat_card($param = '', $type = 'openid')
    {	
    	if(empty($param)){return false;}
        $model = D('Crm/Member');
    	if($type == 'openid'){
            $map = [
                'openid' => $param
            ];
    	}
    	if($type == 'idcard'){
            $map = [
                'idcard' => $param
            ];
    	}
        //判断身份证号是否在允许范围内
        
        $count = $model->where($map)->count();
        if($conut <> 0){
            return $count;
        }else{
            return true;
        }
    }
    /**
     * @Company  承德乐游宝软件开发有限公司
     * @Author   zhoujing      <zhoujing@leubao.com>
     * @DateTime 2017-11-05
     * @param    string        $idcard               身份证号
     * @return   [type]                              [description]
     */
    public function check_year_card($idcard = '')
    {
        //判断身份证号是否有效
        if(checkIdCard($idcard)){
            $model = D('Crm/Member');
            $map = [
                'cardid' => $idcard,
                'status' => 1,
            ];
            $count = $model->where($map)->count();
            if($count <> 0){
            	$this->error = '您已经办理完年卡';
                return false;
            }
            //身份证号拆解
            $sex = substr($idcard,-2,1);
            $card = [
               'province' =>  substr($idcard,0,2),//省 1-2
               'city'     =>  substr($idcard,2,2),//市3-4
               'county'   =>  substr($idcard,4,2),//县5-6
               'birthday' =>  substr($idcard,6,8),//生日7—14
               'sex'      =>  $sex,//第17位 奇数男 偶数女
               'sexs'     =>  $sex%2 == 0 ? '女' : '男'
            ];
            //读取年卡配置
            $year = D('Crm/MemberType')->where(['id'=>1])->cache('year_card',3600)->getField('rule');
            $rule = json_decode($year,true);
            $area = explode(',',$rule['area']);
            if(in_array($card['province'].$card['city'],$area)){
                return true;
            }else{
            	$this->error = '非常抱歉,您所在的区域暂不能办理年卡';
                return false;
            }
        }else{
        	$this->error = '您输入的身份证号码无效';
            return false;
        }
    }
}