<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Manage/Product/edit',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	<div class="form-group">
    <label class="col-sm-2 control-label">产品名称:</label>
    <input type="text" name="name" class="form-control required" data-rule="required;" value="{$data.name}" size="40" placeholder="产品名称">
  </div>
  <?php if($data['type'] == '1'){ ?>
  <div class="form-group seat">
    <label class="col-sm-2 control-label">场所列表:</label>
    <select name="place_id" id="place" data-toggle="selectpicker">
    <option>请选择</option>
    <volist name="place" id="place">
      <option value="{$place.id}">{$place.name}</option>
    </volist>
    </select>
  </div>
  <div class="form-group seat">
    <label class="col-sm-2 control-label">座椅模板:</label>
    <select name="template_id" class="required" id="seat_list" >

    </select>
  </div>
  <?php }?>
  <div class="form-group">
    <label class="col-sm-2 control-label">产品描述:</label>
    <textarea name="content" cols="30" >{$data.content}</textarea>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">状态:</label>
    <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="">状态</option>
      <option value="1" <eq name="data.status" value='1'>selected</eq>>启用</option>
      <option value="0" <eq name="data.status" value='0'>selected</eq>>禁用</option>
    </select>
  </div>
</div>
<input type="hidden" name="id" value="{$data.id}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>
<script type="text/javascript">
$(document).ready(function(){
    // 选择不同的场所调用不同的座椅模板
  $("#place").change(function(){
    var place = $("#place").val();
    if (place != 0) { 
      $.ajax({
         type: "POST",
         url: "<?php echo U('Manage/place/template');?>",
         data: "placeid="+place,
         success: function(msg){
            $("#seat_list").empty();
            $("#seat_list").append(msg);
          }});
    } else {
    }
        
    });
})
</script>