<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title" id="myModalLabel">新增员工</h4>
</div>
<div class="modal-body">
  <div class="panel panel-default cler_mag_20"> 
    <!-- Default panel contents -->
    <form action="{:U('Home/User/add');}" method="post">
      <div class="panel-body form-horizontal">
        <div class="form-group">
          <label class="col-sm-2 control-label">姓名</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="nickname" placeholder="如：姓名" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">用户名</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="username" placeholder="如：用于系统登录" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">手机号码</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="phone" placeholder="如：18631450000" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">Email</label>
          <div class="col-sm-8">
            <input type="email" class="form-control" name="email" value="{$data.email}" placeholder="如：xx@chengde360.com" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">密码</label>
          <div class="col-sm-8">
            <input type="password" class="form-control" name="password">
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">重复密码</label>
          <div class="col-sm-8">
            <input type="password" class="form-control" name="pwdconfirm">
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">所属角色</label>
          <div class="col-sm-4">
            <select name="role_id" class="form-control">
              <volist name="level" id="vo">
                <option value="{$vo.id}">{$vo.name}</option>
              </volist>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">支付方式</label>
          <div class="col-sm-8">
           <label class="radio-inline">
			  <input type="radio" name="is_pay" value="1" checked>授信额
			</label>
			<label class="radio-inline">
			  <input type="radio" name="is_pay" value="2" disabled>网银支付
			</label>
			<label class="radio-inline">
			  <input type="radio" name="is_pay" value="3" disabled>授信+网银
			</label>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">状态</label>
          <div class="col-sm-3">
            <select name="status" class="form-control">
              <option value="1"  <eq name="$data.status" value="1">selected="selected"</eq> >启用
              </option>
              <option value="0" <eq name="$data.status" value="0">selected="selected"</eq> >禁用
              </option>
            </select>
          </div>
        </div>
      </div>
      <input type="hidden" name="cid" value="{$crm.id}">
      <input type="hidden" name="item_id" value="{$crm.itemid}">
      <input type="hidden" name="product" value="{$crm.product_id}">
      <input type="hidden" name="groupid" value="{$crm.groupid}">
      <div class="panel-footer">
        <button type="submit" class="btn btn-success fright" >提交</button>
        <button type="reset" class="btn btn-default">重置</button>
      </div>
    </form>
  </div>
</div>