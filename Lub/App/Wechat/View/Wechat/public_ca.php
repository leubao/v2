<div class="bjui-pageContent">
	<div class="form-group">
    <label class="col-sm-2 control-label">apiclient_cert.pem:</label>
    <form action="{:U('Wechat/Wechat/public_upload');}" method="post" enctype="multipart/form-data">
      <input type="file" name="file" id="file" /> 
      <input type="submit" name="submit" value="Submit" />
      </form>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">微信号:</label>
    <input type="text" name="wxid" class="form-control required" data-rule="required;" size="30" value="{$data.wxid}" placeholder="微信号">
  </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
    </ul>
</div>