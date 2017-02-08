<script type="text/javascript">
function do_open_layout(event, treeId, treeNode) {
    if (treeNode.isParent) {
        var zTree = $.fn.zTree.getZTreeObj(treeId)
        zTree.expandNode(treeNode)
        return
    }
    $(event.target).bjuiajax('doLoad', {url:treeNode.url, target:treeNode.divid})
    event.preventDefault()
}
</script>
<div class="bjui-pageContent">
  <div style="float:left; width:180px;">
    <div style="height:100%; overflow:hidden;">
      <fieldset style="height:100%;">
        <ul id="layout-tree" class="ztree" data-toggle="ztree" data-expand-all="true" data-on-click="do_open_layout">
          <li data-id="9999999" data-pid="0">客户分组</li>
          <volist name="data" id="vo">
            <li data-id="{$vo.id}" data-pid="9999999" data-url="{:U('Crm/Index/grouplist',array('menuid'=>crmlist,'id'=>$vo['id'],'type'=>$vo['type']));}" data-divid="#crmlist">{$vo.name}</li>
          </volist>
        </ul>
      </fieldset>
    </div>
  </div>
  <div style="margin-left:190px; height:99.9%; overflow:hidden;">
    <div id="crmlist" style="height:99.9%; overflow:hidden;"> </div>
  </div>
</div>