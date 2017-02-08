<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title" id="myModalLabel">余额提现</h4>
</div>
<div class="modal-body">
  <div class="panel panel-default cler_mag_20"> 
    <!-- Default panel contents -->
    <form action="{:U('Home/Pay/cash');}" method="post">
      <div class="panel-body form-horizontal">
      <div class="form-group"></div>
        <div class="form-group">
          <label class="col-sm-2 control-label">收款人</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="nickname" placeholder="如：姓名" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">提现金额</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="money" placeholder="整数提现" required>
          </div>
        </div>
         <div class="form-group">
          <label class="col-sm-2 control-label">收款方式</label>
          <div class="col-sm-8">
           <label class="radio-inline">
        <input type="radio" name="is_pay" value="1" checked>现金
      </label>
      <label class="radio-inline">
        <input type="radio" name="is_pay" value="2">银行卡
      </label>
      <label class="radio-inline">
        <input type="radio" name="is_pay" value="3">支付宝
      </label>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">联系电话</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="phone" placeholder="如：18631450000" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">银行卡号</label>
          <div class="col-sm-8">
            <input type="email" class="form-control" name="email" value="{$data.email}" placeholder="如：xx@chengde360.com" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">支付宝账号</label>
          <div class="col-sm-8">
            <input type="email" class="form-control" name="email" value="{$data.email}" placeholder="如：xx@chengde360.com" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">希望到账日期</label>
          <div class="col-sm-8">
            <input type="password" class="form-control" name="password">
          </div>
        </div>
        
      </div>
      <input type="hidden" name="channel_id" value="{$crm.id}">
      <div class="panel-footer">
        <button type="submit" class="btn btn-success fright" >提交</button>
        <button type="reset" class="btn btn-default">重置</button>
      </div>
    </form>
  </div>
</div>