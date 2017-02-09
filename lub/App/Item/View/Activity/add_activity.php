<form class="form-horizontal" action="{:U('Item/Activity/add_activity',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
    <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <td>活动名称:</td><td colspan="3">{$data.title}</td>
        </tr>
        <tr>
            <td>活动类型:</td>
            <td colspan="3">
              {$data.type|activity_type}
            </td>
        </tr>
        <!--买赠-->
        <if condition="$data.type eq '1'">
        <tr>
          <td>付款票型:</td>
          <td colspan="3">
           <input type="hidden" name="ticket.id" value="{$ticket_id}">
           <input type="text" name="ticket.name" readonly value="{$ticket_name}" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/get_price',array('ifadd'=>1));}" data-group="ticket" data-width="600" data-height="445" data-title="票型名称" placeholder="票型名称">
    
          </td>
        </tr>
        <?php for ($i=1; $i < $data['param']['nums']+1; $i++) { ?>
          <tr>
            <td>赠送票型-{$i}:</td>
            <td colspan="3">
              <input type="hidden" name="ticket_{$i}.id" value="{$ticket_id}">
              <input type="text" name="ticket_{$i}.name" readonly value="{$ticket_name}" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/get_price',array('ifadd'=>1));}" data-group="ticket_{$i}" data-width="600" data-height="445" data-title="票型名称" placeholder="票型名称">
            </td>
          </tr>
        <?php } ?> 
        </if>
        
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