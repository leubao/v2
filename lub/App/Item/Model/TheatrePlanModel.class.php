<?php
// +----------------------------------------------------------------------
// | LubTMP  销售计划模型
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;
use Common\Model\Model;

class TheatrePlanModel extends Model{
	
	protected $_auto = array(
        array('createtime', 'time', 1, 'function'),
    );
	/**
	 * 座椅模板表 名称规则
	 * 前缀+名称+产品ID+日期+场次
	 * lub_seat_2_140607_2
	 */
	public function addPlan($data){
		if (empty($data)) {
            return false;
        }
		$starttime = strtotime($data['starttime'].$data['start']);
		$endtime = strtotime($data['starttime'].$data['end']);
		
		$param=array(
			'seat' => $data['seat'],
			'ticket' => $data['ticket'],
		);
		

		$data = $this->create($data, 1);
		if($data){
			$data = array(
				'plantime' => strtotime($data['starttime']),
				'product_id' => $data['product_id'],
				'games' => $data['games'],
				'starttime'	=> $starttime,
				'endtime'	=> $endtime,
				'seat_table' => 'seat_'.$data['product_id'].'_'.substr(date('Ymd',$starttime),2).'_'.$data['games'],
				'order_table' => 'order_'.$data['product_id'].'_'.substr(date('Ymd',$starttime),2).'_'.$data['games'],
				'status' => $data['status'],
				'is_sales' => 1,
				'user_id' => 1,
				'createtime' => time(),
				'template_id'=>$data['template_id'],
				'param'	=> serialize($param),
			);
			$planid = $this->add($data);
		}
		
		if($planid){
			//$b = "﻿CREATE TABLE IF NOT EXISTS `lub_seat_2_140724_1` (  `id` smallint(5) NOT NULL AUTO_INCREMENT,  `row` int(3) unsigned NOT NULL COMMENT '行',  `list` int(3) unsigned NOT NULL COMMENT '列',  `area` int(11) NOT NULL COMMENT '区域',  `status` tinyint(1) NOT NULL COMMENT '状态',  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		   // $c = M()->execute($b,true);
			//echo $this->getLastSql();
			return true;
			/*if ($c) {
			//if ($this->createtable($data['seat_table'])) {
			 		return true;
            } else {
                	return false;
            }*/
			
		}else{
			return false;
		}
		//echo $this->getLastSql();
	}
	/**
     * 创建内容模型
     * @param type $tableName 表名称（不包含表前缀）
     * @return boolean
     */
    protected function createtable($tableName) {
        if (empty($tableName)) {
            return false;
        }
        //表前缀
        $dbPrefix = C("DB_PREFIX");
        //读取模型主表SQL模板
       
        $seatTableSql = file_get_contents(COMMON_PATH."Sql/lub_seat.sql");

        //$orderTableSql = file_get_contents(COMMON_PATH."Sql/lub_order.sql";);
        //表前缀，表名，模型id替换
        $sqlSplit = str_replace(array('@lubtmp@', '@seat@'), array($dbPrefix, $tableName), $seatTableSql);
        //$sqlSplit = str_replace(array('@lubtmp@', '@zhubiao@'), array($dbPrefix, $ordertable), $orderTableSql);
		//$this->execute($sqlSplit);
		
       // return $this->sql_execute($sqlSplit);
        //dump($this->sql_execute($sqlSplit));
    }
	/**
     * 执行SQL
     * @param type $sqls SQL语句
     * @return boolean
     */
    protected function sql_execute($sqls) {
        $sqls = $this->sql_split($sqls);
        if (is_array($sqls)) {
            foreach ($sqls as $sql) {
                if (trim($sql) != '') {
                    $a = $this->query($sql,true);dump($sql);
                }
            }
        } else {
            $this->execute($sqls, true);
        }
        //return true;
    }
/**
     * SQL语句预处理
     * @param type $sql
     * @return type
     */
    public function sql_split($sql) {
        if (mysql_get_server_info() > '4.1' && C('DB_CHARSET')) {
            $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=" . C('DB_CHARSET'), $sql);
        }
        if (C("DB_PREFIX") != "lub_") {
            $sql = str_replace("lub_", C("DB_PREFIX"), $sql);
        }
        $sql = str_replace("\r", "\n", $sql);
        $ret = array();
        $num = 0;
        $queriesarray = explode(";\n", trim($sql));
        unset($sql);
        foreach ($queriesarray as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            $queries = array_filter($queries);
            foreach ($queries as $query) {
                $str1 = substr($query, 0, 1);
                if ($str1 != '#' && $str1 != '-')
                    $ret[$num] .= $query;
            }
            $num++;
                   
        } //dump($ret);
        return $ret;
    }
}