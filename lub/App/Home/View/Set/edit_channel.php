<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title" id="myModalLabel">编辑代理商</h4>
</div>
<form action="{:U('Home/Set/edit_channel');}" method="post">
  <div class="modal-body">
  <div class="panel panel-default cler_mag_20"> 
    <!-- Default panel contents -->
    <div class="panel-body form-horizontal">
      <div class="form-group">
        <div class="form-group">
          <label class="col-sm-2 control-label">代理商名称：</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="name" value="{$data.name}" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">地址：</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="address" value="{$data.address}" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">联系人</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="contacts" value="{$data.contacts}" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">联系电话</label>
          <div class="col-sm-8">
            <input type="phone" class="form-control" name="phone"  value="{$data.phone}" required>
          </div>
        </div>
        <input type="hidden" name="id" value="{$data.id}">
        <!--  
        <div class="form-group">
          <label class="col-sm-2 control-label">代理商级别</label>
          <div class="col-sm-4">
            <select class="form-control" name="level" required>
              <option value=" ">请选择</option>
              <volist name="level" id="vo"> <option value="{$vo.id}" 
            
                <if condition="$vo['id'] eq $data['level']">selected="selected"</if>
                >{$vo.name}
            
                </option>
              </volist>
            </select>
          </div>
        </div>-->
        <div class="form-group">
          <label class="col-sm-2 control-label">状态</label>
          <div class="col-sm-2">
            <select class="form-control" name="status" required>
              <option value="">状态</option>
              <option value="1" <eq name="vo['status']" value="1">selected="selected"</eq>>启用</option>
              <option value="0" <eq name="vo['status']" value="0">selected="selected"</eq>>停用</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-success" >提交</button>
    <button type="reset" class="btn btn-default">重置</button>
  </div>
</form>
