<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--工具条 s-->
<Managetemplate file="Common/Nav"/>
<!--工具条 e--> 
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Sales/Red/index',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 

  <!--检索条件 e-->
</form>
<!--Page end-->
</div>
<div class="bjui-pageContent tableContent"><?php //dump($data);?>
  <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
    <thead>
      <tr>
        <th>编号</th>
        <th>活动名称</th>
        <th>商户名称</th>
        <th>祝福语</th>
        <th>场景</th>
        <th width="70">状态</th>
        <th>创建时间</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td>{$i}</td>
        <td><a href="{:U('Sales/Sales/public_uinfo',array('id'=>$vo['id']));}" data-toggle="dialog" data-width="700" data-height="500" data-id="uinfo">{$vo.act_name}</a></td>
        <td>{$vo.send_name}</td>
        <td>{$vo.wishing}</td>
        <td>{$vo.scene_id}</td>
        <td>{$vo.status|status}</td>
        <td>{$vo.create_time|date="Y-m-d H:i:s",###}</td>
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