<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageContent">
<table class="table table-striped table-bordered table-hover">
      <tbody>
        <tr>
          <td width="100px">姓名</td>
          <td width="500px">{$data.nickname}</td> 
          <td width="100px">创建日期</td>
          <td>{$data.create_time|datetime}</td>
        </tr>
        <tr>
          <td>身份证:</td>
          <td>{$data.idcard}</td>
          <td>手机号:</td>
          <td>{$data.phone}</td>
        </tr>
        <tr>
          <td>会员号:</td>
          <td>{$data.no_number}</td>
          <td>会员类型:</td>
          <td>{$data.group_id|memGroup}</td>
        </tr>
        <tr>
          <td>入园次数</td>
          <td>{$data.number}</td>
          <td>最后入园时间</td>
          <td>{$data.update_time|datetime}</td>
        </tr>
        <tr>
          <td>办理方式</td>
          <td><if condition="$data['source'] eq 5"> 自助办理 <else /> 窗口办理 </if></td>
          <td>状态</td>
          <td>{$data.status|status}</td> 
        </tr>
        <if condition="$data['source'] eq 5">
        <tr>
          <td>微信openid</td>
          <td>{$data.openid}</td> 
          <td></td>
          <td></td> 
        </tr>
        </if>
        <tr>
          <td>备注</td>
          <td colspan="3">{$data.remark}</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<div class="bjui-pageFooter">
  <ul>
    
    <li>
      <button type="button" class="btn-close" data-icon="close">关闭</button>
    </li>
    <if condition="$data['status'] eq '1' ">
    <li>
      <button type="button" class="btn-info" data-icon="print" data-url="{:U('Item/Order/drawer',array('id'=>$data['id'],'genre'=>6));}" data-width="213" data-height="208" data-title="打印临时凭证" data-pageid="print" id="print_window_member" id="print_window">打印临时凭证</button>
    </li>
    </if>
    
  </ul>
</div>
<script>
$('#print_window_member').click(function(){
    /*关闭订单详情的窗口*/
    $(this).dialog('close','memberinfo');
    $(this).dialog({id:''+$(this).data('pageid')+'', url:''+$(this).data('url')+'', title:''+$(this).data('title')+'',width:''+$(this).data('width')+'',height:''+$(this).data('height')+'',resizable:false,maxable:false,mask:true});
});
</script>