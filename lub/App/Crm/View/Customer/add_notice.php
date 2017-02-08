<script>KindEditor.create('textarea[name="content"]',{
        minWidth : '540px',
        minHeight : '282px',
        resizeType : 1,
        uploadJson : '{:U('Crm/Customer/upload');}',
        allowFileManager : true,
        allowImageUpload : true, 
        items : [
          'source', '|', 'undo', 'redo', '|', 'preview', 'cut', 'copy', 'paste',
          'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
          'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
          'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/',
          'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
          'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image', 'table', 'hr', 'emoticons'
        ]
});

</script>
<form class="form-horizontal" action="{:U('Crm/Customer/add_notice',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <div class="form-group">
    <label class="col-sm-1 control-label">标题:</label>
    <input type="text" name="title" class="form-control required" data-rule="required;" size="40" placeholder="通知标题">
  </div>
  <div class="form-group">
    <label class="col-sm-1 control-label">状态:</label>
    <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="">状态</option>
      <option value="1" selected>启用</option>
      <option value="0">禁用</option>
  </select>
  </div>
  <div class="form-group">
    <label class="col-sm-1 control-label">内容:</label>
    <textarea name="content" style="width: 700px; height: 400px;"  cols="80" rows="6"></textarea>
  </div>

</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>