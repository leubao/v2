<?php
// +----------------------------------------------------------------------
// | LubTMP 系统配置
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------

namespace Manage\Controller;

use Common\Controller\ManageBase;
use Libs\Util\Upload;
class ConfigController extends ManageBase {

    private $Config = null;

    protected function _initialize() {
        parent::_initialize();
        $this->Config = D('Common/Config');
        $configList = $this->Config->getField("varname,value");
        $this->assign('Site', $configList);
    }

    //网站基本设置
    public function index() {
        if (IS_POST) {
        	C('TOKEN_ON',false);
            if ($this->Config->saveConfig($_POST)) {
                $this->success("更新成功！");
            } else {
                $error = $this->Config->getError();
                $this->error($error ? $error : "配置更新失败！");
            }
        } else {
            //首页模板
            $filepath = TEMPLATE_PATH . (empty(self::$Cache["Config"]['theme']) ? 'Default' : self::$Cache["Config"]['theme']) . '/Home/Index/';
            $indextp = str_replace($filepath, '', glob($filepath . 'index*'));
            //URL规则
            $Urlrules = cache('Urlrules');
            $IndexURL = array();
            $TagURL = array();
            foreach ($Urlrules as $k => $v) {
                if ($v['file'] == 'tags') {
                    $TagURL[$v['urlruleid']] = $v['example'];
                }
                if ($v['module'] == 'content' && $v['file'] == 'index') {
                    $IndexURL[$v['ishtml']][$v['urlruleid']] = $v['example'];
                }
            }
            //读取角色
            $role = M('Role')->field('id,name')->select();
            $this->assign('TagURL', $TagURL)
                    ->assign('IndexURL', $IndexURL)
                    ->assign('indextp', $indextp)
                    ->assign('role',$role)
                    ->display();
        }
    }

    //邮箱参数
    public function mail() {
        if (IS_POST) {
        	C('TOKEN_ON',false);
            $this->index();
        } else {
        	C('TOKEN_ON',false);
            $this->display();
        }
    }

    //附件参数
    public function attach() {
        if (IS_POST) {
        	C('TOKEN_ON',false);
            $this->index();
        } else {
        	$path = PROJECT_PATH . 'Libs/Driver/Attachment/';
            $dirverList = glob($path . '*');
            $lang = array(
                'Local' => '本地存储驱动',
                'Ftp' => 'FTP远程附件驱动',
            );
            foreach ($dirverList as $k => $rs) {
                unset($dirverList[$k]);
                $dirverName = str_replace(array($path, '.class.php'), '', $rs);
                $dirverList[$dirverName] = $lang[$dirverName]? : $dirverName;
            }
            $this->assign('dirverList', $dirverList);
            $this->display();
        }
    }

    //高级配置
    public function addition() {
        if (IS_POST) {
            if ($this->Config->addition($_POST)) {
                $this->success("修改成功，请及时更新缓存！");
            } else {
                $error = $this->Config->getError();
                $this->error($error ? $error : "高级配置更新失败！");
            }
        } else {
            $addition = include COMMON_PATH . 'Conf/addition.php';
            if (empty($addition) || !is_array($addition)) {
                $addition = array();
            }
            $this->assign("addition", $addition);
            $this->display();
        }
    }
	//二维码设置
	public function code(){
		if(IS_POST) {
			C('TOKEN_ON',false);
			$this->index();
		} else {
			C('TOKEN_ON',false);
			$this->display();
		}
	}
    //扩展配置
    public function extend() {
        if (IS_POST) {
            $action = I('post.action');
            if ($action) {
                //添加扩展项
                if ($action == 'add') {
                    $data = array(
                        'fieldname' => trim(I('post.fieldname')),
                        'type' => trim(I('post.type')),
                        'setting' => I('post.setting'),
                        C("TOKEN_NAME") => I('post.' . C("TOKEN_NAME")),
                    );
                    if ($this->Config->extendAdd($data) !== false) {
                        $this->success('扩展配置项添加成功！', U('Config/extend'));
                        return true;
                    } else {
                        $error = $this->Config->getError();
                        $this->error($error ? $error : '添加失败！');
                    }
                }
            } else {
                //更新扩展项配置
                if ($this->Config->saveExtendConfig($_POST)) {
                    $this->success("更新成功！");
                } else {
                    $error = $this->Config->getError();
                    $this->error($error ? $error : "配置更新失败！");
                }
            }
        } else {
            $action = I('get.action');
            $db = M('ConfigField');
            if ($action) {
                if ($action == 'delete') {
                    $fid = I('get.fid', 0, 'intval');
                    if ($this->Config->extendDel($fid)) {
                        cache('Config', NULL);
                        $this->success("扩展配置项删除成功！");
                        return true;
                    } else {
                        $error = $this->Config->getError();
                        $this->error($error ? $error : "扩展配置项删除失败！");
                    }
                }
            }
            $extendList = $db->order(array('fid' => 'DESC'))->select();
            $this->assign('extendList', $extendList);
            C('TOKEN_ON',false);
            $this->display();
        }
    }
    /*登录背景*/
    function login_bj(){
        $this->assign('data',$info)->display();
    }
    /*upload*/
    function up_img(){
        $info = I('get.name');
        $upload = new Upload();// 实例化上传类
        $upload->maxSize   =  3145728 ;// 设置附件上传大小
        $upload->exts      =  array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->autoSub   =  false;
        $upload->replace   =  true;//存在同名文件是否是覆盖，默认为false
        $upload->rootPath  =  'static/images/'; // 设置附件上传根目录
        $upload->saveName  =  I('get.name');
        // 上传单个文件 
        $info   =   $upload->uploadOne($_FILES['file']);
        if(!$info) {// 上传错误提示错误信息
            $this->erun($upload->getError());
        }else{// 上传成功 获取上传文件信息
            // $return['error'] = 0;
             //$return['url'] = $upload->rootPath.$info['savepath'].$info['savename'];
             $this->srun("上传成功",array('tabid'=>$this->menuid.MODULE_NAME));
        }
        
    }
}
