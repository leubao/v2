<?php
// +----------------------------------------------------------------------
// | LubTMP Controller
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@chengde360.com>
// +----------------------------------------------------------------------
namespace Common\Controller;
use Libs\System\Components;
use Libs\Util\Page;
class LubTMP extends \Think\Controller {

    //缓存
    public static $Cache = array();
    //当前对象
    private static $_app;

    public function __get($name) {
        $parent = parent::__get($name);
        if (empty($parent)) {
            return Components::getInstance()->$name;
        }
        return $parent;
    }

    public function __construct() {
        parent::__construct();
        self::$_app = $this;
    }

    //初始化
    protected function _initialize() {
        $this->config = cache("Config");
        $this->initSite($this->config);
        //默认跳转时间
        $this->assign("waitSecond", 3);
       // $this->action_lock();
    }
    //判断是否可操作
    protected function action_lock(){
        /*判断请求方法  是否需要锁验证 */
        $url = ucwords(MODULE_NAME).'/'.ucwords(CONTROLLER_NAME).'/'.ACTION_NAME;
        $getUrlArr = [
            'Item/Order/drawer',
            'Item/Work/refunds',
            'Item/Work/agree',
            'Item/Work/subtract',
            'Item/Order/printTicket'
        ];
        if(IS_GET && in_array($url, $getUrlArr)){
            //检查锁是否存在,存在返回错误，不存在加锁
            $sn = I('get.sn');
            $info = load_redis('get','lock_'.$sn);
            if(empty($info)){
                load_redis('setex','lock_'.$sn,'警告:该订单被锁定,稍后再试...',40);
            }else{
                $this->erun($info);
            }
        }
        $postUrlArr = array(
            'Item/Work/refunds',
            'Item/Work/agree',
            'Item/Work/subtract',
            'Home/Order/cancel_order');
        if(IS_POST && in_array($url, $postUrlArr)){
            $sn = I('post.sn');
            $info = load_redis('get','lock_'.$sn);
            if(empty($info)){
                load_redis('setex','lock_'.$sn,'警告:该订单被锁定,稍后再试...',40);
            }else{
                $this->erun($info);
            }
        }
    }
    /**
     * 获取LubTMP 对象
     * @return type
     */
    public static function app() {
        return self::$_app;
    }

    /**
     * 初始化站点配置信息
     * @return Arry 配置数组
     */
    protected function initSite($Config) {
        /**
         * 判断缓存是否支持
         * @var [type]
         */
        if (class_exists('Redis') == false) {
            $this->error('您的环境不支持Redis,系统无法正常运行！');
            return false;
        }
        $config_siteurl = $Config['siteurl'];
        /*
        if (isModuleInstall('Domains')) {
            $parse_url = parse_url($config_siteurl);
            $config_siteurl = (is_ssl() ? 'https://' : 'http://') . "{$_SERVER['HTTP_HOST']}{$parse_url['path']}";
        }
        defined('CONFIG_SITEURL_MODEL') or define('CONFIG_SITEURL_MODEL', $config_siteurl);*/
        $this->initApp();
        $this->assign("config_siteurl", $config_siteurl);
        $this->assign("Config", $Config);
    }
	/**
     *  系统初始化 
     */
    function initApp(){
        /*
         * 判断必须缓存是否存在
         */
        if(empty(cache('Product'))){
            D('Manage/Product')->product_cache();
        }
        if(empty(cache('Config')) || empty(cache('ProConfig'))){
            D('Common/Config')->config_cache();
        }
        $crm = F('Crm');
        if(empty($crm)){
            D('Crm/Crm')->crm_cache();
            
        }
        $crmGroup = F('CrmGroup');
        if(empty($crmGroup)){
            D('Crm/CrmGroup')->crm_group_cache();
        }
        $province = F('Province');
        if(empty($province)){D('Item/Province')->province_cache();}
        $kpimoney = F('KpiMoney');
        if(empty($kpimoney)){D('Item/KpiChannel')->kpi_channel_cache();}
        //停用已过期的场次
        check_plan();
    }
    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @return void
     */
    protected function ajaxReturn($data, $type = '',$json_option = 0) {
        $data['state'] = $data['status'] ? "success" : "fail";
        if (empty($type))
            $type = C('DEFAULT_AJAX_RETURN');
        switch (strtoupper($type)) {
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:text/html; charset=utf-8');
                exit(json_encode($data));
            case 'XML' :
                // 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($data));
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:text/html; charset=utf-8');
                $handler = isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
                exit($handler . '(' . json_encode($data) . ');');
            case 'EVAL' :
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                exit($data);
            default :
                // 用于扩展其他返回格式数据
                tag('ajax_return', $data);
        }
    }

    /**
     * 返回模型对象
     * @param type $model
     * @return type
     */
    protected function getModelObject($model) {
        if (is_string($model) && strpos($model, '/') == false) {
            $model = M(ucwords($model));
        } else if (strpos($model, '/') && is_string($model)) {
            $model = D($model);
        } else if (is_object($model)) {
            return $model;
        } else {
            $model = M();
        }
        return $model;
    }

    /**
     * 基本信息分页列表方法
     * @param type $model 可以是模型对象，或者表名，自定义模型请传递完整（例如：Content/Model）
     * @param type $where 条件表达式
     * @param type $order 排序
     * @param type $limit 每次显示多少
     */
    protected function basePage($model, $where = '', $order = '', $limit = 25) {
        $model = $this->getModelObject($model);
        $count = $model->where($where)->count();
        $currentPage = !empty($_REQUEST["pageCurrent"])?$_REQUEST["pageCurrent"]:1;
        $firstRow = ($currentPage - 1) * $limit;
        $page = new page($count, $limit);
        $data = $model->where($where)->order($order)->limit($firstRow . ',' . $page->listRows)->select();
        $this->assign('data', $data)
             ->assign( 'totalCount', $count )
             ->assign( 'numPerPage', $page->listRows)
             ->assign( 'currentPage', $currentPage);



        /*$options    =   array();
        $REQUEST    =   (array)I('request.');
         if(is_string($model)){
             $model  =   M($model);
         }
         
         $OPT        =   new \ReflectionProperty($model,'options');
         $OPT->setAccessible(true);
         
         $pk         =   $model->getPk();
         //排序
         if ( isset($REQUEST['orderField']) && isset($REQUEST['orderDirection']) && in_array(strtolower($REQUEST['orderDirection']),array('desc','asc')) ) {
             $options['order'] = '`'.$REQUEST['orderField'].'` '.$REQUEST['orderDirection'];
         }elseif( empty($options['orderField']) && !empty($pk) ){
             $options['order'] = $pk.' desc';
         }
         unset($REQUEST['orderField'],$REQUEST['orderDirection']);
         //查询条件
         if( !empty($map)){
             $options['where'] = $map;
         }else {
             $options['where']['_logic'] = 'or';;
         }
         //每页显示行数
         $pageSize=C('PAGE_SIZE') > 0 ? C('PAGE_SIZE') : 10;
         //当前页
         $pageCurrent =null;
         if (isset($_REQUEST ['pageCurrent'])) {
             $pageCurrent = $_REQUEST ['pageCurrent'];
         }
        if ($pageCurrent == '') {
            $pageCurrent = 1;
        }
         
         $options      =   array_merge( (array)$OPT->getValue($model), $options );
         $count        =   $model->where($options['where'])->count();
         $options['limit'] = $pageSize;
         $options['page'] = $pageCurrent.','.$pageSize.'';
         
         $model->setProperty('options',$options);
         
         $voList= $model->field($field)->select();
         $this->assign('list', $voList);
         $this->assign('total', $count);//数据总数
         $this->assign('pageCurrent', !empty($_REQUEST['pageCurrent']) ? $_REQUEST['pageCurrent'] : 1);//当前的页数，默认为1
         $this->assign('pageSize', $pageSize); //每页显示多少条
         cookie('_currentUrl_', __SELF__);*/
         return;
    }

    /**
     * 基本信息添加
     * @param type $model 可以是模型对象，或者表名，自定义模型请传递完整（例如：Content/Model）
     * @param type $u 添加成功后的跳转地址
     * @param type $data 需要添加的数据
     */
    protected function baseAdd($model, $u = 'index', $data = '') {
        $model = $this->getModelObject($model);
        if (IS_POST) {
            if (empty($data)) {
                $data = I('post.', '', '');
            }
            if ($model->create($data) && $model->add()) {
                $this->success('添加成功！', $u ? U($u) : '');
            } else {
                $error = $model->getError();
                $this->error($error? : '添加失败！');
            }
        } else {
            $this->display();
        }
    }

    /**
     * 基础修改信息方法
     * @param type $model 可以是模型对象，或者表名，自定义模型请传递完整（例如：Content/Model）
     * @param type $u 修改成功后的跳转地址
     * @param type $data 需要修改的数据
     */
    protected function baseEdit($model, $u = 'index', $data = '') {
        $model = $this->getModelObject($model);
        $fidePk = $model->getPk();
        $pk = I('request.' . $fidePk, '', '');
        if (empty($pk)) {
            $this->error('请指定需要修改的信息！');
        }
        $where = array($fidePk => $pk);
        if (IS_POST) {
            if (empty($data)) {
                $data = I('post.', '', '');
            }
            if ($model->create($data) && $model->where($where)->save() !== false) {
                $this->success('修改成功！', $u ? U($u) : '');
            } else {
                $error = $model->getError();
                $this->error($error? : '修改失败！');
            }
        } else {
            $data = $model->where($where)->find();
            if (empty($data)) {
                $this->error('该信息不存在！');
            }
            $this->assign('data', $data);
            $this->display();
        }
    }

    /**
     * 基础信息单条记录删除，根据主键
     * @param type $model 可以是模型对象，或者表名，自定义模型请传递完整（例如：Content/Model）
     * @param type $u 删除成功后跳转地址
     */
    protected function baseDelete($model, $u = 'index') {
        $model = $this->getModelObject($model);
        $pk = I('request.' . $model->getPk());
        if (empty($pk)) {
            $this->error('请指定需要修改的信息！');
        }
        $where = array($model->getPk() => $pk);
        $data = $model->where($where)->find();
        if (empty($data)) {
            $this->error('该信息不存在！');
        }
        if ($model->delete() !== false) {
            $this->success('删除成功！', $u ? U($u) : '');
        } else {
            $error = $model->getError();
            $this->error($error? : '删除失败！');
        }
    }

    /**
     * 客户端成功返回代码
     * @param statusCode  int 必选。状态码(ok = 200, error = 300, timeout = 301)，可以在BJUI.init时配置三个参数的默认值。
     * @param message string  可选。信息内容。
     * @param tabid  string  可选。待刷新navtab id，多个id以英文逗号分隔开，当前的navtab id不需要填写，填写后可能会导致当前navtab重复刷新。
     * @param dialogid    string  可选。待刷新dialog id，多个id以英文逗号分隔开，请不要填写当前的dialog id，要控制刷新当前dialog，请设置dialog中表单的reload参数。
     * @param divid   string  可选。待刷新div id，多个id以英文逗号分隔开，请不要填写当前的div id，要控制刷新当前div，请设置该div中表单的reload参数。
     * @param closeCurrent    boolean 可选。是否关闭当前窗口(navtab或dialog)。
     * @param forward string  可选。跳转到某个url。
     * @param forwardConfirm  string  可选。跳转url前的确认提示信息。
     */
    final public function srun($message = '', $param = null){
        $return = array(
            'statusCode' => '200',
            'message'   => $message,
        );
        if($param){
            $return = array_merge($return,$param);
        }
        D('Manage/Operationlog')->record($message, 1);
        $this->ajaxReturn($return,'json');
    }
    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    final public function erun($message = '', $param = null){
        $return = array(
            'statusCode' => '300',
            'message'   => $message,
        );
        if($param){
            $return = array_merge($return,$param);
        }
        D('Manage/Operationlog')->record($message, 0);
        $this->ajaxReturn($return,'json');
    }
    /**
     * 验证码验证
     * @param type $verify 验证码
     * @param type $type 验证码类型
     * @return boolean
     */
    static public function verify($verify, $type = "verify") {
        return A('Api/Checkcode')->validate($type, $verify);
    }
    //空操作
    public function _empty() {
        $this->error('该页面不存在！');
    }
}