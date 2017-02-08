<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Product/up_area',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
  	<div class="panel panel-default">
      <div class="panel-body">
        注意:1、更新区域只能操作已经写入计划的区域
        	2、取消选中将停止该区域门票销售
      </div>
    </div>
	<table class="table table-striped table-bordered">
	    <tbody>
	      <tr>
	        <td width="100px">当前计划</td>
	        <td><strong>{$id|planShow}</strong></td>
	      </tr>
	      <tr>
	        <td width="100px">区域</td>
	        <td>
		        <volist name="area" id="vo">
		        <input type="checkbox" name="area[]" value="{$vo['id']}" <if condition="in_array($vo['id'],$seat)"> checked</if>>{$vo.name}<br/>
		        </volist>
	        </td>
	      </tr>
      	</tbody>
    </table>
  </div>
  <input name="id" value="{$id}" type="hidden">
  <div class="bjui-pageFooter">
    <ul>
      <li>
        <button type="button" class="btn-close" data-icon="close">取消</button>
      </li>
      <li>
        <button type="submit" class="btn-default" data-icon="save">保存</button>
      </li>
    </ul>
  </div>
</form>