<?php if (!defined('LUB_VERSION')) exit(); ?>

<div class="bjui-pageHeader">

<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Report/Report/quota_lass',array('menuid'=>$menuid));}" method="post">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <select class="required" name="plan_id" id="plan" data-toggle="selectpicker">
        <option value="">+=^^=销售计划=^^=+</option>
        <volist name="plan" id="vo">
          <?php $ptime =  $vo['plantime']."-".$vo['games'];?>
          <option value="{$vo.id}"  <if condition="$plan_id eq $vo['id']">selected</if>>{$vo.plantime|date="Y-m-d",###} <if condition="$product['type'] eq 1"> 第{$vo.games}场 {$vo.starttime|date="H:i",###}</if>
          </option>
        </volist>
    </select>
    <label>级别：</label>
    <select name="level" data-toggle="selectpicker">
      <option value="16" <if condition="$level eq '16'">selected</if>>一级</option>
      <option value="17" <if condition="$level eq '17'">selected</if>>二级</option>
      <option value="18" <if condition="$level eq '18'">selected</if>>三级</option>
    </select>
    &nbsp;
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>

    <div class="btn-group f-right" role="group"> 
        <a type="button" class="btn btn-default" onclick="$(this).navtab('refresh');" data-placement="left" data-toggle="tooltip" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
        <a type="button" class="btn btn-default" href="http://www.kancloud.cn/leubao/leubao?token=NcdU7yGVqj5e" target="_blank" data-placement="left" data-toggle="tooltip" title="使用帮助"><i class="fa fa-question-circle"></i></a>
      </div>
  </div>
  <!--检索条件 e-->
</form>

<!--Page end-->
</div>
<div class="bjui-pageContent tableContent">
  <if condition="$data neq '404'">
    <table class="table table-bordered table-hover w900">
    <thead>
      <tr>
        <th width="56px">编号</th>
        <th>渠道商</th>
        <th>标准</th>
        <th>已使用</th>
      </tr>
    </thead>
    <tbody>
      <volist name="data" id="vo">
            <tr>
              <td>{$i}</td>
              <td>{$vo.channel_id|crmName}</td>
              <td>{$vo.channel_id|crmQuota}</td>
              <td>{$vo.number}</td>
            </tr>
          </volist>
    </tbody>
    </table>
  <else />
  <table class="table table-bordered">
  <tbody>
  <tr><td style='padding:15px;' align='center'><strong style='color:red;font-size:48px;'>未找到相关信息</strong></td></tr>
  </tbody>
  </table>
  </if>
</div>
<div class="bjui-pageFooter">
  <ul>
    <li>
      <button type="button" class="btn-close" data-icon="close">关闭</button>
    </li>
  </ul>
</div>