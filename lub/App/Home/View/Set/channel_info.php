<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title" id="myModalLabel">代理商详情</h4>
</div>
  <div class="modal-body">
  <div class="panel panel-default cler_mag_20"> 
    <!-- Default panel contents -->
    <div class="panel-body form-horizontal">
      <div class="form-group">
        <div class="form-group">
          <label class="col-sm-2 control-label">代理商名称：</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="name" value="{$data.name}" disabled>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">地址：</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="address" value="{$data.address}" disabled>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">联系人</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="contacts" value="{$data.contacts}" disabled>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">联系电话</label>
          <div class="col-sm-8">
            <input type="phone" class="form-control" name="phone"  value="{$data.phone}" disabled>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">销售配额</label>
          <div class="col-sm-8">
            <input type="phone" class="form-control" name="quota" value="{$data.quota}" disabled>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">状态</label>
          <div class="col-sm-2">
            <if condition="$data['status'] eq 1"><span class="label label-success">启用</span>
                    <else />
                    <span class="label label-danger">禁用</span></if>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>