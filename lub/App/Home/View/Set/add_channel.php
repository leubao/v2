<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title" id="myModalLabel">新增代理商</h4>
</div>
<form action="{:U('Home/Set/add_channel');}" method="post">
  <div class="modal-body">
  <div class="panel panel-default cler_mag_20"> 
    <!-- Default panel contents -->
    
    <div class="panel-body form-horizontal">
      <div class="form-group">
        <div class="form-group">
          <label class="col-sm-2 control-label">代理商名称：</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="name" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">地址：</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="address" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">联系人</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="contacts" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">联系电话</label>
          <div class="col-sm-8">
            <input type="phone" class="form-control" name="phone" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">产品列表</label>
          <div class="col-sm-4">
            <select class="form-control" name="product_id" required>
              <option value=" ">请选择</option>
              <volist name="product" id="vo">
                <option value="{$vo.id}">{$vo.name}</option>
              </volist>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">状态</label>
          <div class="col-sm-2">
            <select class="form-control" name="status" required>
              <option value="">状态</option>
              <option value="1">启用</option>
              <option value="0">停用</option>
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