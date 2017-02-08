<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title" id="myModalLabel">授信额充值 --- 中国农业银行(ABC)</h4>
</div>
<form action="{:U('Home/Pay/to_up_cash');}" method="post">
  <div class="modal-body">
  <div class="panel panel-default cler_mag_20"> 
    <!-- Default panel contents -->
    
    <div class="panel-body form-horizontal">
      <div class="form-group">
          <label class="col-sm-2 control-label">网银:</label>
          <div class="col-sm-8">
            <div class="radio">
              <label>
                <input type="radio" name="pay_type" value="1" checked>
                <img src="{$config_siteurl}static/images/abcicon2.jpg" width="200px" height="40px" class="img-rounded">
              </label>
            </div>
          </div>
      </div>




      <div class="form-group">
        <div class="form-group">
          <label class="col-sm-2 control-label">充值金额:</label>
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
  </div>
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-success" >提交</button>
    <button type="reset" class="btn btn-default">重置</button>
  </div>
</form>