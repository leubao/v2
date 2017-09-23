<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title" id="myModalLabel">更新代理商配额</h4>
</div>
<form action="{:U('Home/Set/public_quota_channel');}" method="post">
  <div class="modal-body">
  <div class="panel panel-default cler_mag_20"> 
    <!-- Default panel contents -->
    <div class="panel-body form-horizontal">
      <div class="form-group">
        <div class="form-group">
          <label class="col-sm-2 control-label">代理商名称：</label>
          <div class="col-sm-8">
            <strong>{$data.id|crmName}</strong>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">产品列表：</label>
          <div class="col-sm-8">
            <volist name="quota" id="vo">
            {$vo.product_id|productName} 
            <input type="text" name="quota[{$vo.product_id}]" class="form-control" size="5" value="{$vo.quota}" placeholder="0"><br>
            </volist>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  <input type="hidden" name="crm_id" value="{$data.id}"/>
  <div class="modal-footer">
    <button type="submit" class="btn btn-success" >提交</button>
    <button type="reset" class="btn btn-default">重置</button>
  </div>
</form>