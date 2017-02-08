<form class="form-horizontal" action="{:U('Manage/Item/add',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
  <if condition="$pinfo['type'] eq '1' ">
    <table class="table table-condensed table-hover">
      <tbody>
        <tr>
          <td><label for="j_dialog_operation" class="control-label x90">销售日期:</label>
            <input type="text" data-toggle="datepicker" name="plantime" value="{$data['plantime']|date='Y-m-d',###}"></td>
          <td><label for="j_dialog_code" class="control-label x85">场次：</label>
            <select name="games" id="games" data-toggle="selectpicker">
              <option value="1" <eq name="data.games" value='1'>selected</eq>>第一场</option>
              <option value="2" <eq name="data.games" value='2'>selected</eq>>第二场</option>
              <option value="3" <eq name="data.games" value='3'>selected</eq>>第三场</option>
              <option value="4" <eq name="data.games" value='4'>selected</eq>>第四场</option>
            </select></td>
        </tr>
        <tr>
          <td><label for="j_dialog_operation" class="control-label x90">开始时间:</label>
            <input type="text" data-toggle="datepicker" data-options="hh:ii"  name="starttime" value="{$data.starttime}"></td>
          <td><label for="j_dialog_operation" class="control-label x90">结束时间:</label>
            <input type="text" data-toggle="datepicker" data-options="hh:ss" name="endtime" value="{$data.endtime}"></td>
        </tr>
        <tr>
          <td><label for="j_dialog_name" class="control-label x90">配额：</label>
            <input type="text" name="quota" id="quota" value="{$data.quota}" data-rule="required" size="20"></td>
          <td><label for="j_dialog_name" class="control-label x90">状态：</label>
            <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
              <option value="">状态</option>
              <option value="1" <eq name="data.status" value='1'>selected</eq>>启用</option>
              <option value="0" <eq name="data.status" value='0'>selected</eq>>禁用</option>
            </select></td>
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
            <volist name="seat" id="vo">
              <input type="checkbox" <if condition="$vo['id'] eq $data['template_id']">checked</if> data-toggle="icheck" name="seat[]" value="{$vo.id}" data-label="{$vo.name}（座椅数{$vo.num}个）">
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
                <volist name="group['TicketType']" id="type">
                  <input type="checkbox" <if condition="$data['template_id'] eq $type['id']">checked</if> data-toggle="icheck" name="ticket[]" value="{$type.id}" data-label="{$type.name}（价格：{$type.discount}）">
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
          <td><label for="j_dialog_operation" class="control-label x90">销售日期:</label>
            <input type="text" data-toggle="datepicker" name="plantime" value="{$data['plantime']|date='Y-m-d',###}"></td>
          <td><label for="j_dialog_code" class="control-label x85">场次：</label>
            <label for="j_dialog_name" class="control-label x90">状态：</label>
            <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
              <option value="">状态</option>
              <option value="1" <eq name="data.status" value='1'>selected</eq>>启用</option>
              <option value="0" <eq name="data.status" value='0'>selected</eq>>禁用</option>
            </select></td>
        </tr>
        <tr>
          <td><label for="j_dialog_name" class="control-label x90">配额：</label>
            <input type="text" name="quota" id="quota" value="0" data-rule="required" size="20" value="{$data.quota}"></td>
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
                  <input type="checkbox" <if condition="$data['template_id'] eq $type['id']">checked</if> data-toggle="icheck" name="ticket[]" value="{$type.id}" data-label="{$type.name}（价格：{$type.discount}）">
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
  <input type="hidden" name="id" value="{$data.id}">
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