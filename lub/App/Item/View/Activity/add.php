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
              <select name="type" class="required" id="activity_type" data-toggle="selectpicker" data-rule="required">
                <option value="">活动类型</option>
                <option value="1" data-area="buy">买赠</option>
                <option value="2" data-area="first">首单免</option>
                <option value="3" data-area="area">限制区域销售</option>
                <option value="4" data-area="team">组团销售</option>
                <option value="5" data-area="more">多产品套票销售</option>
              </select>
            </td>
        </tr>
        <tr>
            <td>参与范围:</td>
            <td><select name="scope" class="required" data-toggle="selectpicker" data-rule="required">
                <option value="1">开启</option>
                <option value="0" selected>关闭</option>
              </select>
              <span class="remark">限制渠道商参与</span>
            </td>
            <td>实名制入园:</td><td>
              <select name="real" class="required" data-toggle="selectpicker" data-rule="required">
                <option value="1">开启</option>
                <option value="0" selected>关闭</option>
              </select>
              <span class="remark">下单时需要输入身份证</span>
            </td>
        </tr>
        <tr>
            <td>打印模板:</td>
            <td>
              <select name="print_tpl" data-toggle="selectpicker">
                  <option value="">打印模板</option>
                  <volist name="printer" id='pri'>
                    <option value="{$pri.id}">{$pri.title}</option>
                  </volist>
                </select>
            </td>
            <td></td><td>
              </td>
        </tr>
        <tr>
            <td>排序:</td><td><input type="text" name="sort" value="0" size="15"></td>
            <td>状态:</td><td>
              <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
                <option value="">状态</option>
                <option value="1" selected>启用</option>
                <option value="0">禁用</option>
            </select></td>
        </tr>
        <tr>
          <td>备注:</td><td colspan="3"><input type="text" name="remark" placeholder="如:备注" value="" size="50"></td>
        </tr>
      </tbody>
    </table>
    <!--买赠-->
    <div id="buy" style="display: none;">
      <table class="table table-striped table-bordered">
        <tbody>
        <volist name="seat" id="vo"> 
        <tr>
            <td>活动区域：</td>
            <td><input type="checkbox" name="area[{$vo.id}]" value="{$vo.id}"> {$vo.id|areaName} 
            买: <input type="text" name="num[{$vo.id}]" value="" data-rule="digits" size="5"> 
              <input type="hidden" name="ticket_num_{$vo.id}.id" value="{$ticket_id}">
              <input type="text" name="ticket_num_{$vo.id}.name" readonly value="{$ticket_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_get_price',array('ifadd'=>0));}" data-group="ticket_num_{$vo.id}" data-width="600" data-height="445" data-title="票型名称" placeholder="票型名称">

            赠送: <input type="text" name="nums[{$vo.id}]" value="" data-rule="digits" size="5">
              <input type="hidden" name="ticket_nums_{$vo.id}.id" value="{$ticket_id}">
              <input type="text" name="ticket_nums_{$vo.id}.name" readonly value="{$ticket_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_get_price',array('ifadd'=>0));}" data-group="ticket_nums_{$vo.id}" data-width="600" data-height="445" data-title="票型名称" placeholder="票型名称"></td>

            
        </tr>
        </volist>
      </tbody>
      </table>
    </div>
    <!--首单免-->
    <!--区域销售-->
    <div id="area" style="display: none;">
      <table class="table table-striped table-bordered">
        <tbody>
          <tr>
            <td>身份证号段:</td><td colspan="3"><input type="text" name="card" value="" size="45"><span class="remark">身份证号前6位,多个区域用“|”分隔开</span></td>
          </tr>
          <tr>
            <td>可售票型:</td><td colspan="3"><input type="hidden" name="ticket.id" value="">
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
    </div>
    <!--组团销售-->
    <div id="team" style="display: none;">
      <table class="table table-striped table-bordered">
        <tbody>
          <tr>
            <td>单笔订单最小人数:</td><td colspan="3"><input type="text" name="number" value="" size="15"></td>
          </tr>
          <tr>
            <td>可售票型:</td>
            <td colspan="3"><input type="hidden" name="ticket.id" value="">
    <input type="text" name="ticket.name" readonly value="" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/public_get_price',array('ifadd'=>1));}" data-group="ticket" data-width="600" data-height="445" data-title="票型名称" placeholder="票型名称"><span class="remark">如果是多个票型，请勾选追加</span></td>
          </tr>
        </tbody>
      </table>
    </div>
    <!--多产品套票-->
    <div id="more" style="display: none;">

      <table class="table table-striped table-bordered">
          <tbody>
            <tr>
              <td>票型名称：</td>
              <td><input type="text" name="price_name" value="">
                销售价格
                <input type="text" name="price" value="">
                结算价格
                <input type="text" name="discount" value="">
              </td>
            </tr>
          <volist name="prolist" id="vo"> 
          <tr>
              <td>活动产品：</td>
              <td><input type="checkbox" name="product[]" value="{$vo.id}"> {$vo.name} 
              参与票型
                <input type="text" name="ticket_{$vo.id}" value="{$ticket_id}">
              </td>
           </tr>
          </volist>
        </tbody>
      </table>
    </div>
  </div>
  <input name="product_id" value="{$product_id}" type="hidden">
  <div class="bjui-pageFooter">
    <ul>
      <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
      <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
  </div>
</form>
<script type="text/javascript">
$(document).ready(function() {
  //监听类型切换
  $('#activity_type').change(function(){
      var selected = $(this).children('option:selected').data('area');
      $("#"+selected).css('display','block');
      $("#activity_type option").map(function(){return $(this).data('area');}).each(function(index,select) {
        if(select != selected){
          $("#"+select).css('display','none');
        }
    });
  });
  function checkSelect(selected) {
    
  }
});
</script>