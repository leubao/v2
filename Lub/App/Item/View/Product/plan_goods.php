<form class="form-horizontal" action="{:U('Item/Product/plan_goods',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
  <div class="panel panel-default">
      <div class="panel-body">
        当前销售计划:{$pid|planShow}
      </div>
    </div>
        <volist name="goods" id="goods" mod='3'>
          <input type="checkbox" data-toggle="icheck" name="goods[]" value="{$goods.id}" data-label="{$goods.title}" <?php if(in_array($goods['id'],$data['goods'])){ echo "checked"; }?>>
          <eq name='mod' value="3"><br /></eq>
        </volist>
  </div>
  <input name="plan_id" value="{$pid}" type="hidden">
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