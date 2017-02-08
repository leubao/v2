<?php
// +----------------------------------------------------------------------
// | LubTMP 座椅区域模型
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Manage\Model;

use Common\Model\Model;
class AreaModel extends Model{
	/**
	 * 更新商户缓存
	 * 商户缓存中包含商户信息、和商户相关的产品信息、员工信息
	 */
	function area_cache(){ 	
	 	$data = $this->where(array('status'=>1))->field('id,name,num')->select();
        if (empty($data)) {
            return false;
        }
        $cache = array();
        foreach ($data as $rs) {
        	$cache[$rs['id']] = $rs;
        }
        F('Area', $cache);
        return true;
	 }
     /**
      * 更新座位图
      * @param  string $h_seat 座位
      * @param  array $info   提交数据
      * @return 状态
      */
     function up_seat($h_seat,$info){
        //更新设计视图
        $fg = "','";
        $seatid = explode(',', $info['seatid']);
        $n_seatid = implode($fg, $seatid);
        $seat['h_seat'] = $info['seatid'];
        $seat['n_seat'] = $n_seatid;
        $seat['seat']   = $h_seat['seat'];
        $seat['rows']   = $h_seat['rows'];
        $seat['columns']= $h_seat['columns'];
        //更新生产视图
        $seats = $this->creat_map($info['areaid'],$seat);
        //座位图
        $seat_map = array(
            'seat' => $seats['seat'],
            'rows' => $seats['rows'],
            'columns' => $seats['columns'],
            );
        //写入数据
        $status = $this->where(array('id'=>$info['areaid']))->save(array('seat'=>serialize($seat),'seats'=>serialize($seat_map),'seatid'=>$seats['seatid'],'num'=>$info['number']));
        return $status;   
     }
     /*生成生产环境座椅模板*/
     function creat_map($id,$seat){
        $data = $this->where(array('id'=>$id))->find();
        $i = $data['start_row'];//起始行
        $start_list = $data['start_list'];//起始列
        //座椅模板
        $row = $data['row']+$i;//总行数
        $list = $data['list']-1;//列循环次数
        //初始模板
        //$seat = unserialize($data['seat']);
        if ($data['is_mono'] == '1') {
            //单号
            for($i;$i<$row;$i++){
                for($k=0;$k<=$list;$k++){
                    if($k == 0){
                        if($start_list%2 == 0){
                            $start_list = 1+$start_list;
                            $slist = $start_list;
                        }else{
                            $slist = $start_list;
                        }
                    }else{
                        $slist = $start_list+2*$k;
                    }
                    //座位ID
                    $seatid = $i.'-'.$slist;
                    if(in_array($seatid,explode(',', $seat['h_seat']))){
                        $r['seat'][$i] .= '_';
                    }else{
                        $r['seatid'][$i][] = $seatid;
                        $r['seat'][$i] .= 'a'; 
                    }
                }
            }
        } elseif ($data['is_mono'] == '2'){
            //双号
            for($i;$i<$row;$i++){
                for($k=0;$k<=$list;$k++){
                    if($k == 0){
                        if($start_list%2 == 0){
                            $slist = $start_list;
                        }else{
                            $start_list = 1+$start_list;
                            $slist = $start_list;
                        }
                    }else{
                        $slist = $start_list+2*$k;
                    }
                    //座位ID
                    $seatid = $i.'-'.$slist;
                    if(in_array($seatid,explode(',', $seat['h_seat']))){
                        $r['seat'][$i] .= '_';
                    }else{
                        $r['seatid'][$i][] = $seatid;
                        $r['seat'][$i] .= 'a'; 
                    }
                }
            }
        } else {
            //单双号
            for($i;$i<$row;$i++){
                for($k=0;$k<=$list;$k++){
                    $slist = $start_list+$k;
                    $seatid = $i.'-'.$slist;
                    if(in_array($seatid,explode(',', $seat['h_seat']))){
                        $r['seat'][$i] .= '_';
                    }else{
                        $r['seatid'][$i][] = $seatid;
                        $r['seat'][$i] .= 'a'; 
                    }
                }
            }
        }
        $fg = "','";
        $datas = array(
            'seat' => implode($fg,$r['seat']),
            'seatid' => serialize($r['seatid']),
            'rows' => $seat['rows'],
            'columns' => $seat['columns'],
        );
        //$datas = serialize($datas);
        return $datas;
     }



	/**
     * 插入成功后的回调方法
     */
    protected function _after_insert() {
        $this->area_cache();
    }
    /**
     *更新成功后的回调方法
     */
     protected function _after_update(){
        $this->area_cache();
     }
}