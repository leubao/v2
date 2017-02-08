<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title" id="myModalLabel">编辑联系人</h4>
</div>
<div class="modal-body">
  <div class="panel panel-default cler_mag_20"> 
    <!-- Default panel contents -->
    <form action="{:U('Home/Set/contact_edit');}" method="post">
      <div class="panel-body form-horizontal">
        <div class="form-group">
          <label class="col-sm-2 control-label">姓名</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="name" value="{$data.name}" placeholder="如：姓名" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">手机号码</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="phone" value="{$data.phone}" placeholder="如：18631450000" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">身份证号码</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="id_card" value="{$data.id_card}" placeholder="如：130123198809080000">
          </div>
        </div>
        <input type="hidden" name="id" value="{$data.id}"/>
        <div class="form-group">
          <label class="col-sm-2 control-label">状态</label>
          <div class="col-sm-3">
            <select name="status" class="form-control">
              <option value="1"  <eq name="data.status" value="1">selected="selected"</eq> >启用
              </option>
              <option value="0" <eq name="data.status" value="0">selected="selected"</eq> >禁用
              </option>
            </select>
          </div>
        </div>
      </div>
      <div class="panel-footer">
        <button type="submit" class="btn btn-success fright" >提交</button>
        <button type="reset" class="btn btn-default">重置</button>
      </div>
    </form>
  </div>
</div>