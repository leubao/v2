<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Work/subtract',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
  <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <td width="100px">说明</td>
          <td style="color: red;"><strong>单一订单最多核减一次</strong></td>
        </tr>
        <tr>
          <td width="100px">订单号:</td>
          <td><strong>{$sn}</strong></td>
        </tr>
        <tr>
          <td width="100px">可核减数:</td>
          <td><strong>{$num}</strong></td>
        </tr>
        <tr>
          <td width="100px">核减区域:</td>
          <td><volist name="area" id="vo">
              <input type="checkbox" name="area[]" id="a_{$vo['area']}" value="{$vo['area']}" onclick="subtract({$vo['area']});">
              {$vo['areaname']}({$vo['num']})
            <input type="text" name="seat_num[]" id="num_{$vo['area']}" size="2" disabled>
          </volist>
          </td>
        </tr>
        </tbody>
    </table>
  </div>
  <input name="sn" value="{$sn}" type="hidden">
  <input name="num" value="{$num}" type="hidden">
  <div class="bjui-pageFooter">
    <ul>
      <li>
        <button type="button" class="btn-close" data-icon="close">取消</button>
      </li>
      <li>
        <button type="submit" class="btn-warning" data-icon="save">立即核减</button>
      </li>
    </ul>
  </div>
</form>



<script>
function subtract(id){
  if($('#a_'+id).is(':checked')){
    $('#num_'+id).removeAttr('disabled');
  }else{
    $('#num_'+id).attr("disabled","disabled");
  }   
}
</script>