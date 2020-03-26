<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title" id="myModalLabel">授信额退款 --- {$cid|crmName}</h4>
</div>
<form action="{:U('Home/Pay/arefund');}" method="post">
  <div class="modal-body">
    <div class="panel panel-default cler_mag_20"> 
      <!-- Default panel contents -->
      
      <div class="panel-body form-horizontal">
        <div class="form-group">
            <label class="col-sm-2 control-label">退款客户:</label>
            <div class="col-sm-8">
            	<input type="text" class="form-control" disabled="true" value="{$crm.name}">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">当前余额:</label>
            <div class="col-sm-8">
            	<input type="text" class="form-control" disabled="true" value="{$crm.cash}">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">退款金额:</label>
            <div class="col-sm-4">
              <div class="input-group">
                <span class="input-group-addon">￥</span>
                <input type="text" class="form-control" name="money" required>
                <span class="input-group-addon">.00</span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">备注:</label>
            <div class="col-sm-8">
              <textarea class="form-control" name="remark" rows="3"></textarea>
            </div>
          </div>
      </div>
    </div>
    <input type="hidden" name="crmid" value="{$cid}">
    <input type="hidden" name="channel" value="{$channel}">
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-success" >提交</button>
    <button type="reset" class="btn btn-default">重置</button>
  </div>
</form>