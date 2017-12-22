<form class="form-horizontal" action="{:U('Item/Product/typeadd',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
    <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <td>所属分组:</td><td>{$gid|groupName}
          <input name="group_id" type="hidden" value="{$gid}"/></td>
          <td>票型名称:</td><td><input type="text" name="name" value="" data-rule="required" size="25"></td>
        </tr>
        <tr>
          <td>票型价格:</td><td>
          <input type="hidden" name="single.id" value="" data-rule="required">
          <input type="text" name="single.price" value="" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/public_ticket_single');}" data-group="single" data-width="600" data-height="445" data-title="票型价格" placeholder="票型价格"></td>
          <td>补贴金额:</td><td><input type="text" name="rebate" value="" data-rule="required" size="15"></td>
        </tr>
        <tr>
          <td>结算价格:</td><td>
            <input type="text" name="discount" value="" data-rule="required" size="15"></td>
          <td>票型类型:</td><td>
            <select name="type" class="required" data-toggle="selectpicker" data-rule="required">
              <option value="">请选择</option>
              <option value="1">散客票</option>
              <option value="2">团队票</option>
              <option value="3">散客、团队票</option>
              <option value="4">政企票</option>
            </select></td>
        </tr>
        <tr>
          <td>票型特权:</td>
          <td>
            <input type="checkbox" name="param[quota]" value="1"> 不消耗配额
          </td>
          <td>票面标记:</td>
          <td>
            <input type="checkbox" name="param[ticket_print]" value="1">
            <input type="text" name="param[ticket_print_custom]" value="" size="10"><span class="remark">座位号选择自定义时打印该内容</span>
          </td>
        </tr>
        <tr>
          <td>联票支持:</td>
          <td>
            <input type="radio" name="param[present]" value="1">  是
            <input type="radio" name="param[present]" value="0">  否
            <span class="remark">用于子票设置，主票不用设置</span>
          </td>
          <td>活动标记</td>
          <td>
            <input type="radio" name="param[activity]" value="1">  是
            <input type="radio" name="param[activity]" value="0">  否
            <span class="remark">仅用于活动销售</span>
          </td>
        </tr>
        <tr>
          <td>销售场景:</td>
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
          <td>排序:</td><td><input type="text" name="sort" value="0" size="15"></td>
            <td>状态:</td><td>
              <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
                <option value="">状态</option>
                <option value="1">启用</option>
                <option value="0">禁用</option>
            </select></td>
        </tr>
        <tr>
          <td>备注:</td><td colspan="3"><input type="text" name="remark" placeholder="如：一大人|一儿童" value="" size="50"></td>
        </tr>
      </tbody>
    </table>
    <if condition="$ptype eq 1">
     <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne">
          <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne"> 座椅区域 </a> </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
          <div class="panel-body">
            <volist name="area" id="vo">
              <input type="radio"  data-toggle="icheck" name="area" value="{$vo.id}" data-label="{$vo.name}（座椅数{$vo.num}个）">
            </volist>
          </div>
        </div>
      </div>
    </if>
  </div>
  <input name="product_id" value="{$product_id}" type="hidden">
  <div class="bjui-pageFooter">
    <ul>
      <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
      <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
  </div>
</form>