<?php
namespace Api\Controller;
use \Payment\Notify\PayNotifyInterface;
use Payment\Common\PayException;
use Payment\Client\Notify;
/**
 * @author: helei
 * @createTime: 2016-07-20 18:31
 * @description:
 */

/**
 * 客户端需要继承该接口，并实现这个方法，在其中实现对应的业务逻辑
 * Class TestNotify
 * anthor helei
 */
class Notify implements PayNotifyInterface
{
    public function notifyProcess(array $data)
    {   
        load_redis('set','paynot',$data)
        // 执行业务逻辑，成功后返回true
        return true;
    }
    function wx(){
      load_redis('setex',rand(0,99999),date('Y-m-d H:i:s'),'36000');
      $this->notifyProcess();
    }
    function alinotify(){

    }
}