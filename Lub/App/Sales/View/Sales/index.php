<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--工具条 s-->
<Managetemplate file="Common/Nav"/>
<!--工具条 e--> 
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Sales/sales/index',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <input type="hidden" name="user.id" value="">
    <input type="text" name="user.name" readonly value="" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_user',array('type'=>5,'ifadd'=>2));}" data-group="user" data-width="600" data-height="445" data-title="业务员" placeholder="业务员">
    <input type="text" value="{$map.phone}" name="phone" class="form-control" size="10" placeholder="手机号">&nbsp;
    <input type="text" value="{$map.legally}" name="legally" class="form-control" size="10" placeholder="身份证号">&nbsp;
    <select name="status" data-toggle="selectpicker">
        <option value="">状态</option>
        <option value="1" <if condition="$map.status eq '1'">selected</if>>正常</option>
        <option value="3" <if condition="$map.status eq '3'">selected</if>>待审核</option>
        <option value="0" <if condition="$map.status eq '0'">selected</if>>已作废</option>
    </select>
    <select name="type" data-toggle="selectpicker">
        <option value="">分销类型</option>
        <option value="8" <if condition="$map.type eq '8'">selected</if>>全员分销</option>
        <option value="9" <if condition="$map.type eq '9'">selected</if>>三级分销</option>
    </select>
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>
<div class="bjui-pageContent tableContent">
  <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
    <thead>
      <tr>
        <th>姓名</th>
        <th>分销类型</th>
        <th>分组</th>
        <th>编号</th>
        <th width="70">状态</th>
        <th>余额</th>
        <th>创建时间</th> 
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td>{$vo.nickname}</td>
        <td>{$vo.type|sales_type}</td>
        <td>{$vo.groupid|crmgroupName}</td>
        <td>{$vo.legally}</td>
        <td>{$vo.status|status}</td>
        <td>{$vo.cash}</td>
        <td>{$vo.create_time|date="Y-m-d H:i:s",###}</td>
        <td>
          <a href="{:U('Sales/Full/qrcode',array('id'=>$vo['id']));}" data-toggle="dialog" data-width="600" data-height="500" data-id="fullqr">二维码</a>
          
          <a href="{:U('Sales/Full/edit',array('id'=>$vo['id']));}" data-toggle="dialog" data-width="600" data-height="500" data-id="full_edit">编辑</a>
          <a href="{:U('Sales/Sales/unbundling',array('id'=>$vo['id']));}" data-toggle="dialog" data-width="600" data-height="500" data-id="full_edit">解绑</a>
          <!--|
          <a href="{:U('Sales/Full/order',array('id'=>$vo.id));}">订单</a>|
          <a href="{:U('Sales/Full/income',array('id'=>$vo.id));}">收入</a>-->
        </td>
      </tr>
    </volist>
    </tbody>
  </table>
</div>
<div class="bjui-pageFooter">
  <div class="pages">
    <span>共 {$totalCount} 条</span>
  </div>
  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
</div>