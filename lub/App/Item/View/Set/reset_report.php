<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Set/reset_report',array('menuid'=>$menuid));}" method="post">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <label>日期:</label>
    <input type="text" size="11" name="datetime" data-toggle="datepicker" value="{$datetime}">
    &nbsp;
    <label>&nbsp;类型:</label>
    <select name="type" data-toggle="selectpicker">
        <option value="">类型</option>
        <option value="1" <eq name="type" value="1"> checked</eq>>清除数据</option>
        <option value="2" <eq name="type" value="2"> checked</eq>>生成数据</option>
    </select>
    <button type="submit" class="btn-default" data-icon="search">提交</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>
<div class="bjui-pageContent tableContent">
  
</div>
<div class="bjui-pageFooter">
  <div class="pages">
    <span></span>
  </div>
</div>