<form class="form-horizontal" action="{:U('Item/Product/plan_ticket',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
  <div class="panel panel-default">
      <div class="panel-body">
        当前销售计划:{$pid|planShow}
      </div>
    </div>
    <volist name="group" id="group">
      <fieldset>
        <legend>{$group.name}</legend>
        <volist name="group['TicketType']" id="type" mod='3'>
          <input type="checkbox" data-toggle="icheck" name="ticket[]" value="{$type.id}" data-label="{$type.name}（价格：{$type.discount}）" <?php if(in_array($type['id'],$data['ticket'])){ echo "checked"; }?>>
          <eq name='mod' value="3"><br /></eq>
        </volist>
      </fieldset>
    </volist>
  </div>
  <input name="plan_id" value="{$pid}" type="hidden">
  <div class="bjui-pageFooter">
    <ul>
      <li>
        <button type="button" class="btn-close" data-icon="close">取消</button>
      </li>
      <li>
        <button type="submit" class="btn-default" data-icon="save">保存</button>
      </li>
    </ul>
  </div>
</form>