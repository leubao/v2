<?php
// +----------------------------------------------------------------------
// | LubTMP 第三方应用模型
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>   2014-10-15
// +----------------------------------------------------------------------
namespace Manage\Model;
use Common\Model\Model;
class AppModel extends Model{
	protected $_auto = array ( 
		array('appkey', 'genRandomString', 1, 'function',8),
		array('createtime','time',1,'function'),
		array('userid','get_user_id',1,'function'),
	);
	/**
	 * 生成appid
	 */
	function appid($id){
		$code = genRandomString(5,1).$id;
		$appid = substr($code,0,5);
		return $appid;
	}
	
	/**
     * 插入成功后的回调方法
     */
    protected function _after_insert($data, $options) {
        $this->where(array('id'=>$data['id']))->save(array('appid'=>$this->appid($data['id'])));      
    }
}