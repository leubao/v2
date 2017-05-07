<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
  <form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Crm/Index/grouplist',array('menuid'=>$menuid,'id'=>$groupid,'channel'=>$type));}" method="post">
    <input type="hidden" name="pageCurrent" value="{$currentPage}" />
    <input type="hidden" name="pageSize" value="{$numPerPage}" />
  
    <div class="bjui-searchBar">
    <if condition="$type neq '4'">
      <div class="btn-group" role="group"> 
        <a type="button" class="btn btn-success" href="{:U('Crm/Index/add',array('menuid'=>$menuid,'groupid'=>$groupid,'channel'=>$type));}" data-toggle="dialog" data-width="800" data-height="480" data-id="新增" data-mask="true"><i class="fa fa-plus"></i> 新增</a> 
        <a type="button" class="btn btn-info" href="{:U('Crm/Index/edit',array('groupid'=>$groupid,'channel'=>$type));}&id={#bjui-selected}" data-toggle="dialog" data-width="800" data-height="430" data-id="编辑"><i class="fa fa-pencil"></i> 编辑</a> 
        <a type="button" class="btn btn-warning" href="{:U('Crm/Index/start_us',array('menuid'=>$menuid,'groupid'=>$groupid,'channel'=>$type));}&id={#bjui-selected}" data-toggle="doajax" data-confirm-msg="你确定要停用/启用吗？"><span><i class="fa fa-youtube-play"></i> 停用/启用</a>
        <a type="button" class="btn btn-danger" href="{:U('Crm/Index/delete',array('menuid'=>$menuid,'groupid'=>$groupid,'channel'=>$type));}&id={#bjui-selected}" data-toggle="doajax" data-confirm-msg="确定要删除选中项吗？"><i class="fa fa-trash-o"></i> 删除</a> 
        <a type="button" class="btn btn-primary" href="{:U('Crm/Index/userslist',array('menuid'=>$menuid,'groupid'=>$groupid,'channel'=>$type))}&cid={#bjui-selected}" data-toggle="dialog" data-width="800" data-height="400" data-id="员工管理" data-mask="true" data-id="crm_user"><i class="fa fa-users"></i> 员工</a> 
        <a type="button" class="btn btn-default" href="{:U('Crm/Index/checkcash',array('menuid'=>$menuid,'groupid'=>$groupid,'channel'=>$type))}&id={#bjui-selected}" data-toggle="dialog" data-mask="true" data-max="true" data-id="checkcash"><i class="fa fa-cc-visa"></i> 授信</a>
         <a type="button" class="btn btn-default" href="{:U('Crm/Index/quota',array('menuid'=>$menuid,'groupid'=>$groupid,'channel'=>$type))}&id={#bjui-selected}" data-mask="true" data-toggle="dialog" data-width="800" data-height="400" data-id="编辑"><i class="fa fa-life-ring"></i> 配额</a>
         <a type="button" class="btn btn-default" href="{:U('Crm/Index/auth_product',array('menuid'=>$menuid,'groupid'=>$groupid,'channel'=>$type))}&id={#bjui-selected}" data-mask="true" data-toggle="dialog" data-width="800" data-height="400" data-id="产品权限"><i class="fa fa-life-ring"></i> 产品权限</a>

      </div>
      <else />
        <div class="bjui-searchBar">
          <div class="btn-group" role="group">
          <a type="button" class="btn btn-default" href="{:U('Crm/Index/checkcash',array('menuid'=>$menuid,'groupid'=>$groupid,'channel'=>$type))}&id={#bjui-selected}" data-toggle="dialog" data-mask="true" data-max="true" data-id="checkcash"><i class="fa fa-cc-visa"></i> 授信</a>
          </div>
        </div>
     </if>
      <label>名称：</label>
      <input type="text" value="" name="name" size="15">
      &nbsp;
      <button type="button" class="showMoreSearch" data-toggle="moresearch" data-name="custom"><i class="fa fa-angle-double-down"></i></button>
      <div class="bjui-moreSearch">
        <label>管理员：</label>
        <input type="hidden" name="user.id" value="">
        <input type="text" disabled name="user.name" value="" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/user',array('type'=>1));}" data-group="user" data-width="600" data-height="445">
        <if condition="$type eq 1">
        <label>级别：</label>
        <select name="level" data-toggle="selectpicker">
          <option value="">全部</option>
          <option value="16" <if condition="$map['level'] eq '16'">selected</if>>一级</option>
          <option value="17" <if condition="$map['level'] eq '17'">selected</if>>二级</option>
          <option value="18" <if condition="$map['level'] eq '18'">selected</if>>三级</option>
        </select>
        </if>
        <select name="status" data-toggle="selectpicker">
          <option value="">状态</option>
          <option value="1" <if condition="$map['status'] eq '1'">selected</if>>启用</option>
          <option value="0" <if condition="$map['status'] eq '0'">selected</if>>禁用</option>
        </select>
      </div>
      <div class="btn-group f-right" role="group">
      <a type="button" class="btn btn-default" href="http://www.leubao.com/index.php?g=Manual&a=show&sid=59" target="_blank" data-placement="left" data-toggle="tooltip" title="使用帮助"><i class="fa fa-question-circle"></i></a>
      
      </div>
      <button type="submit" class="btn-default" data-icon="search">查询</button>
      &nbsp; <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a> </div>
      
    <input type="hidden" name="id" value="{$groupid}" />
    <input type="hidden" name="type" value="{$type}" />
  </form>
</div>
<div class="bjui-pageContent tableContent">  
  <if condition="$type neq '4'">
    <table data-toggle="tablefixed" data-width="100%">
      <thead>
        <tr>
          <th width="30">编号</th>
          <th width="180">客户名称</th>
          <th width="60" align="center">状态</th>
          <th width="60">管理员</th>
          <th width="130" align="center">更新时间</th>
          <th width="110">可用金额</th>
          <if condition="($type eq 1) AND ($proconf['agent'] eq '1')">
            <th width="100">级别</th>
          </if>
        </tr>
      </thead>
      <tbody>
        <volist name="data" id="vo">
          <tr data-id="{$vo['id']}">
            <td>{$vo['id']}</td>
            <td><a href="{:U('Crm/Index/detail',array('id'=>$vo['id']))}" data-toggle="dialog" data-width="800" data-height="450" data-id="detail" data-mask="true">{$vo['name']}</a></td>
            <td align="center">{$vo.status|status}</td>
            <td>{$vo['salesman']|userName}</td>
            <td>{$vo['uptime']|datetime}</td>
            <td>{$vo['cash']}</td>
            <if condition="($type eq 1) AND ($proconf['agent'] eq '1')">
              <td><if condition="$vo['level'] eq '16'">一级<elseif condition="$vo['level'] eq '17'"/>二级<else/>三级</if></td>
            </if>
          </tr>
        </volist>
      </tbody>
    </table>
  <else />
    <table data-toggle="tablefixed" data-width="100%">
      <thead>
        <tr>
          <th width="30">编号</th>
          <th width="180">姓名</th>
          <th width="80">手机号</th>
          <th width="60" align="center">状态</th>
          <th width="130" align="center">创建时间</th>
          <th width="130" align="center">更新时间</th>
          <th width="110">可用金额</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        <volist name="data" id="vo">
          <tr data-id="{$vo['id']}">
            <td>{$vo['id']}</td>
            <td><a href="{:U('Crm/Index/detail',array('id'=>$vo['id'],'channel'=>$type))}" data-toggle="dialog" data-width="800" data-height="450" data-id="detail" data-mask="true">{$vo['nickname']}</a></td>
            <td>{$vo['phone']}</td>
            <td align="center">{$vo.status|status}</td>
            <td>{$vo['create_time']|datetime}</td>
            <td>{$vo['uptime']|datetime}</td>
            <td>{$vo['cash']}</td>
            <td><a href="{:U('Wechat/Cashback/index',array('id'=>$vo['id'],'menuid'=>'549'))}" data-max="true" data-toggle="dialog" data-id="cashback" data-mask="true">补贴</a>|<a href=""></a></td>
          </tr>
        </volist>
      </tbody>
    </table>
  </if>
</div>
<div class="bjui-pageFooter">
  <div class="pages"> <span>共 {$totalCount} 条</span> </div>
  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
</div>