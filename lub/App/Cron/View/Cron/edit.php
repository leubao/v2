<form class="form-horizontal" action="{:U('Cron/Cron/edit',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
<div class="form-group">
    <label class="col-sm-2 control-label">计划名称:</label>
    <input type="text" name="subject" class="form-control required" data-rule="required;" value="{$subject}" size="40" placeholder="计划名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">执行时间:</label>
    <select id="J_time_select" name="loop_type" class="mr10">
          <volist name="loopType" id="vo">
              <option value="{$key}"  <if condition=" $key eq $loop_type ">selected</if>>{$vo}</option>
           </volist>
            </select>
            <span class="J_time_item" id="J_time_month"  <if condition=" $loop_type neq 'month' ">style="display:none;"</if>>
            <select class="select_2 mr10" name="month_day">
              <for start="1" end="31">
              <option value="{$i}"  <if condition=" $i eq $day ">selected</if>>{$i}日</option>
              </for>
              <option value="99" <if condition=" 99 eq $day ">selected</if>>最后一天</option>
            </select>
            <select class="select_2"  name="month_hour">
              <for start="0" end="23">
              <option value="{$i}"  <if condition=" $i eq $hour ">selected</if>>{$i}点</option>
              </for>
            </select>
            </span> <span class="J_time_item" id="J_time_week"   <if condition=" $loop_type neq 'week' ">style="display:none;"</if> >
            <select class="select_2 mr10" name="week_day">
              <option value="1" <if condition=" 1 eq $day ">selected</if>>周一</option>
              <option value="2" <if condition=" 2 eq $day ">selected</if>>周二</option>
              <option value="3" <if condition=" 3 eq $day ">selected</if>>周三</option>
              <option value="4" <if condition=" 4 eq $day ">selected</if>>周四</option>
              <option value="5" <if condition=" 5 eq $day ">selected</if>>周五</option>
              <option value="6" <if condition=" 6 eq $day ">selected</if>>周六</option>
              <option value="0" <if condition=" 0 eq $day ">selected</if>>周日</option>
            </select>
            <select class="select_2" name="week_hour">
              <for start="0" end="23">
              <option value="{$i}"  <if condition=" $i eq $hour ">selected</if>>{$i}点</option>
              </for>
            </select>
            </span> <span class="J_time_item" id="J_time_day"    <if condition=" $loop_type neq 'day' ">style="display:none;"</if> >
            <select class="select_2 mr10"  name="day_hour">
              <for start="0" end="23">
              <option value="{$i}"  <if condition=" $i eq $hour ">selected</if>>{$i}点</option>
              </for>
            </select>
            </span> <span class="J_time_item" id="J_time_hour"   <if condition=" $loop_type neq 'hour' ">style="display:none;"</if>>
            <select class="select_2" name="hour_minute">
              <option value="0" <if condition=" 0 eq $minute ">selected</if>>00分</option>
              <option value="10"  <if condition=" 10 eq $minute ">selected</if>>10分</option>
              <option value="20"  <if condition=" 20 eq $minute ">selected</if>>20分</option>
              <option value="30"  <if condition=" 30 eq $minute ">selected</if>>30分</option>
              <option value="40"  <if condition=" 40 eq $minute ">selected</if>>40分</option>
              <option value="50"  <if condition=" 50 eq $minute ">selected</if>>50分</option>
            </select>
            </span> <span class="J_time_item" id="J_time_now"   <if condition=" $loop_type neq 'now' ">style="display:none;"</if> >
            <?php
      if ($day) $time =  $day;
      if ($hour) $time =  $hour;
      if ($minute) $time =  $minute;
      ?>
            <input type="text" class="input length_2 mr5" name="now_time" value="{$time}">
            <select class="select_2" name="now_type">
              <option value="minute" <if condition=" $minute ">selected</if> >分钟</option>
              <option value="hour"  <if condition=" $hour ">selected</if>>小时</option>
              <option value="day" <if condition=" $day ">selected</if>>天</option>
            </select>
            </span>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">执行文件:</label>
    <select name="cron_file" class="required" data-toggle="selectpicker" data-rule="required">
      <volist name="fileList" id="vo">
              <option value="{$vo|basename=###,'.php'}" <if condition=" $cron_file eq  basename($vo,'.php')">selected</if>>{$vo}</option>
              </volist>
  </select>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">状态:</label>
    <select name="isopen" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="1" selected>启用</option>
      <option value="0">禁用</option>
    </select>
  </div>
</div>
<input type="hidden" name="cron_id" value="{$cron_id}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</form>
<script>
$(function(){
  $('#J_time_select').on('change', function(){
    $('#J_time_'+ $(this).val()).show().siblings('.J_time_item').hide();
  });
  $("#J_type_select").on('change', function(){
    if($(this).val() == "0"){
      $('.J_type_item').hide();
    }else{
      $('#type'+ $(this).val()).show().siblings('.J_type_item').hide();
    }
  });
});
</script>