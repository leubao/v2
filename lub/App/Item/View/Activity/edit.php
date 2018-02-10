<form class="form-horizontal" action="{:U('Item/Activity/add',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
    <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <td>活动名称:</td><td colspan="3"><input type="text" name="title" value="{$data.title}" data-rule="required" size="35"></td>
        </tr>
        <tr>
          <td>开始时间:</td><td><input type="text" data-toggle="datepicker" value="{$data.starttime|date='Y-m-d',###}" name="starttime" class="required" data-rule="required"></td>
          <td>结束时间:</td><td><input type="text" data-toggle="datepicker" value="{$data.endtime|date='Y-m-d',###}" name="endtime" class="required" data-rule="required"></td>
        </tr>
        <tr>
          <td>活动场景:</td>
          <td colspan="3">
            <input type="checkbox" name="scene[]" value="1" <if condition="in_array('1',explode(',',$data['scene']))">checked</if>> 窗口
            <input type="checkbox" name="scene[]" value="2" <if condition="in_array('2',explode(',',$data['scene']))">checked</if>> 渠道版
            <input type="checkbox" name="scene[]" value="3" <if condition="in_array('3',explode(',',$data['scene']))">checked</if>> 网站
            <input type="checkbox" name="scene[]" value="4" <if condition="in_array('4',explode(',',$data['scene']))">checked</if>> 微信
            <input type="checkbox" name="scene[]" value="5" <if condition="in_array('5',explode(',',$data['scene']))">checked</if>> API
            <input type="checkbox" name="scene[]" value="6" <if condition="in_array('6',explode(',',$data['scene']))">checked</if>> 自助机
          </td>
        </tr>
        <tr>
            <td>活动类型:</td>
            <td colspan="3">
              
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
          <td>备注:</td><td colspan="3"><input type="text" name="remark" placeholder="如:备注" value="{$data.remark}" size="50"></td>
        </tr>
      </tbody>
    </table>
    <if condition="$data['type'] eq '3'">
      <table class="table table-striped table-bordered">
        <tbody>
          <tr>
            <td>身份证号段:</td><td colspan="3"><input type="text" name="card" value="" size="45"><span class="remark">身份证号前6位,多个区域用“|”分隔开</span></td>
          </tr>
          <tr>
            <td>可售票型:</td><td colspan="3"><input type="hidden" name="ticket.id" value="{$data.}">
    <input type="text" name="ticket.name" readonly value="" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/public_get_price',array('ifadd'=>1));}" data-group="ticket" data-width="600" data-height="445" data-title="票型名称" placeholder="票型名称"><span class="remark">如果是多个票型，请勾选追加</span></td>
          </tr>
          <tr>
            <td>其它设置:</td>
            <td colspan="3">
              <input type="checkbox" name="voucher" value="card"> 身份证入园
            </td>
          </tr>
        </tbody>
      </table>
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