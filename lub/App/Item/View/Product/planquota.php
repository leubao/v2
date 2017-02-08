<form class="form-horizontal" action="{:U('Item/Product/planquota',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	<div class="panel panel-default">
      <div class="panel-body">
        当前销售计划:{$data.id|planShow}
      </div>
    </div>
	 <div class="form-group">
    <label class="col-sm-2 control-label">配额:</label>
    <if condition="$data.product_type eq '1'">
    <input type="text" name="quota" class="form-control required" data-rule="required;" size="40" value="{$data.quota}" placeholder="请输入正整数">
    <else />
    <input type="text" name="quotas" class="form-control required" data-rule="required;" size="40" value="{$data.quotas}" placeholder="请输入正整数">
    </if>
  </div>
</div>
<input type="hidden" name="id" value="{$data.id}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">更新</button></li>
    </ul>
</div>
</form>