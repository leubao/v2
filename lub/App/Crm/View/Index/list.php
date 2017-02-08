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
      <li>
        <label>级别：</label>
        <select name="level">
          <option value="">不限级别</option>
          <volist name="levelid" id="le"> <option value="{$le.id}" 
            <if condition="$le['id'] eq $level">selected</if>
            >{$le.name}
            </option>
          </volist>
        </select>
      </li>
      <li>
        <input type="hidden" name="id" value="{$groupid}" />
        <input type="hidden" name="navTabId" value="{$navTabId}" />
        <button type="submit">检 索</button>
      </li>
    </ul>
  </div>
</form>
<div class="panelBar">
  <ul class="toolBar">
    <li><a class="add" href="{:U('Crm/Index/add',array('navTabId'=>$navTabId,'groupid'=>$groupid));}" target="dialog" rel="{$navTabId}" mask="true" width="610" height="500"><span>新增渠道商</span></a></li>
    <li><a class="delete" href="{:U('Crm/Index/delete',array('navTabId'=>$navTabId,'groupid'=>$groupid));}&id={gid}" rel="{$navTabId}" target="ajaxTodo" calback="navTabAjaxMenu" title="你确定要删除吗？" warn="请选择客户名称"><span>删除客户</span></a></li>
    <li><a class="edit" href="{:U('Crm/Index/edit',array('navTabId'=>$navTabId,'groupid'=>$groupid));}&id={gid}" rel="{$navTabId}" target="dialog" mask="true" width="610" height="500" warn="请选择一条数据"><span>修改客户</span></a></li>
    <li><a class="icon" href="{:U('Crm/Index/userslist',array('navTabId'=>$navid,'groupid'=>$groupid))}&cid={gid}" rel="{$navTabId}" target="dialog" mask="true" warn="请选择一条数据"><span>新增员工</span></a></li>
    <li><a class="icon" href="{:U('Crm/Index/recharge',array('navTabId'=>$navid,'groupid'=>$groupid))}&id={gid}" rel="{$navTabId}" target="dialog" mask="true" warn="请选择一条数据"><span>充值</span></a></li>
    <li><a class="icon" href="{:U('Crm/Index/quota',array('navTabId'=>$navTabId,'groupid'=>$groupid))}&id={gid}" rel="{$navTabId}" target="dialog" mask="true" warn="请选择一条数据"><span>管理配额</span></a></li>
  </ul>
</div>
<if condition="">
<table class="table" width="100%" layoutH="103">
  <thead>
    <tr>
      <th width="30">编号</th>
      <th width="120">客户名称</th>
      <th>销售配额</th>
      <th width="30" align="center">状态</th>
      <th>管理员</th>
      <th width="110">添加时间</th>
      <th>可用金额</th>
      <th>级别</th>
      <th>操作</th>
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
        <td>{$vo['salesman']}</td>
        <td>{$vo['create_time']}</td>
        <td>{$vo['cash']}</td>
        <td><?php echo D('Home/Role')->getRoleIdName($vo['level'])?></td>
        <td><a target="dialog" href="{:U('Crm/Index/userslist',array('navTabId'=>$navid,'groupid'=>$groupid,'cid'=>$vo['id']))}">员工</a> | <a target="dialog" href="{:U('Crm/Index/checkcash',array('navTabId'=>$navid,'groupid'=>$groupid,'cid'=>$vo['id']))}">授信</a> | <a target="dialog" href="{:U('Crm/Index/checkcash',array('navTabId'=>$navid,'groupid'=>$groupid,'cid'=>$vo['id']))}">业绩</a></td>
      </tr>
    </volist>
  </tbody>
</table>
</else>
<table class="table" width="100%" layoutH="75">
    <thead>
    <tr>
      <th width="60">编号</th>
      <th width="100">客户名称</th>
      <!-- <th>地址</th> 
      <th>联系人</th>-->
      <th>电话</th>
      <th width="100">状态</th>
      <!-- <th>相关销售人员</th> -->
      <th width="120">添加时间</th>
      <!--如果是导游，加上“录入指纹”的操作-->
      <if condition="$type eq 3">
        <th>操作</th>
      </if>
    </tr>
    </thead>
    <tbody>
    <volist id="vo" name="data">
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
        <td>{$vo['create_time']}</td>
        <!--如果是导游，加上“录入指纹”的操作-->
        <if condition="$type eq 3">
          <td>
            <if condition="$vo['finger1'] eq '' AND $vo['finger2'] eq ''">
              <a href="{:U('Crm/Index/fingerprint',array('id'=>$vo['id']))}" target="navTab" rel="external" external="true">录入指纹</a>
            <else/> 
              指纹已录入，<a href="{:U('Crm/Index/fingerprint',array('id'=>$vo['id']))}" target="navTab" rel="external" external="true" style="color:blue">点此修改</a>
            </if>
          </td>
        </if>
      </tr>
    </volist>
    </tbody>
  </table>
</if>
<div class="panelBar">
  <div class="pages"> <span>共{$totalCount}条</span> </div>
  <div class="pagination" rel="gloupBox" targetType="navTab" totalCount="{$totalCount}" numPerPage="{$numPerPage}" pageNumShown="10" currentPage="{$currentPage}"></div>
</div>
