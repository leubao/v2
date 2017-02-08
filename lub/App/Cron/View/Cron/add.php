<form class="form-horizontal" action="{:U('Cron/Cron/add',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
<div class="form-group">
    <label class="col-sm-2 control-label">计划名称:</label>
    <input type="text" name="subject" class="form-control required" data-rule="required;" size="40" placeholder="计划名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">执行时间:</label>
    <select id="J_time_select" name="loop_type" data-toggle="selectpicker">
              <option value="month">每月</option>
              <option value="week">每周</option>
              <option value="day">每日</option>
              <option value="hour">每小时</option>
              <option value="now">每隔</option>
    </select>
    <span class="J_time_item" id="J_time_month"  style="">
    <select class="select_2 mr10" name="month_day" data-toggle="selectpicker">
      <option value="1">1日</option>
      <option value="2">2日</option>
      <option value="3">3日</option>
      <option value="4">4日</option>
      <option value="5">5日</option>
      <option value="6">6日</option>
      <option value="7">7日</option>
      <option value="8">8日</option>
      <option value="9">9日</option>
      <option value="10">10日</option>
      <option value="11">11日</option>
      <option value="12">12日</option>
      <option value="13">13日</option>
      <option value="14">14日</option>
      <option value="15">15日</option>
      <option value="16">16日</option>
      <option value="17">17日</option>
      <option value="18">18日</option>
      <option value="19">19日</option>
      <option value="20">20日</option>
      <option value="21">21日</option>
      <option value="22">22日</option>
      <option value="23">23日</option>
      <option value="24">24日</option>
      <option value="25">25日</option>
      <option value="26">26日</option>
      <option value="27">27日</option>
      <option value="28">28日</option>
      <option value="29">29日</option>
      <option value="30">30日</option>
      <option value="31">31日</option>
      <option value="99">最后一天</option>
    </select>
    <select class="select_2"  name="month_hour" data-toggle="selectpicker">
      <option value="0">0点</option>
      <option value="1">1点</option>
      <option value="2">2点</option>
      <option value="3">3点</option>
      <option value="4">4点</option>
      <option value="5">5点</option>
      <option value="6">6点</option>
      <option value="7">7点</option>
      <option value="8">8点</option>
      <option value="9">9点</option>
      <option value="10">10点</option>
      <option value="11">11点</option>
      <option value="12">12点</option>
      <option value="13">13点</option>
      <option value="14">14点</option>
      <option value="15">15点</option>
      <option value="16">16点</option>
      <option value="17">17点</option>
      <option value="18">18点</option>
      <option value="19">19点</option>
      <option value="20">20点</option>
      <option value="21">21点</option>
      <option value="22">22点</option>
      <option value="23">23点</option>
    </select>
    </span> <span class="J_time_item" id="J_time_week" style="display:none;">
    <select class="select_2 mr10" name="week_day" data-toggle="selectpicker">
      <option value="1">周一</option>
      <option value="2">周二</option>
      <option value="3">周三</option>
      <option value="4">周四</option>
      <option value="5">周五</option>
      <option value="6">周六</option>
      <option value="0">周日</option>
    </select>
    <select class="select_2" name="week_hour" data-toggle="selectpicker">
      <option value="0">0点</option>
      <option value="1">1点</option>
      <option value="2">2点</option>
      <option value="3">3点</option>
      <option value="4">4点</option>
      <option value="5">5点</option>
      <option value="6">6点</option>
      <option value="7">7点</option>
      <option value="8">8点</option>
      <option value="9">9点</option>
      <option value="10">10点</option>
      <option value="11">11点</option>
      <option value="12">12点</option>
      <option value="13">13点</option>
      <option value="14">14点</option>
      <option value="15">15点</option>
      <option value="16">16点</option>
      <option value="17">17点</option>
      <option value="18">18点</option>
      <option value="19">19点</option>
      <option value="20">20点</option>
      <option value="21">21点</option>
      <option value="22">22点</option>
      <option value="23">23点</option>
    </select>
    </span> <span class="J_time_item" id="J_time_day" style="display:none;">
    <select class="select_2 mr10"  name="day_hour" data-toggle="selectpicker">
      <option value="0">0点</option>
      <option value="1">1点</option>
      <option value="2">2点</option>
      <option value="3">3点</option>
      <option value="4">4点</option>
      <option value="5">5点</option>
      <option value="6">6点</option>
      <option value="7">7点</option>
      <option value="8">8点</option>
      <option value="9">9点</option>
      <option value="10">10点</option>
      <option value="11">11点</option>
      <option value="12">12点</option>
      <option value="13">13点</option>
      <option value="14">14点</option>
      <option value="15">15点</option>
      <option value="16">16点</option>
      <option value="17">17点</option>
      <option value="18">18点</option>
      <option value="19">19点</option>
      <option value="20">20点</option>
      <option value="21">21点</option>
      <option value="22">22点</option>
      <option value="23">23点</option>
    </select>
    </span> <span class="J_time_item" id="J_time_hour" style="display:none;">
    <select class="select_2" name="hour_minute" data-toggle="selectpicker">
      <option value="0">00分</option>
      <option value="10">10分</option>
      <option value="20">20分</option>
      <option value="30">30分</option>
      <option value="40">40分</option>
      <option value="50">50分</option>
    </select>
    </span> <span class="J_time_item" id="J_time_now" style="display:none;">
    <input type="text" class="form-control" name="now_time" size='5' value="0">
    <select class="select_2" name="now_type" data-toggle="selectpicker">
      <option value="minute">分钟</option>
      <option value="hour">小时</option>
      <option value="day">天</option>
    </select>
    </span>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">执行文件:</label>
    <select name="cron_file" class="required" data-toggle="selectpicker" data-rule="required">
      <volist name="fileList" id="vo">
              <option value="{$vo|basename=###,'.php'}">{$vo}</option>
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