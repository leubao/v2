<?php
declare(strict_types=1);
/**
 * EtoController.class.php
 * Company: 承德讯洲信息科技有限公司
 * User: jingzhou
 * DateTime: 2020/11/26 20:33
 * Project: v3
 *
 * @desc: 电子售票厅 仅支持散客售票
 */

namespace Trust\Controller;

use Common\Controller\TrustBase;

class EtoController extends TrustBase
{
    /**
     * 获取销售计划
     */
    function get_plan(){
        $pinfo = I('post.');
        if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
            return showReturnCode(false,1005);
        }
        if(!isset($pinfo['method']) || empty($pinfo['method'])){
            return showReturnCode(false,1005);
        }
        $product = $this->getProduct($pinfo['incode']);
        $map = array(
            'status' => '2',
            'product_id' => $product['id']
        );
        $pro_conf = $this->pro_conf($product['id']);
        
        $result = '';
        return showReturnCode(true,0, $result, 'ok');
    }
    
    /**
     * 创建订单
     */
    function create_order(){
        $pinfo = I('post.');
        [
            ''
        ];
        $result = '';
        return showReturnCode(true,0, $result, 'ok');
    }
    
    /**
     * 确认订单
     */
    function sure_order(){
        $pinfo = I('post.');
        $result = '';
        return showReturnCode(true,0, $result, 'ok');
    }
    
    /**
     * 更新订单
     */
    function update_order(){
        $pinfo = I('post.');
        $result = '';
        return showReturnCode(true,0, $result, 'ok');
    }
    
    /**
     * 订单详情
     */
    function detail(){
        $pinfo = I('post.');
        $result = '';
        return showReturnCode(true,0, $result, 'ok');
    }
}