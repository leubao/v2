<!--[if lt IE 9]>
<script src="http://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
<script src="http://cdn.staticfile.org/modernizr/2.8.3/modernizr.js"></script>
<script src="{$config_siteurl}static/v2/js/amazeui.ie8polyfill.min.js"></script>
<![endif]-->

<!--[if (gte IE 9)|!(IE)]><!-->
<script src="{$config_siteurl}static/v2/js/jquery.min.js"></script>
<!--<![endif]-->

<script type="text/javascript">
	<!--头部导航-->
var SUBMENU_CONFIG = <?php echo $SUBMENU_CONFIG; ?>; /*主菜单区*/
var USER_INFO = <?php echo $USER_INFO; ?>;
var PRO_CONF = <?php echo $PRO_CONF; ?>;
$(function () {
    var html = [],
		child_html = [],
		child_index = 0,
		user_html = [];
	$.each(SUBMENU_CONFIG, function (i, o) {
		if(typeof (o['items']) === 'object'){
			/*存在二级菜单*/
			$.each(o['items'], function (m, n) {
				child_html.push('<li><a href="'+n.url+'" title="' + n.name + '" data-id="' + n.id + '"><span class="am-icon-bug"></span>' + n.name + '</a></li>');
			});
			//结束
			html.push('<li class="admin-parent"><a class="am-cf" data-am-collapse="{target: \''+o.action+'\'}"><span class="am-icon-file"></span>' + o.name + '<span class="am-icon-angle-right am-fr am-margin-right"></span></a><ul class="am-list am-collapse admin-sidebar-sub am-in" id="'+o.action+'">'+ child_html.join('') +'</ul>');
			child_html = [];
		}else{
			/*一级导航*/
			html.push('<li><a href="' + o.url + '" title="' + o.name + '" data-id="' + o.id + '"><span class="am-icon-table"></span>' + o.name + '</a></li>');
		}
    });
    user_html.push('<li class="am-dropdown" data-am-dropdown><a class="am-dropdown-toggle" data-am-dropdown-toggle href="javascript:;"><span class="am-icon-users"></span> '+USER_INFO.nickname+' <span class="am-icon-caret-down"></span></a><ul class="am-dropdown-content"><li><a href="<?php echo U('Home/User/index'); ?>"><span class="am-icon-user"></span> 信息维护</a></li><li><a href="<?php echo U('Home/User/passwords');?>"><span class="am-icon-cog"></span> 密码修改</a></li><li><a href="<?php echo U('Public/logout'); ?>" class="js-modal-toggle"><span class="am-icon-power-off"></span> 退出</a></li></ul></li><li class="am-hide-sm-only"><a href="javascript:;" id="admin-fullscreen"><span class="am-icon-arrows-alt"></span> <span class="admin-fullText">开启全屏</span></a></li>');
	$('#nav').html(html.join(''));
	$('#user').html(user_html.join(''));
	<?php if($uinfo['group']['type'] == '1'){?>
		$('#cash').html(USER_INFO.crm.cash);
	<?php }else{?>
		$('#cash').html(USER_INFO.cash);
	<?php }?>
	$('.print').click(function(){
		if(USER_INFO.param.prints == '1'){
			var url = $(this).data('url');
			layer.open({type: 2,title: '门票打印',skin: 'layui-layer-rim',shadeClose: true,area: ['180px', '210px'],content: [url,'no']});
		}else{
			layer.msg("您没有此项操作权限!");
		}
	});
});
</script>
<script src="{$config_siteurl}static/v2/js/amazeui.min.js"></script>
<script src="{$config_siteurl}static/v2/js/app.js"></script>
<script src="{$config_siteurl}static/js/layer.js"></script>
<script src="{$config_siteurl}static/home/js/common.js"></script>