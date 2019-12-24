<?php

/**
 * 系统推送
 * @Author: IT Work
 * @Date:   2019-12-10 11:51:49
 * @Last Modified by:   IT Work
 * @Last Modified time: 2019-12-19 15:53:05
 */
namespace Item\Controller;
use Common\Controller\ManageBase;
class PushController extends ManageBase{

 	//推送日志
    public function push_log()
    {
        try{
            $where = array();
            $start_time = I('starttime');
            $end_time = I('endtime');
            $status = I('status');
            $this->assign('starttime',$start_time)->assign('endtime',$end_time);
            if (!empty($start_time) && !empty($end_time)) {
                $start_time = strtotime($start_time);
                $end_time = strtotime($end_time) + 86399;
                $where['datetime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
            }
            if ($status != '') {
                $where['status'] = array('eq', $status);
            }
            $this->basePage('PushLog',$where,array("id" => "desc"));
            $this->assign('push',$this->getPush())
            	->assign('status',$status)
            	->assign('where', $where)
            	->display();
        } catch(Exception $e) {
          $this->erun('错误:'.$e->getMessage());
        }
    }
	public function push()
	{
		
		try{
			if(IS_POST){
				$pinfo = I('post.');
				if(isset($pinfo['push']) && empty($pinfo['push'])){
					$this->erun('请选择推送平台~');
				}
				if(!in_array($pinfo['push'], array_column($this->getPush(), 'id'))){
					$this->erun('不被支持度的推送平台~');
				}
				switch ((int)$pinfo['push']) {
					case 1001:
						$this->erun('阿里智游同业分销平台暂不支持手动推送~');
						break;
					case 1002:
						$provincial = new \Libs\Service\Provincial;
						//$provincial->upTodayData($pinfo['datetime'], $pinfo['count']);
						$provincial->upRealData(time());
						$this->srun('推送成功~', array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
						break;
					case 1003:
						$jscity = new \Libs\Service\Jxcity;
						$jscity->upTodayData($pinfo['datetime'], $pinfo['count']);
						$jscity->upRealData(time());
						$jscity->upExitData(time());
						$this->srun('推送成功~', array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
						break;
				}
			}else{
				$this->assign('push',$this->getPush())->display();
			}

		} catch(Excetion $e){
			$this->erun('错误:'.$e->getMessage());
		}
	}
	public function getPush()
	{
		$push = [
			[
				'id'  => '1001',
				'name'=> '阿里智游分销平台',
			],
			[
				'id'  => '1002',
				'name'=> '江西省旅游平台',
			],
			[
				'id'  => '1003',
				'name'=> '上饶市旅游平台',
			]
		];
		return $push;
	}
}