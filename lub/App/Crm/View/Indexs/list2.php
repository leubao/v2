  <form id="pagerForm" action="{:U('Crm/Index/grouplist')}" method="post">
    <input type="hidden" name="pageNum" value="1" /><!--【必须】value=1可以写死-->
    <input type="hidden" name="numPerPage" value="10" /><!--【可选】每页显示多少条-->
    <input type="hidden" name="id" value="asc" /><!--【可选】升序降序-->
    <input type="hidden" name="groupid" value="{$groupid}" />
    <input type="hidden" name="navTabId" value="{$navTabId}" />
  </form> 
  <div class="panelBar">
    <ul class="toolBar">
      <li><a class="add" href="{:U('Crm/Index/add',array('navTabId'=>$navTabId,'groupid'=>$groupid));}" rel="{$navTabId}" width="610" height="500" target="dialog" mask="true"><span>新增客户</span></a></li>
      <li><a class="delete" href="{:U('Crm/Index/delete',array('navTabId'=>$navTabId,'groupid'=>$groupid));}&id={gid}" rel="{$navTabId}" target="ajaxTodo" calback="navTabAjaxMenu" title="你确定要删除吗？" warn="请选择客户名称"><span>删除客户</span></a></li>
      <if condition="$type eq 3 OR $type eq 2">
        <li><a class="edit" href="{:U('Crm/Index/edit',array('navTabId'=>$navTabId,'groupid'=>$groupid));}&id={gid}&type=other" rel="{$navTabId}" target="dialog" mask="true" width="610" height="500" warn="请选择客户名称"><span>修改客户</span></a></li>
      <else/>
        <li><a class="edit" href="{:U('Crm/Index/edit',array('navTabId'=>$navTabId,'groupid'=>$groupid));}&id={gid}" rel="{$navTabId}" target="dialog" mask="true" width="610" height="500" warn="请选择客户名称"><span>修改客户</span></a></li>
      </if>
    </ul>
  </div>
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
  <div class="panelBar">
    <div class="pages">
      <span>共{$totalCount}条</span>
    </div>
    <div class="pagination" targetType="navTab"  rel="gloupBox" totalCount="{$totalCount}" numPerPage="{$numPerPage}" pageNumShown="10" currentPage="{$currentPage}"></div>
  </div>