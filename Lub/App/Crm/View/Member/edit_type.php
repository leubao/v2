<form class="form-horizontal" action="{:U('Crm/Member/edit_type',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
    <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <td width="100px">类型名称:</td>
          <td><input type="text" name="title" size="30" value="{$data.title}" maxlength="20" class="required"/></td>
        </tr>
        <tr>
          <td width="100px">办理期:</td>
          <td><input type="text" size="11" id="starttime" name="starttime" data-toggle="datepicker" value="{$data.rule.datetime.starttime}">
              <label>至</label>
              <input type="text" size="11" id="endtime" name="endtime" data-toggle="datepicker"  value="{$data.rule.datetime.endtime}">
            </td>
        </tr>
        <tr>
          <td width="100px">有效期:</td>
          <td><input type="text" size="11" id="eff_starttime" name="eff_starttime" data-toggle="datepicker" value="{$data.rule.efftime.start}">
              <label>至</label>
              <input type="text" size="11" id="eff_endtime" name="eff_endtime" data-toggle="datepicker"  value="{$data.rule.efftime.end}">
            </td>
        </tr>
        <tr>
          <td width="100px">天/次数:</td>
          <td><input type="text" name="number" id="number" value="{$data.rule.number}" size="5" /><span class="fun_tips">按天、次卡选择</span></td>
        </tr>
        <tr>
          <td width="100px">可办理区域:</td>
          <td><input type="text" name="area" value="{$data.rule.area}"/></td>
        </tr>
        <tr>
            <td>打印模板:</td>
            <td>
              <select name="print_tpl" data-toggle="selectpicker">
                  <option value="">打印模板</option>
                  <volist name="printer" id='pri'>
                    <option value="{$pri.id}" <if condition="$data['print_tpl'] eq $pri['id']">selected</if>>{$pri.title}</option>
                  </volist>
                </select>
            </td>
        </tr>
        <tr>
            <td>状态:</td><td>
              <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
                <option value="">状态</option>
                <option value="1" <if condition="$data['status'] eq 1">selected</if>>启用</option>
                <option value="0" <if condition="$data['status'] eq 0">selected</if>>禁用</option>
            </select></td>
        </tr>
        </tbody>
    </table>
    <input type="hidden" name="id" value="{$data.id}">
  </div>
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