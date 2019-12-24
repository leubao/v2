<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Manage/Product/add',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	<div class="form-group">
    <label class="col-sm-2 control-label">产品名称:</label>
    <input type="text" name="name" class="form-control required" data-rule="required;" size="40" placeholder="产品名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">产品类型:</label>
    <input name="type" id="theatre" value="1" type="radio" <if condition=" $data['type'] == '1' ">checked</if>  onClick="$('.seat').css('display','');"> 剧院产品
    <input name="type" id="scenic" value="2" type="radio" <if condition=" $data['type'] == '2' ">checked</if> onClick="$('.seat').css('display','none');"> 景区产品 
    <input name="type" id="scenic" value="3" type="radio" <if condition=" $data['type'] == '3' ">checked</if> onClick="$('.seat').css('display','none');"> 漂流产品<span class="gray"> 注意操作顺序类型>场所</span>
  </div>
  <tr>
              
  <div class="form-group">
    <label class="col-sm-2 control-label">所属商户:</label>
    <select name="item_id" class="required" data-toggle="selectpicker" data-rule="required">
    <volist name="item" id="vo">
      <option value="{$vo.id}">{$vo.name}</option>
    </volist>
    </select>
  </div>
  <div class="form-group seat" style="display: none">
    <label class="col-sm-2 control-label">场所列表:</label>
    <select name="place_id" id="place" data-toggle="selectpicker">
    <option>请选择</option>
    <volist name="place" id="place">
      <option value="{$place.id}">{$place.name}</option>
    </volist>
    </select>
  </div>
  <div class="form-group seat" style="display: none">
    <label class="col-sm-2 control-label">座椅模板:</label>
    <select name="template_id" class="required" id="seat_list" >

    </select>
  </div>
  <!--
  <div class="form-group">
    <label class="col-sm-2 control-label">所属商户:</label>
    <input type="hidden" name="channel.id" value="">
    <input type="text" name="channel.name" disabled value="" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_channel');}" data-group="channel" data-width="600" data-height="445" data-title="渠道商" placeholder="渠道商">
  </div>
  -->
  <div class="form-group">
    <label class="col-sm-2 control-label">产品描述:</label>
    <textarea name="content" cols="30" ></textarea>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">状态:</label>
    <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
	    <option value="">状态</option>
	    <option value="1">启用</option>
	    <option value="0">禁用</option>
	  </select>
  </div>
</div>
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
    var ptype = $('input[name="type"]:checked').val();
    if (place != 0 && ptype == 1) { 
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