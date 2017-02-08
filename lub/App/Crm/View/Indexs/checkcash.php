<!--商户消费详情页面-->
<form id="pagerForm" action="{:U('Crm/Index/checkcash')}" method="post">
  <input type="hidden" name="pageNum" value="1" /><!--【必须】value=1可以写死-->
  <input type="hidden" name="numPerPage" value="10" /><!--【可选】每页显示多少条-->
  <input type="hidden" name="id" value="desc" /><!--【可选】升序降序-->
  <input type="hidden" name="id" value="{$cid}" />
  <input type="hidden" name="start_date" value="{$start_date}" />
  <input type="hidden" name="end_date" value="{$end_date}" />
</form>
<!--条件搜索 START-->
<div class="pageHeader">
  <form onsubmit="return navTabSearch(this);" action="{:U('Crm/Index/checkcash')}" method="post">
  <div class="searchBar">
    <table class="searchContent">
    <input name="id" type="hidden" value="{$cid}">
      <tr>
        <td>
          起始日期：<input type="text" name="start_date" class="date textInput readonly valid" readonly="true" value="{$start_date}">
        </td>
        <td>
          终止日期：<input type="text" name="end_date" class="date textInput readonly valid" readonly="true" value="{$end_date}">
        </td>
        <td><select name="type" class="required combox">
          <option value="" selected>类型</option>
          <option value="1" <eq name="type" value="1"> selected</eq>>充值</option>
          <option value="2" <eq name="type" value="2"> selected</eq>>消费</option>
          <option value="3" <eq name="type" value="3"> selected</eq>>补贴</option>
          <option value="4" <eq name="type" value="4"> selected</eq>>退票</option>
          <option value="4" <eq name="type" value="5"> selected</eq>>退款</option>
        </select></td>
        <td>
          <div class="subBar">
            <ul>
              <li><div class="buttonActive"><div class="buttonContent"><button type="submit">检索</button></div></div></li>
            </ul>
          </div>          
        </td>
      </tr>
    </table>
  </div>
  </form>
</div>
<!--条件搜素 END-->
<div class="pageContent"> 
<div class="panelBar">
    <ul class="toolBar">
      <li><span><strong>当前渠道商：</strong>{$cid|crmName}</span></li><li class="line">line</li>
      <li><a class="add" href="{:U('Crm/Index/recharge',array('navTabId'=>$navTabId,'cid'=>$cid,'type'=>'1'));}" rel="{$navTabId}" width="610" height="500" target="dialog" mask="true"><span>充值</span></a></li>
      <li><a class="add" href="{:U('Crm/Index/refund',array('navTabId'=>$navTabId,'cid'=>$cid,'type'=>'1'));}" rel="{$navTabId}" width="610" height="500" target="dialog" mask="true"><span>退款</span></a></li>
      <!--
      <li><a class="edit" href="{:U('Crm/Index/editusers',array('navTabId'=>$navTabId,'cid'=>$cid,'type'=>'2'));}" rel="{$navTabId}" target="dialog" mask="true" width="610" height="500" warn="请选择客户名称"><span>扣款</span></a></li>
      -->
     </ul>
  </div>
  <table class="table" width="100%" layoutH="112">
    <thead>
      <tr>
        <th width="80">ID</th>
        <th width="130">操作时间</th>
        <th width="50">类型</th>
        <th width="100">金额</th>
        <th width="100">余额</th>
        <th width="80">操作员</th>
        <th>备注</th>
      </tr>
    </thead>
    <tbody>
      <volist name="list" id="vo">
          <tr>
            <td>{$vo.id}</td>
            <td>{$vo.createtime|date="Y-m-d H:i:s",###}</td>
            <td>{$vo['type']|operation}</td>          
            <td>{$vo.cash}</td>
            <td>{$vo.balance}</td>
            <td>{$vo.user_id|userName}</td>
            <td>{$vo.remark}</td>
          </tr>
      </volist>
    </tbody>
  </table>
  <div class="panelBar">
    <div class="pages">
      <span>共{$totalCount}条</span>
    </div>
    <div class="pagination" targetType="navTab" totalCount="{$totalCount}" numPerPage="{$numPerPage}" pageNumShown="10" currentPage="{$currentPage}"></div>
  </div>
</div>