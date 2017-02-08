<form class="form-horizontal" action="{:U('Item/Activity/add',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
    <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <td>活动名称:</td><td colspan="3"><input type="text" name="title" value="" data-rule="required" size="35"></td>
        </tr>
        <tr>
          <td>开始时间:</td><td><input type="text" data-toggle="datepicker" name="starttime" class="required" data-rule="required"></td>
          <td>结束时间:</td><td><input type="text" data-toggle="datepicker" name="endtime" class="required" data-rule="required"></td>
        </tr>
        <tr>
          <td>活动场景:</td>
          <td colspan="3">
            <input type="checkbox" name="scene[]" value="1"> 窗口
            <input type="checkbox" name="scene[]" value="2"> 渠道版
            <input type="checkbox" name="scene[]" value="3"> 网站
            <input type="checkbox" name="scene[]" value="4"> 微信
            <input type="checkbox" name="scene[]" value="5"> API
            <input type="checkbox" name="scene[]" value="6"> 自助机
          </td>
        </tr>
        <tr>
            <td>活动类型:</td>
            <td colspan="3">
              <select name="type" class="required" data-toggle="selectpicker" data-rule="required">
                <option value="">活动类型</option>
                <option value="1">买赠</option>
                <option value="2">首单免</option>
              </select>
            </td>
        </tr>
        
        <volist name="seat" id="vo"> 
        <tr>
            <td>活动区域：</td>
            <td><input type="checkbox" name="area[{$vo.id}]" value="{$vo.id}"> {$vo.id|areaName} 
            买: <input type="text" name="num[{$vo.id}]" value="" data-rule="digits" size="5"> 
              <input type="hidden" name="ticket_num_{$vo.id}.id" value="{$ticket_id}">
              <input type="text" name="ticket_num_{$vo.id}.name" readonly value="{$ticket_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_get_price',array('ifadd'=>1));}" data-group="ticket_num_{$vo.id}" data-width="600" data-height="445" data-title="票型名称" placeholder="票型名称">
            赠送: <input type="text" name="nums[{$vo.id}]" value="" data-rule="digits" size="5">
              <input type="hidden" name="ticket_nums_{$vo.id}.id" value="{$ticket_id}">
              <input type="text" name="ticket_nums_{$vo.id}.name" readonly value="{$ticket_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_get_price',array('ifadd'=>1));}" data-group="ticket_nums_{$vo.id}" data-width="600" data-height="445" data-title="票型名称" placeholder="票型名称"></td>
            <td>数量: <input type="text" name="quota[{$vo.id}]" value="" data-rule="digits" size="5">
            <input name="seat[{$vo.id}]" id="seat_{$vo.id}" type="hidden" value="">
            </td>
            <td><a href="{:U('Item/Activity/row_seat',array('area'=>$vo['id']));}" data-toggle="dialog" data-mask="true" data-max="true" data-id="activity_seat">指定区域</a></td>
        </tr>
        </volist>
        <tr>
            <td>排序:</td><td><input type="text" name="sort" value="0" size="15"></td>
            <td>状态:</td><td>
              <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
                <option value="">状态</option>
                <option value="1">启用</option>
                <option value="0">禁用</option>
            </select></td>
        </tr>
        <tr>
          <td>备注:</td><td colspan="3"><input type="text" name="remark" placeholder="如:备注" value="" size="50"></td>
        </tr>
      </tbody>
    </table>
  </div>
  <input name="product_id" value="{$product_id}" type="hidden">
  <div class="bjui-pageFooter">
    <ul>
      <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
      <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
  </div>
</form>