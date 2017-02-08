<?php if (!defined('LUB_VERSION')) exit(); ?>
<script type="text/javascript">
//单击事件
function ZWchatClick(event, treeId, treeNode) {
    event.preventDefault();
    var $detail = $('#ztree-detail-wechat-{$wechat_id}');
  //  alert(treeNode.id);
  //  alert(treeNode.pid);
    if ($detail.attr('tid') == treeNode.tId) return
	if (treeNode.name) $('#w_menu_name_{$wechat_id}').val(treeNode.name)
    treeNode.id ? $('#w_menu_id_{$wechat_id}').val(treeNode.id) : $('#w_menu_id_{$wechat_id}').val('');
    treeNode.pid ? $('#w_menu_pid_{$wechat_id}').val(treeNode.pid) : $('#w_menu_pid_{$wechat_id}').val('0');
    treeNode.type ? $('#w_menu_type_{$wechat_id}').val(treeNode.type) : $('#w_menu_type_{$wechat_id}').val('');
    treeNode.status ? $('#w_menu_status_{$wechat_id}').val(treeNode.status) : $('#w_menu_status_{$wechat_id}').val('');
    treeNode.param ? $('#w_menu_param_{$wechat_id}').val(treeNode.param) : $('#w_menu_param_{$wechat_id}').val('');
    treeNode.sort ? $('#w_menu_sort_{$wechat_id}').val(treeNode.sort) : $('#w_menu_sort_{$wechat_id}').val('');
	$detail.attr('tid', treeNode.tId)
    $detail.show()
}
//保存属性
function M_Ts_Menu() {
	var zTree  = $.fn.zTree.getZTreeObj("ztree_wechat_{$wechat_id}");
	var name   = $('#w_menu_name').val()
	    param    = $('#w_menu_param').val()
	    status = $('#w_menu_status').val();

	
	if ($.trim(name).length == 0) {
		$(this).alertmsg('error','菜单名称不能为空！');
		return;
	}
    if ($.trim(url).length == 0) {
        $(this).alertmsg('error','Url不能为空！');
        return;
    }
	var upNode = zTree.getSelectedNodes()[0]
	
	if (!upNode) {
        $(this).alertmsg('error','未选中任何菜单！')
        return;
	}
    //更新到服务器

    //更新本地树
	upNode.name   = name
	upNode.url    = url
	upNode.status  = status
	upNode.target  = target
    upNode.icon     = icon
	zTree.updateNode(upNode)
}
//删除结束事件
function W_NodeRemove(event, treeId, treeNode) {
    $.ajax({
        type:'GET',
        url:'<?php echo U('Wechat/Wechat/delete',array('type'=>1));?>',
        cache:false,    
        dataType:'json',
        data:{id:treeNode.id},
        success:function(data){
            var type = data.statusCode == '200' ? 'ok':'error';
            $(this).alertmsg(type,data.message);
        }
    });
}
function release_menu(wechatId){
    layer.confirm('自定义菜单最多勾选3个，每个菜单的子菜单最多5个，请确认!(注意：自定义菜单需要第二天，或重新关注才能生效！！！)', {
      btn: ['确定发布','取消'] //按钮
    }, function(){
      $.ajax({
        type:'GET',
        url:'<?php echo U('Wechat/Wechat/menu_send');?>',
        cache:false,    
        dataType:'json',
        data:{id:wechatId},
        success:function(data){
            var type = data.statusCode == '200' ? 'ok':'error';
            layer.closeAll();
            $(this).alertmsg(type,data.message);
        }});
    }, function(){});
}
function remove_menu(wechatId){
    layer.confirm('删除自定义菜单!', {
      btn: ['确定删除','取消'] //按钮
    }, function(){
      $.ajax({
        type:'GET',
        url:'<?php echo U('Wechat/Wechat/remove_menu');?>',
        cache:false,    
        dataType:'json',
        data:{id:wechatId},
        success:function(data){
            var type = data.statusCode == '200' ? 'ok':'error';
            layer.closeAll();
            $(this).alertmsg(type,data.message);
        }});
    }, function(){});
}
</script>
<div class="bjui-pageContent">
    <div style="padding:20px;">
        <div class="clearfix">
            <div style="float:left; width:320px; overflow:auto;">
                <a class="btn btn-green" id="release_menu" onclick="remove_menu({$wechat_id});">删除菜单</a>
                <a class="btn btn-green" id="release_menu" onclick="release_menu({$wechat_id});">发布菜单</a>
                <ul id="ztree_wechat_{$wechat_id}" class="ztree" data-toggle="ztree" data-options="{expandAll: false,onClick: 'ZWchatClick',showRemoveBtn: 'true',showRenameBtn: 'true',addDiyDom: 'true',maxAddLevel:'1',addHoverDom:'edit',removeHoverDom:'edit',onRemove:'W_NodeRemove'}">
                    <li data-id="0" data-pid="0">自定义菜单</li>
                    <volist name="menu" id="vo">
                        <li data-id="{$vo.id}" data-pid="{$vo.parentid}" data-param="{$vo.param}" data-status="{$vo.status}" data-type="{$vo.type}" data-sort="{$vo.sort}">{$vo.name}</li>
                    </volist>
                </ul>
                
            </div>
            <div id="ztree-detail-wechat-{$wechat_id}" style="display:none; margin-left:330px; width:450px; height:470px;">
            <form action="{:U('Wechat/Wechat/menulist',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
                <div class="bs-example" data-content="详细信息">
                    <input type="hidden" name="id" id="w_menu_id_{$wechat_id}" value="">
                    <div class="form-group">
                        <label for="w_menu_parentid" class="control-label x85">上级菜单：</label>
                        <input type="text" class="form-control validate[required] required" name="parentid" id="w_menu_pid_{$wechat_id}" size="15" placeholder="父级菜单" />
                    </div>
                    <div class="form-group">
                        <label for="w_menu_name" class="control-label x85">菜单名称：</label>
                        <input type="text" class="form-control validate[required] required" name="name" id="w_menu_name_{$wechat_id}" size="15" placeholder="名称" />
                    </div>
                    
                    <div class="form-group">
                        <label for="w_menu_status" class="control-label x85">状态：</label>
                        <select class="selectpicker show-tick" class="form-control validate[required] required" id="w_menu_status_{$wechat_id}" name="status" data-style="btn-default btn-sel" data-width="auto">
                            <option value=""></option>
                            <option value="1">启用</option>
                            <option value="0">停用</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="w_menu_type" class="control-label x85">类型：</label>
                        <select class="selectpicker show-tick" id="w_menu_type_{$wechat_id}" name="type" data-style="btn-default btn-sel" data-width="auto">
                            <option value=""></option>
                            <option value="1">关键词回复</option>
                            <option value="2">url</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="w_menu_param" class="control-label x85">参数：</label>
                        <input type="text" class="form-control" name="param" id="w_menu_param_{$wechat_id}" size="25" placeholder="参数" />
                    </div>
                    <div class="form-group">
                        <label for="w_menu_sort" class="control-label x85">排序：</label>
                        <input type="text" class="form-control" name="sort" id="w_menu_sort_{$wechat_id}" size="5" placeholder="0" />
                    </div>
                    <input type="hidden" name="wechat_id" value="{$wechat_id}">
                    <div class="form-group" style="padding-top:8px; border-top:1px #DDD solid;">
                        <label class="control-label x85"></label>
                        <button class="btn btn-green" >新增/更新菜单</button>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
    </ul>
</div>