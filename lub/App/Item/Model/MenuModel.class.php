<?php

// +----------------------------------------------------------------------
// | LubTMP 后台菜单模型
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@chengde360.com>
// +----------------------------------------------------------------------

namespace Item\Model;

use Common\Model\Model;

class MenuModel extends Model {


    /**
     * 获取菜单
     * @return type
     */
    public function getMenuList() {
    	/*
        $items['0changyong'] = array(
            "id" => "",
            "name" => "常用菜单",
            "parent" => "changyong",
            "url" => U("Public/changyong"),
        );
        foreach (D('Manage/AdminPanel')->getAllPanel(\Manage\Service\User::getInstance()->id) as $r) {
            $items[$r['mid'] . '0changyong'] = array(
                "icon" => "",
                "id" => $r['mid'] . '0changyong',
                "name" => $r['name'],
                "parent" => "changyong",
                "url" => U($r['url']),
            );
        }
        $changyong = array(
            "changyong" => array(
                "icon" => "",
                "id" => "changyong",
                "name" => "常用菜单",
                "parent" => "",
                "url" => "",
                "items" => $items
            )
        );
        */
        $data = $this->getTree(0);//dump($data);
        return $data;
        //return array_merge($changyong, $data ? $data : array());
    }

    /**
     * 按父ID查找菜单子项
     * @param integer $parentid   父菜单ID  
     * @param integer $with_self  是否包括他自己
     */
    public function adminMenu($parentid, $with_self = false) {
        //父节点ID
        $parentid = (int) $parentid;
        $result = $this->where(array('parentid' => $parentid, 'status' => 1, 'is_scene' => 2))->order('listorder ASC,id ASC')->select();//dump($result);
        if (empty($result)) {
            $result = array();
        }
        if ($with_self) {
            $parentInfo = $this->where(array('id' => $parentid))->find();
            $result2[] = $parentInfo ? $parentInfo : array();
            $result = array_merge($result2, $result);
        }
        //是否超级管理员
        if (\Item\Service\Partner::getInstance()->isAdministrator()) {
            //如果角色为 1 直接通过
            return $result;
        }
        $array = array();
        //子角色列表
        $child = explode(',', D("Item/Role")->getArrchildid(\Item\Service\Partner::getInstance()->role_id));
        foreach ($result as $v) {
            //方法
            $action = $v['action'];
            //条件
            $where = array('app' => $v['app'], 'controller' => $v['controller'], 'action' => $action, 'role_id' => array('IN', $child));
            //如果是菜单项
            if ($v['type'] == 0) {
                $where['controller'] .= $v['id'];
                $where['action'] .= $v['id'];
            }
            //public开头的通过
            if (preg_match('/^public_/', $action)) {
                $array[] = $v;
            } else {
                if (preg_match('/^ajax_([a-z]+)_/', $action, $_match)) {
                    $action = $_match[1];
                }
                //是否有权限
                if (D('Item/Access')->isCompetence($where)) {
                    $array[] = $v;
                }
            }
        }
        return $array;
    }

    /**
     * 取得树形结构的菜单
     * @param type $myid
     * @param type $parent
     * @param type $Level
     * @return type
     */
    public function getTree($myid, $parent = "", $Level = 1) {
        $data = $this->adminMenu($myid);
        $Level++;
        if (is_array($data)) {
            foreach ($data as $a) {
                $id = $a['id'];
                $name = $a['app'];
                $controller = $a['controller'];
                $action = $a['action'];
                //附带参数
                $fu = "";
                if ($a['parameter']) {
                    $fu = "?" . $a['parameter'];
                }
                $array = array(
                    "icon" => "",
                    "id" => $id,
                    "name" => $a['name'],
                    "parent" => $parent,
                	"action" => $action.$id,
                    "url" => U("{$name}/{$controller}/{$action}{$fu}", array('menuid' => $id,'navTabId'=>$action.$id)),
                );
                //$ret[$id . $name] = $array;
                $ret[$id] = $array;
                $child = $this->getTree($a['id'], $id, $Level);
                //由于后台管理界面只支持三层，超出的不层级的不显示
                if ($child && $Level <= 3) {
                    $ret[$id]['items'] = $child;
                }
            }
        }
        return $ret;
    }

    /**
     * 获取菜单导航
     * @param type $app
     * @param type $model
     * @param type $action
     */
    public function getMenu() {
        $menuid = I('get.menuid', 0, 'intval');
        $menuid = $menuid ? $menuid : cookie("menuid", "", array("prefix" => ""));
        $info = $this->where(array("id" => $menuid))->getField("id,action,app,controller,parentid,parameter,type,name");
        $find = $this->where(array("parentid" => $menuid, "status" => 1))->getField("id,action,app,controller,parentid,parameter,type,name");
        if ($find) {
            array_unshift($find, $info[$menuid]);
        } else {
            $find = $info;
        }
        foreach ($find as $k => $v) {
            $find[$k]['parameter'] = "menuid={$menuid}&{$find[$k]['parameter']}";
        }
        return $find;
    }
}