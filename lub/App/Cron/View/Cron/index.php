<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Manage/Cache/cache',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
</form>
<!--Page end-->
<div class="bjui-pageHeader">
  <Managetemplate file="Common/Nav"/>
</div>
<div class="bjui-pageContent tableContent">
<div class="prompt_text">
    <ol>
      <li>计划任务是一项使系统在规定时间自动执行某些特定任务的功能。</li>
      <li>合理设置执行时间，能有效地为服务器减轻负担。</li>
      <li>触发任务除系统指定的时间外，用户行为也可触发。触发任务的任务周期只是初始值。</li>
      <li>想要计划任务顺利执行，需要一个触发媒介！ <br />独立主机用户可以在系统增加计划任务间隔20秒执行访问[http://网站地址/index.php?g=Cron&m=Index&a=index]。<br />虚拟主机用户，需要在网站模板中最底部增加一个js调用[&lt;script type="text/javascript" src="http://网站地址/index.php?g=Cron&m=Index&a=index"&gt;&lt;/script&gt;]以游客访问页面的形式触发！</li>
    </ol>
  </div>
  <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
    <thead>
        <tr>
          <td>计划标题</td>
          <td>任务周期</td>
          <td>任务状态</td>
          <td>上次执行时间</td>
          <td>下次执行时间</td>
        </tr>
    </thead>
    <tbody>
     <volist name="data" id="r">
      <?php
    $modified = $r['modified_time'] ? date("Y-m-d H:i",$r['modified_time']) : '-';
    $next = $r['next_time'] ? date("Y-m-d H:i",$r['next_time']) : '-';
    ?>
      <tr data-id="{$r['cron_id']}">
        <td>{$r.subject}</td>
        <td>{$r.type}</td>
        <td><if condition=" $r['isopen'] ">开启 <else />关闭</if></td>
        <td>{$modified}</td>
        <td>{$next}</td>
        
      </tr>
      </volist>
    </tbody>
  </table>
</div>
<div class="bjui-pageFooter">
  <div class="pages"> <span>共 {$totalCount} 条</span> </div>
  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
</div>