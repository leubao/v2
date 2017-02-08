<form class="form-horizontal" action="{:U('Item/Product/typeedit',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
    <table class="table table-striped table-bordered">
      <tbody>
        <tr>
            <td>所属分组:</td><td> <select name="group_id" class="required" data-toggle="selectpicker" data-rule="required">
          <volist name="group" id="vo">
          <option value="{$vo.id}" <if condition="$data['group_id'] eq $vo['id']">selected</if>>{$vo.name}</option>
          </volist>
        </select></td>
          <td>票型名称:</td><td><input type="text" name="name" value="{$data.name}" data-rule="required" size="25"></td>
        </tr>
        <tr>
          <td>票型价格:</td><td> <input type="text" name="discount" value="{$data.price}" disabled="" size="15"></td>
          <td>补贴金额:</td><td><input type="text" name="rebate" value="{$data.rebate}" data-rule="required" size="15"></td>
        </tr>
        <tr>
          <td>结算价格:</td><td>
            <input type="text" name="discount" value="{$data.discount}" data-rule="required" size="15"></td>
          <td>票型类型:</td><td>
             <select name="type" class="required" data-toggle="selectpicker" data-rule="required">
              <option value="1" <if condition="$data['type'] eq 1">selected</if>>散客票</option>
              <option value="2" <if condition="$data['type'] eq 2">selected</if>>团队票</option>
              <option value="3" <if condition="$data['type'] eq 3">selected</if>>散客、团队票</option>
              <option value="4" <if condition="$data['type'] eq 4">selected</if>>政企渠道票</option>
            </select></td>
        </tr>
        <tr>
          <td>票型特权:</td>
          <td>
            <input type="checkbox" name="param[quota]" value="1" <if condition="$data['param']['quota'] eq '1'">checked</if>> 不消耗配额
          </td>
          
          <td>票面标记:</td>
          <td>
            <input type="checkbox" name="param[ticket_print]" value="1" <if condition="$data['param']['ticket_print'] eq '1'">checked</if>>
              <input type="text" name="param[ticket_print_custom]" value="{$data['param']['ticket_print_custom']}" size="10"><span class="remark">座位号选择自定义时打印该内容</span>
          </td>
        </tr>
        <tr>
          <td>联票支持:</td>
          <td>
            <input type="radio" name="param[present]" value="1" <if condition="$data['param']['present'] eq '1'">checked</if>>  是
             <input type="radio" name="param[present]" value="0" <if condition="$data['param']['present'] eq '0'">checked</if>>  否
            <span class="remark">用于子票设置，主票不用设置</span>
          </td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td>销售场景:</td>
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
          <td>排序:</td><td><input type="text" name="sort" value="0" size="15" value="{$data.sort}"></td>
            <td>状态:</td><td>
              <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
                <option value="1" <if condition="$data['status'] eq 1">selected</if>>启用</option>
                <option value="0" <if condition="$data['status'] eq 0">selected</if>>禁用</option>
            </select></td>
        </tr>
        <tr>
          <td>其它:</td><td colspan="3"><volist name="data['param']['full']" id="v" >{$i}级:{$v}元 / </volist></td>
        </tr>
        <tr>
          <td>备注:</td><td colspan="3"><input type="text" name="remark" placeholder="如：一大人|一儿童" value="{$data.remark}" size="50"></td>
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
              <input type="radio"  data-toggle="icheck" name="area" value="{$vo.id}" <if condition="$data['area'] eq $vo['id']">checked</if> data-label="{$vo.name}（座椅数{$vo.num}个）">
            </volist>
          </div>
        </div>
      </div>
    </if>
  </div>
  <input name="id" value="{$data.id}" type="hidden">
  <div class="bjui-pageFooter">
    <ul>
      <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
      <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
  </div>
</form>