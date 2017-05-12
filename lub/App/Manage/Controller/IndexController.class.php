<?php
namespace Manage\Controller;

use Common\Controller\ManageBase;
use Manage\Service\User;
class IndexController extends ManageBase {
    public function index() {
        if (IS_AJAX) {
            return true;
        }
        $pid = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');
        $this->assign("SUBMENU_CONFIG", D("Manage/Menu")->getMenuList());
        $this->assign('userInfo', User::getInstance()->getInfo());
        $this->assign('role_name', D('Manage/Role')->getRoleIdName(User::getInstance()->role_id));
        $this->assign('pid',$pid)->display();
    }
    //缓存更新
    public function cache() {
        if (isset($_GET['type'])) {
            $Dir = new \Dir();
            $cache = D('Common/Cache');
            $type = I('get.type');
            set_time_limit(0);
            switch ($type) {
                case "site":
                    //开始刷新缓存
                    $stop = I('get.stop', 0, 'intval');
                    if (empty($stop)) {
                        try {
                            //已经清除过的目录
                            $dirList = explode(',', I('get.dir', ''));
                            //删除缓存目录下的文件
                            $Dir->del(RUNTIME_PATH);
                            //获取子目录
                            $subdir = glob(RUNTIME_PATH . '*', GLOB_ONLYDIR | GLOB_NOSORT);
                            if (is_array($subdir)) {
                                foreach ($subdir as $path) {
                                    $dirName = str_replace(RUNTIME_PATH, '', $path);
                                    //忽略目录
                                    if (in_array($dirName, array('Cache', 'Logs','Wechat'))) {
                                        continue;
                                    }
                                    if (in_array($dirName, $dirList)) {
                                        continue;
                                    }
                                    $dirList[] = $dirName;
                                    //删除目录
                                    $Dir->delDir($path);
                                    //防止超时，清理一个从新跳转一次
                                   // $this->assign("waitSecond", 200);
                                    //$this->success("清理缓存目录[{$dirName}]成功！", );
                                    $this->srun("清理缓存目录[{$dirName}]成功！",array('urls'=>U('Index/cache', array('type' => 'site', 'dir' => implode(',', $dirList))),'stop'=>'999'));
                                    exit;
                                }
                            }
                            //更新开启其他方式的缓存
                            \Think\Cache::getInstance()->clear();
                        } catch (Exception $exc) {
                            
                        }
                    }
                    if ($stop) {
                        $modules = $cache->getCacheList();
                        //需要更新的缓存信息
                        $cacheInfo = $modules[$stop - 1];//dump($cacheInfo);
                        if ($cacheInfo) {
                            if ($cache->runUpdate($cacheInfo) !== false) {
                                $this->assign("waitSecond", 200);
                                $this->srun('更新缓存：' . $cacheInfo['name'], array('urls'=>U('Index/cache', array('type' => 'site', 'stop' => $stop + 1)),'stop'=>$stop));
                                exit;
                            } else {
                                $this->erun('缓存[' . $cacheInfo['name'] . ']更新失败！', array('urls'=>U('Index/cache', array('type' => 'site', 'stop' => $stop + 1)),'stop'=>$stop));
                            }
                        } else {
                            $this->srun('缓存更新完毕！', array('urls'=>U('Index/cache'),'stop'=>0));
                            exit;
                        }
                    }

                    $this->srun("即将更新系统缓存！", array('urls'=>U('Index/cache', array('type' => 'site', 'stop' => 1)),'stop'=>1));
                    break;
                case "template":
                    //删除缓存目录下的文件
                    $Dir->del(RUNTIME_PATH);
                    $Dir->delDir(RUNTIME_PATH . "Cache/");
                    $Dir->delDir(RUNTIME_PATH . "Temp/");
                    //更新开启其他方式的缓存
                    \Think\Cache::getInstance()->clear();
                    $this->srun("模板缓存清理成功！", array('urls'=>U('Index/cache'),'stop'=>'0'));
                    break;
                case "logs":
                    $Dir->delDir(RUNTIME_PATH . "Logs/");
                    $this->srun("站点日志清理成功！", array('urls'=>U('Index/cache'),'stop'=>'0'));
                    break;
                default:
                    $this->erun("请选择更新缓存类型！");
                    break;
            }
        } else {
            $this->display();
        }
    }
    public function public_index_info(){
        $pid = \Libs\Util\Encrypt::authcode(get_product('id'),'ENCODE');
        $seale = U('Api/figure/index',array('pid'=>$pid,'type'=>$this->product['type']));
    	$this->assign('seale',$seale)->display();
    }
    //登录超时
    function login_time()
    {
        $this->display();
    }
    //日历
    function calendar(){
    	$this->display();
    }
    //座位图
    function seat(){
        $this->display();
    }
    //区域加载
    function area(){
        $ginfo = I('get.');
        $this->assign('area',$ginfo['area'])->display();
    }
    /*通用方法*/
    function public_temp(){
        $this->display();
    }
    /*获取渠道商*/
    function public_channel(){
        if(IS_POST){
            if($_POST["name"] != ""){
                $map["name"] = array('like','%'.$_POST["name"].'%');
                $map['product_id'] = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');
                $this->assign("name",$_POST["name"]);
            }
        }
        $ifadd = I('ifadd');
        $level = I('level') ? I('level') : '16';//默认为一级代理商
        $map['level'] = $level;
        $this->basePage('Crm',$map,array('id'=>'ASC'),10);
        $this->assign("ifadd",$ifadd)->display();

    }
    /*获取员工*/
    function public_user(){
        $pinfo = I('post.');
        if(!empty($pinfo['name'])){
            $map["nickname"] = array('like','%'.$pinfo["name"].'%');
        }
        if(!empty($pinfo['phone'])){
            $map["phone"] = $pinfo['phone'];
        }
        if(!empty($pinfo['legally'])){
            $map["legally"] = $pinfo['legally'];
        }
        //add是否可追加  可多选 1可多选可追加  2只能单选
        $ifadd = I('ifadd');
        $type = I('type');
        switch($type){
            case '1':
                //订单查询中查询下单人
                $map['is_scene'] = array('in','1,2,3,4');
                break;
            case '2':
                //管理员 员工
                $map['is_scene'] = array('in','1');
                break;
            case '3':
                //售票员
                $map['is_scene'] = array('in','1');
                $map['role_id']  =  array('in','7');
                break;
            case '4':
                //导游
                $map['is_scene'] = array('in','3');
                $map['role_id']  =  array('in','20');
                break;
            case '5':
                //全员销售
                $map['is_scene'] = array('in','4');
                break;
            default :
                $map['is_scene'] = array('in','1,2,3,4');
                break;
        }
        //TODO 员工中分售票员和普通员工 动态配置
        $this->basePage('User',$map,array('id'=>'ASC'),10);
        $this->assign('type',$type);
        $this->assign("ifadd",$ifadd)
            ->assign('pinfo',$pinfo)
            ->display();
    }
    /*按日期查询场次*/
    function public_date_plan(){
        $datetime = I('datetime') ? I('datetime') : date('Y-m-d');
        $this->assign('datetime',$datetime);
        $datetime = strtotime($datetime);
        $plan = M('Plan')->where(array('plantime'=>$datetime))->field('id')->select();
        $this->assign('plan',$plan)
            ->display();
    }
    //获取票型
    function public_get_price(){
        if(IS_POST){
            if($_POST["name"] != ""){
                $map["name"] = array('like','%'.$_POST["name"].'%');
                $map['product_id'] = get_product('id');
                $this->assign("name",$_POST["name"]);
            }
        }
        $ifadd = I('ifadd');
        $this->basePage('TicketType',$map,array('id'=>'DESC','status'=>'DESC'),10);
        $this->assign("ifadd",$ifadd)
            ->display();
    }
    //获取单票
    function public_ticket_single(){
        $this->basePage('TicketSingle',array('product_id'=>(int) $this->pid,'status'=>1));
        $this->display();
    }
    //获取分组内所有票型
    function public_get_group_ticket(){
        $group_id = I('get.group_id');
        $scene = I('get.scene');
        if(!empty($scene)){
            $map['scene'] = array('find_in_set',$scene);
        }
        $map = array('group_id'=>array('in',$group_id),'product_id'=>get_product('id'),'status'=>1);
        $lists = M('TicketType')->where($map)->field('id,name,price,param')->select();
        foreach ($lists as $k => $v) {
            $param = unserialize($v['param']);
            $list[] = array(
                'id'    =>  $v['id'],
                'name'  =>  $v['name'],
                'price' =>  $v['price'],
                'full'  =>  $param['full'],
                'level3'=>  $param['level3']
            );
        }
        $return = array('statusCode'=>'200','data'=>$list);
        die(json_encode($return));
    }
    /*/*获取导游
    function guide(){
        if(IS_POST){
            if($_POST["name"] != ""){
                $map["nickname"] = array('like','%'.$_POST["name"].'%');
                $this->assign("name",$_POST["name"]);
            }   
        }
        if(I('type') == '1'){
            //订单查询中查询下单人
            $map['is_scene'] = array('in','2,3');
        }else{
            $map['groupid'] = '2';
        }
        C('VAR_PAGE','pageNum');
        $db = M('User');
        $count = $db->where($map)->count();// 查询满足要求的总记录数
        $num = 10;
        $p = new \Item\Service\Page($count,$num);
        $currentPage = !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1;
        $firstRow = ($currentPage - 1) * $num;
        $listRows = $currentPage * $num;
        $data = $db->where($map)->order("id ASC")->limit($firstRow . ',' . $p->listRows)->select();
        $this->assign ( 'totalCount', $count);
        $this->assign ( 'numPerPage', $p->listRows);
        $this->assign ( 'currentPage', !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1);
        $this->assign('type',I('type'));
        $this->assign("list",$data)
            ->display();
    }*/
    //获取未出票的订单
    function index_order(){
        $this->display();
    }
    //待处理的订单
    function index_pending_order(){
        $this->display();
    }
    //授权证书
    function auth(){
        $this->display();
    }
    //查询操作日志当前一小时内的所有操作
    function public_action_log(){
        $this->display();
    }
}