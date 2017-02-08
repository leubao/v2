<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title" id="myModalLabel">用户-{$data.nickname}修改密码</h4>
</div>
<form action="{:U('Home/User/passwords');}" method="post">
  <div class="modal-body">
    <div class="panel panel-default cler_mag_20"> 
      <!-- Default panel contents -->
      
      <div class="panel-body form-horizontal">
        <div class="form-group">
          <div class="form-group">
            <label class="col-sm-2 control-label">旧密码</label>
            <div class="col-sm-8">
              <input type="password" class="form-control" name="password" required>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">新密码</label>
            <div class="col-sm-8">
              <input type="password" class="form-control" name="new_password" required>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">重复密码</label>
            <div class="col-sm-8">
              <input type="password" class="form-control" name="new_pwdconfirm" required>
            </div>
          </div>
          <input type="hidden" name="id" value="{$data.id}" />
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-success" >提交</button>
    <button type="reset" class="btn btn-default">重置</button>
  </div>
</form>
