<form class="form-horizontal" action="{:U('Item/Product/planauth',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
  <if condition="$data['product_type'] eq '1' ">
    <table class="table table-condensed table-hover">
      <tbody>
        <tr>
          <td><label for="j_dialog_operation" class="control-label x90">销售日期:</label>{$data.plantime|date="Y-m-d",###}</td>
          <td><label for="j_dialog_code" class="control-label x85">场次：</label>第{$data.games}场</td>
        </tr>
        <tr>
          <td><label for="j_dialog_operation" class="control-label x90">开始时间:</label>{$data.starttime|date="H:i",###}</td>
          <td><label for="j_dialog_operation" class="control-label x90">结束时间:</label>{$data.endtime|date="H:i",###}</td>
        </tr>
        <tr>
          <td><label for="j_dialog_name" class="control-label x90">配额：</label>{$data.quota}</td>
          
          <td></td>
        </tr>
      </tbody>
    </table>
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne">
          <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne"> 座椅区域 </a> </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
          <div class="panel-body">
            <volist name="area" id="vo">
              <input type="checkbox" checked disabled data-toggle="icheck" name="seat[]" value="{$vo.id}" data-label="{$vo.name}（座椅数{$vo.num}个）">
            </volist>
          </div>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingTwo">
          <h4 class="panel-title"> <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"> 价格政策 </a> </h4>
        </div>
        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
          <div class="panel-body">
            <volist name="group" id="group">
              <fieldset>
                <legend>{$group.name}</legend>
                <volist name="group['tw']" id="type">
                  <input type="checkbox" checked disabled data-toggle="icheck" name="ticket[]" value="{$type.id}" data-label="{$type.name}（价格：{$type.discount}）">
                </volist>
              </fieldset>
            </volist>
          </div>
        </div>
      </div>
    </div>
  <else />
    <table class="table table-condensed table-hover">
      <tbody>
        <tr>
          <td><label for="j_dialog_operation" class="control-label x90">销售日期:</label>{$data.plantime|date="Y-m-d",###}</td>
          <td><label for="j_dialog_code" class="control-label x85">场次：</label>第{$data.games}场</td>
          <td></td>
        </tr>
        <tr>
          <td><label for="j_dialog_name" class="control-label x90">配额：</label>{$data.quota}</td>
          <td></td>
        </tr>
      </tbody>
    </table>
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingTwo">
          <h4 class="panel-title"> <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo"> 价格政策 </a> </h4>
        </div>
        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
          <div class="panel-body">
            <volist name="group" id="group">
              <fieldset>
                <legend>{$group.name}</legend>
                <volist name="group['TicketType']" id="type" mod='3'>
                  <input type="checkbox" checked="checked" data-toggle="icheck" name="ticket[]" value="{$type.id}" data-label="{$type.name}（价格：{$type.discount}）">
                  <eq name='mod' value="3"><br /></eq>
                </volist>
              </fieldset>
            </volist>
          </div>
        </div>
      </div>
    </div>
  </if>
  </div>
  <input name="plan_id" value="{$data.id}" type="hidden"> 
  <div class="bjui-pageFooter">
    <ul>
      <li>
        <button type="button" class="btn-close" data-icon="close">取消</button>
      </li>
      <li>
        <button type="submit" class="btn-default" data-icon="save">授权</button>
      </li>
    </ul>
  </div>
</form>