<form id="pagerForm" onsubmit="return divSearch(this, 'gloupBox');" action="{:U('Crm/Index/grouplist')}" method="post">
  <input type="hidden" name="pageNum" value="1" />
  <!--【必须】value=1可以写死-->
  <input type="hidden" name="numPerPage" value="10" />
  <!--【可选】每页显示多少条-->
  <input type="hidden" name="id" value="asc" />
  <!--【可选】升序降序-->
  <input type="hidden" name="groupid" value="{$groupid}" />
  <input type="hidden" name="navTabId" value="{$navTabId}" />
  <div class="panelBar">
    <ul class="toolBar">
      <li>
        <label>客户名称：</label>
        <input type="text" name="name" class="" value="{$name}">
      </li>
      <li>
        <label>联系电话：</label>
        <input type="text" name="phone" class="" value="{$phone}">
      </li>
      <if condition="($type eq 1) AND ($proconf['agent'] eq '1')">
      <li>
        <label>级别：</label>
        <select name="level">
          <option value="">不限级别</option>
          <volist name="levelid" id="le"> 
          <option value="{$le.id}" <if condition="$le['id'] eq $level">selected</if>>{$le.name}</option>
          </volist>
        </select>
      </li>
      </if>
      <li>
        <input type="hidden" name="id" value="{$groupid}" />
        <input type="hidden" name="navTabId" value="{$navTabId}" />
        <input type="hidden" name="type" value="{$type}">
        <button type="submit">检 索</button>
      </li>
    </ul>
  </div>
</form>
<if condition="$type eq '1' OR $type eq '3'">
<div class="panelBar">
  <ul class="toolBar">
    <li><a class="add" href="{:U('Crm/Index/add',array('navTabId'=>$navTabId,'groupid'=>$groupid,'type'=>$type));}" target="dialog" rel="{$navTabId}" mask="true" width="610" height="500"><span>新增商户</span></a></li>
    <li><a class="delete" href="{:U('Crm/Index/delete',array('navTabId'=>$navTabId,'groupid'=>$groupid,'type'=>$type));}&id={gid}" rel="{$navTabId}" target="ajaxTodo" calback="navTabAjaxMenu" title="你确定要删除吗？" warn="请选择客户名称"><span>删除商户</span></a></li>
    <li><a class="edit" href="{:U('Crm/Index/edit',array('navTabId'=>$navTabId,'groupid'=>$groupid,'type'=>$type));}&id={gid}" rel="{$navTabId}" target="dialog" mask="true" width="610" height="500" warn="请选择一条数据"><span>修改商户</span></a></li>
    <li><a class="icon" href="{:U('Crm/Index/userslist',array('navTabId'=>$navid,'groupid'=>$groupid,'type'=>$type))}&cid={gid}" rel="{$navTabId}" target="dialog" mask="true" warn="请选择一条数据"><span>员工管理</span></a></li>
    <li><a class="icon" href="{:U('Crm/Index/checkcash',array('navTabId'=>$navid,'groupid'=>$groupid,'type'=>$type))}&id={gid}" target="navTab" rel="checkcash" warn="请选择一条数据"><span>授信管理</span></a></li>
    <li><a class="icon" href="{:U('Crm/Index/quota',array('navTabId'=>$navTabId,'groupid'=>$groupid,'type'=>$type))}&id={gid}" rel="{$navTabId}" target="dialog" mask="true" warn="请选择一条数据"><span>管理配额</span></a></li>
    <li><a class="icon" href="{:U('Crm/Index/start_us',array('navTabId'=>$navTabId,'groupid'=>$groupid,'type'=>$type));}&id={gid}" rel="{$navTabId}" target="ajaxTodo" calback="navTabAjaxMenu" title="你确定要停用/启用吗？" warn="请选择客户名称"><span>停用/启用</span></a></li>
  </ul>
</div>
<table class="table" width="100%" layoutH="103">
  <thead>
    <tr>
      <th width="30">编号</th>
      <th width="180">客户名称</th>
      <th width="60">销售配额</th>
      <th width="30" align="center">状态</th>
      <th width="60">管理员</th>
      <th width="110">添加时间</th>
      <th width="130">可用金额</th>
      <if condition="($type eq 1) AND ($proconf['agent'] eq '1')">
      <th width="100">级别</th>
      </if>
    </tr>
  </thead>
  <tbody>
    <volist id="vo" name="list">
      <tr target="gid" rel="{$vo['id']}">
        <td>{$vo['id']}</td>
        <td><a href="{:U('Crm/Index/detail',array('id'=>$vo['id']))}" target="dialog">{$vo['name']}</a></td>
        <td>{$vo['quota']}</td>
        <td><if condition=" $vo['status'] eq '1' "><img title="可用" src="{$config_siteurl}statics/images/icon/y.png">
            <else />
            <img title="禁止" src="{$config_siteurl}statics/images/icon/x.png"></if></td>
        <td>{$vo['salesman']|userName}</td>
        <td>{$vo['create_time']|date="Y-m-d H:i",###}</td>
        <td>{$vo['cash']}</td>
        <if condition="($type eq 1) AND ($proconf['agent'] eq '1')">
        <td><?php echo D('Home/Role')->getRoleIdName($vo['level'])?></td>
        </if>
        <!--
        <td><a target="dialog" href="{:U('Crm/Index/userslist',array('navTabId'=>$navid,'groupid'=>$groupid,'cid'=>$vo['id']))}">员工</a> | <a target="dialog" href="{:U('Crm/Index/checkcash',array('navTabId'=>$navid,'groupid'=>$groupid,'cid'=>$vo['id']))}">授信</a> | <a target="dialog" href="{:U('Crm/Index/checkcash',array('navTabId'=>$navid,'groupid'=>$groupid,'cid'=>$vo['id']))}">业绩</a></td>
      -->
      </tr>
    </volist>
  </tbody>
</table>
<else />
<div class="panelBar">
    <ul class="toolBar">
      <li><a class="add" href="{:U('Crm/Index/add',array('navTabId'=>$navTabId,'groupid'=>$groupid,'type'=>$type));}" rel="{$navTabId}" width="610" height="500" target="dialog" mask="true"><span>新增客户</span></a></li>
      <li><a class="delete" href="{:U('Crm/Index/delete',array('navTabId'=>$navTabId,'groupid'=>$groupid,'type'=>$type));}&id={gid}" rel="{$navTabId}" target="ajaxTodo" calback="navTabAjaxMenu" title="你确定要删除吗？" warn="请选择客户名称"><span>删除客户</span></a></li>
      <li><a class="edit" href="{:U('Crm/Index/editusers',array('navTabId'=>$navTabId,'groupid'=>$groupid,'type'=>$type));}&id={gid}&type=other" rel="{$navTabId}" target="dialog" mask="true" width="610" height="500" warn="请选择客户名称"><span>修改客户</span></a></li>
      <li><a class="delete" href="{:U('Crm/Index/reset_pwd',array('navTabId'=>$navTabId,'groupid'=>$groupid,'cid'=>$cid));}&id={gid}" target="ajaxTodo" calback="navTabAjaxMenu" title="你确定要重置密码吗？" warn="请选择客户"><span>重置密码</span></a></li>
    </ul>
  </div>
<table class="table" width="100%" layoutH="103">
    <thead>
    <tr>
      <th width="60">编号</th>
      <th width="160">客户名称</th>
      <th>电话</th>
      <th width="80">状态</th>
      <th width="120">管理员</th>
      <th width="120">添加时间</th>
    </tr>
    </thead>
    <tbody>
    <volist id="vo" name="list">
      <tr target="gid" rel="{$vo['id']}">
        <td>{$vo['id']}</td>
        <if condition="$type eq 1">
          <td><a href="{:U('Crm/Index/detail',array('id'=>$vo['id']))}" target="dialog">{$vo['nickname']}</a></td>
        <else/>
          <td><a href="{:U('Crm/Index/gdetail',array('id'=>$vo['id']))}" target="dialog">{$vo['nickname']}</a></td>
          </if>
        <td>{$vo['phone']}</td>
        <td><if condition=" $vo['status'] eq '1' "><img title="可用" src="{$config_siteurl}statics/images/icon/y.png">
                <else />
                <img title="禁止" src="{$config_siteurl}statics/images/icon/x.png"></if></td>
        <td>{$vo.salesman|userName}</td>
        <td>{$vo['create_time']|date="Y-m-d H:i:s",###}</td>
      </tr>
    </volist>
    </tbody>
</table>
</if>
<div class="panelBar">
  <div class="pages"> <span>共{$totalCount}条</span> </div>
  <div class="pagination" rel="gloupBox" targetType="navTab" totalCount="{$totalCount}" numPerPage="{$numPerPage}" pageNumShown="10" currentPage="{$currentPage}"></div>
</div>