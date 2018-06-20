<form class="form-horizontal" action="{:U('Item/Activity/edit',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
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
            <input type="checkbox" name="scene[]" value="1" <if condition="in_array('1',explode(',',$data['is_scene']))">checked</if>> 窗口
            <input type="checkbox" name="scene[]" value="2" <if condition="in_array('2',explode(',',$data['is_scene']))">checked</if>> 渠道版
            <input type="checkbox" name="scene[]" value="3" <if condition="in_array('3',explode(',',$data['is_scene']))">checked</if>> 网站
            <input type="checkbox" name="scene[]" value="4" <if condition="in_array('4',explode(',',$data['is_scene']))">checked</if>> 微信
            <input type="checkbox" name="scene[]" value="5" <if condition="in_array('5',explode(',',$data['is_scene']))">checked</if>> API
            <input type="checkbox" name="scene[]" value="6" <if condition="in_array('6',explode(',',$data['is_scene']))">checked</if>> 自助机
          </td>
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
            <td></td><td>
              </td>
        </tr>
        <tr>
            <td>参与范围:</td>
            <td><select name="scope" class="required" data-toggle="selectpicker" data-rule="required">
                <option value="1" <if condition="$data['scope'] eq 1">selected</if>>开启</option>
                <option value="0" <if condition="$data['scope'] eq 0">selected</if>>关闭</option>
              </select>
              <span class="remark">限制渠道商参与</span>
            </td>
            <td>实名制入园:</td><td>
              <select name="real" class="required" data-toggle="selectpicker" data-rule="required">
                <option value="1" <if condition="$data['real'] eq 1">selected</if>>开启</option>
                <option value="0" <if condition="$data['real'] eq 0">selected</if>>关闭</option>
              </select>
              <span class="remark">下单时需要输入身份证</span>
            </td>
        </tr>
        <tr>
            <td>排序:</td><td><input type="text" name="sort" value="{$data.sort}" size="15"></td>
            <td>状态:</td><td>
              <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
                <option value="">状态</option>
                <option value="1" <if condition="$data['status'] eq 1">selected</if>>启用</option>
                <option value="0" <if condition="$data['status'] eq 0">selected</if>>禁用</option>
            </select></td>
        </tr>
        <tr>
          <td>备注:</td><td colspan="3"><input type="text" name="remark" placeholder="如:备注" value="{$data.remark}" size="50"></td>
        </tr>
      </tbody>
    </table>
    <if condition="$data['type'] eq '4'">
        <table class="table table-striped table-bordered">
          <tbody>
            <tr>
              <td>单笔订单最小人数:</td><td colspan="3"><input type="text" name="number" value="{$data.param.info.number}" size="15"></td>
            </tr>
            <tr>
              <td>可售票型:</td>
              <td colspan="3"><input type="hidden" name="ticket.id" value="{$data.param.info.ticket}">
      <input type="text" name="ticket.name" readonly value="{$ticket_name}" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/public_get_price',array('ifadd'=>1));}" data-group="ticket" data-width="600" data-height="445" data-title="票型名称" placeholder="票型名称"><span class="remark">如果是多个票型，请勾选追加</span></td>
            </tr>
          </tbody>
        </table>
      
    </if>
    <if condition="$data['type'] eq '3'">
      <table class="table table-striped table-bordered">
        <tbody>
          <tr>
            <td>身份证号段:</td><td colspan="3"><input type="text" name="card" value="{$card}" size="45"><span class="remark">身份证号前6位,多个区域用“|”分隔开</span></td>
          </tr>
          <tr>
            <td>可售票型:</td><td colspan="3"><input type="hidden" name="ticket.id" value="{$data.param.info.ticket}">
    <input type="text" name="ticket.name" readonly value="{$ticket_name}" size="37" data-toggle="lookup" data-url="{:U('Manage/Index/public_get_price',array('ifadd'=>1));}" data-group="ticket" data-width="600" data-height="445" data-title="票型名称" placeholder="票型名称"><span class="remark">如果是多个票型，请勾选追加</span></td>
          </tr>
          <tr>
            <td>其它设置:</td>
            <td colspan="3">
              <input type="checkbox" name="voucher" value="card" <if condition="$data['param']['info']['voucher'] eq 'card'">checked</if>> 身份证入园
            </td>
          </tr>
        </tbody>
      </table>
    </if>
  </div>
  <input name="type" value="{$data['type']}" type="hidden">
  <input name="id" value="{$data.id}" type="hidden">
  <div class="bjui-pageFooter">
    <ul>
      <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
      <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
  </div>
</form>