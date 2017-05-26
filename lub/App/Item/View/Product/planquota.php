<form class="form-horizontal" action="{:U('Item/Product/planquota',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	<div class="panel panel-default">
      <div class="panel-body">
        当前销售计划:{$data['plan_id']|planShow}
      </div>
    </div>
    <!--景区时有效-->
  <if condition="$plan['product_type'] eq '2'">
  <div class="form-group">
    <label class="col-sm-2 control-label">总可售数:</label>
    <input type="text" name="number" class="form-control required" data-rule="digits;" size="20" value="{$data.number}" placeholder="请输入正整数">
  </div>
  </if>
	<div class="form-group">
    <label class="col-sm-2 control-label">常规渠道:</label>
    <input type="text" name="often" class="form-control required" data-rule="digits;" size="20" value="{$data.often}" placeholder="请输入正整数">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">政企渠道:</label>
    <input type="text" name="political" class="form-control required" data-rule="digits;" size="20" value="{$data.political}" placeholder="请输入正整数">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">全员销售:</label>
    <input type="text" name="full" class="form-control required" data-rule="digits;" size="20" value="{$data.full}" placeholder="请输入正整数">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">电商直营:</label>
    <input type="text" name="directly" class="form-control required" data-rule="digits;" size="20" value="{$data.directly}" placeholder="请输入正整数">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">电商渠道:</label>
    <input type="text" name="electricity" class="form-control required" data-rule="digits;" size="20" value="{$data.electricity}" placeholder="请输入正整数">
  </div>
</div>
<input type="hidden" name="plan_id" value="{$data['plan_id']}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">更新</button></li>
    </ul>
</div>
</form>