<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Manage/Product/index',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">       
</form>
<!--Page end-->
<div class="bjui-pageHeader">
<Managetemplate file="Common/Nav"/>
</div>
<div class="bjui-pageContent tableContent">
  <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
    <thead>
      <tr>
        <th align="center">产品名称</th>
        <th align="center">识别码</th>
        <th align="center">类型</th>
        <th align="center">状态</th>
        <th align="center">添加时间</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td>{$vo.name}</td>
        <td>{$vo.idcode}</td>
        <td align="center"><if condition="$vo['type'] eq 1">剧院产品<elseif condition="$vo['type'] eq 2"/>景区产品<elseif condition="$vo['type'] eq 3"/>漂流产品</if></td>
        <td align="center">{$vo.status|status}</td>
        <td align="center">{$vo.createtime|datetime}</td>
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