<form class="form-horizontal" action="{:U('Item/Activity/add_water',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
    <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <td>推广员:</td>
          <td colspan="3">
            {$ginfo.id|UserName}
            <input type="hidden" name="member_id" value="{$ginfo.id}">
          </td>
        </tr>
        <tr>
          <td>类型:</td>
          <td colspan="3">
            <input type="radio" name="type" value="1" checked> 领取
            <input type="radio" name="type" value="2"> 返还
          </td>
        </tr>
        <tr>
            <td>数量:</td><td><input type="text" name="number" value="0" size="15" data-rule="digits"></td>
            <td>状态:</td><td>
              <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
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
  <div class="bjui-pageFooter">
    <ul>
      <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
      <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
  </div>
</form>