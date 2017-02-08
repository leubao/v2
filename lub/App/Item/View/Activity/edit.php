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
              <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
                <option value="">活动类型</option>
                <option value="1">买赠</option>
              </select>
            </td>
        </tr>
        <tr>
          <td>活动详情:</td>
          <td colspan="3">
            买: <input type="text" name="num" value="" data-rule="required;digits" size="10">
            赠送: <input type="text" name="nums" value="" data-rule="required;digits" size="10">
          </td>
        </tr>
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