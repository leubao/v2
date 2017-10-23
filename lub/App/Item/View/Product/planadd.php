<form class="form-horizontal" action="{:U('Item/Product/planadd',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
  <if condition="$pinfo['type'] eq '1' ">
    <table class="table table-condensed table-hover">
      <tbody>
        <tr>
          <td><label for="j_dialog_operation" class="control-label x90">销售日期:</label>
            <input type="text" data-toggle="datepicker" name="plantime" class="required" data-rule="required"></td>
          <td><label for="j_dialog_code" class="control-label x85">场次：</label>
            <select name="games" id="games" data-toggle="selectpicker">
              <option value="1" selected>第一场</option>
              <option value="2">第二场</option>
              <option value="3">第三场</option>
              <option value="4">第四场</option>
              <option value="5">第五场</option>
            </select></td>
        </tr>
        <tr>
          <td><label for="j_dialog_operation" class="control-label x90">开始时间:</label>
            <input type="text" data-toggle="datepicker" data-pattern='HH:mm:ss' name="starttime" value="{$proconf.plan_start_time}"></td>
          <td><label for="j_dialog_operation" class="control-label x90">结束时间:</label>
            <input type="text" data-toggle="datepicker" data-pattern='HH:mm:ss' name="endtime" value="{$proconf.plan_end_time}"></td>
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
            <volist name="seat" id="vo" mod="3">
              <input type="checkbox" checked="checked" data-toggle="icheck" name="seat[]" value="{$vo.id}" data-label="{$vo.name}（座椅数{$vo.num}个）">
              <eq name="mod" value="3"><br/></eq>
            </volist>
              <input type="hidden" name="template_id" value="{$pinfo['template_id']}">
          </div>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingTwo">
          <h4 class="panel-title"> <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"> 价格政策 </a> </h4>
        </div>
        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
          <div class="panel-body">
            <volist name="group" id="group" >
              <fieldset>
                <legend>{$group.name}</legend>
                <volist name="group['TicketType']" id="type" mod="3">
                  <input type="checkbox" checked="checked" data-toggle="icheck" name="ticket[]" value="{$type.id}" data-label="{$type.name}（价格：{$type.discount}）">
                  <eq name="mod" value="2"><br/></eq>
                </volist>
              </fieldset>
            </volist>
          </div>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="heading3">
          <h4 class="panel-title"> <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse3" aria-expanded="false" aria-controls="collapse3"> 小商品 </a> </h4>
        </div>
        <div id="collapse3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading3">
          <div class="panel-body">
            <volist name="goods" id="goods" mod="3">
                <input type="checkbox" checked="checked" data-toggle="icheck" name="goods[]" value="{$goods.id}" data-label="{$goods.title}">
                  <eq name="mod" value="2"><br/></eq>
            </volist>
          </div>
        </div>
      </div>
    </div>
  <elseif condition="$pinfo['type'] eq '2'" />
    <table id="tabledit2" class="table table-bordered table-hover table-striped table-top" data-toggle="tabledit" data-initnum="0" data-action="#" data-single-noindex="true">
            <thead>
                <tr data-idname="plan[#index#][id]">
                    <th title="No."><input type="text" name="plan[#index#][no]" class="no" data-rule="required" value="1" size="2"></th>
                    <th title="销售日期" align="center"><input type="text" name="plan[#index#][plantime]" data-rule="required" class="j_custom_issuedate" data-toggle="datepicker" value="2017-10-21" size="10"></th>
                    <th title="开园时间" align="center"><input type="text" name="plan[#index#][starttime]" data-pattern='HH:mm:ss' data-rule="required" class="j_custom_issuedate" data-toggle="datepicker" value="{$proconf.plan_start_time}" size="10"></th>
                    <th title="闭园时间" align="center"><input type="text" name="plan[#index#][endtime]" data-pattern='HH:mm:ss' data-rule="required" class="j_custom_indate"  data-toggle="datepicker" value="{$proconf.plan_end_time}" size="10"></th>
                    <th title="销售配额" align="center"><input type="text" name="plan[#index#][quotas]" data-rule="required" value="{$proconf.quotas}" size="5"></th>
                    <th title="渠道配额" align="center"><input type="text" name="plan[#index#][quota]" data-rule="required" value="{$proconf.quota}" size="5"></th>
                    <th title="" data-addtool="true" width="100">
                        <a href="javascript:;" class="btn btn-red row-del" data-confirm-msg="确定要删除该行信息吗？">删</a>
                    </th>
                </tr>
            </thead>
            <tbody>
              
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
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="heading3">
          <h4 class="panel-title"> <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse3" aria-expanded="false" aria-controls="collapse3"> 小商品 </a> </h4>
        </div>
        <div id="collapse3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading3">
          <div class="panel-body">
            <volist name="goods" id="goods" mod="3">
                <input type="checkbox" checked="checked" data-toggle="icheck" name="goods[]" value="{$goods.id}" data-label="{$goods.title}">
                  <eq name="mod" value="2"><br/></eq>
            </volist>
          </div>
        </div>
      </div>
    </div>

  <elseif condition="$pinfo['type'] eq '3'" />
    <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <td width="80px">销售日期:</td>
          <td><input type="text" data-toggle="datepicker" name="plantime"></td>
          <td width="80px"></td>
          <td></td>
        </tr>
        
      </tbody>
    </table>
    <table id="tabledit1" class="table table-bordered table-hover table-striped table-top" data-toggle="tabledit" data-initnum="0" data-action="#" data-single-noindex="true">
            <thead>
                <tr data-idname="plan[#index#][id]">
                    <th title="No."><input type="text" name="plan[#index#][no]" class="no" data-rule="required" value="1" size="2"></th>
                    
                    <th title="开始时间"><input type="text" name="plan[#index#][starttime]" data-pattern='HH:mm:ss' data-rule="required" class="j_custom_issuedate" data-toggle="datepicker" value="" size="10"></th>
                    <th title="结束时间"><input type="text" name="plan[#index#][endtime]" data-pattern='HH:mm:ss' data-rule="required" class="j_custom_indate"  data-toggle="datepicker" value="{$proconf.plan_end_time}" size="10"></th>
                    <th title="销售配额"><input type="text" name="plan[#index#][quotas]" data-rule="required" value="{$proconf.quotas}" size="5"></th>
                    <th title="渠道配额"><input type="text" name="plan[#index#][quota]" data-rule="required" value="{$proconf.quota}" size="5"></th>
                    <th title="工具类型"><select name="plan[#index#][tooltype]" data-toggle="selectpicker">
                      <option value="0">===请选择===</option>
                      <volist name="tooltype" id="vo">
                        <option value="{$vo.id}">{$vo.title}</option>
                      </volist>
                    </select>
                    </th>
                    
                    
                    
                    <th title="" data-addtool="true" width="100">
                        <a href="javascript:;" class="btn btn-red row-del" data-confirm-msg="确定要删除该行信息吗？">删</a>
                    </th>
                </tr>
            </thead>
            <tbody>
              
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
          <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="heading3">
          <h4 class="panel-title"> <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse3" aria-expanded="false" aria-controls="collapse3"> 小商品 </a> </h4>
        </div>
        <div id="collapse3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading3">
          <div class="panel-body">
            <volist name="goods" id="goods" mod="3">
                <input type="checkbox" checked="checked" data-toggle="icheck" name="goods[]" value="{$goods.id}" data-label="{$goods.title}">
                  <eq name="mod" value="2"><br/></eq>
            </volist>
          </div>
        </div>
      </div>
        </div>
  </if>
  </div>
  <input name="product_id" value="{$pinfo['id']}" type="hidden">
  <input name="product_type" value="{$pinfo['type']}" type="hidden">
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
<script type="text/javascript">
//自动有效日期
$(document).on('afterchange.bjui.datepicker', '.j_custom_issuedate', function(e, data) {
    var pattern = 'yyyy-MM-dd'
    var start   = end = data.value
    var $end    = $(this).closest('tr').find('.j_custom_indate')
    
    if ($end.length) {
        end.setFullYear(start.getFullYear() + 10)
        end.setDate(start.getDate() - 1)
        $end.val(end.formatDate(pattern))
    }
})
</script>