<foooter class="footer">
  <div class="container mt20" style="border-top:1px #CCCCCC solid;">
    <div class="col-lg-8 mt20">
      <p><small>版权所有©{$Config.company} 网址：<a href="{$Config.website}" target="_blank">{$Config.website}</a> 地址：{$Config.address}</small></p>
    </div>
    <div class="col-lg-2 mt20"><small>联系电话：{$Config.call}</small></div>
    <div class="col-lg-2 mt20"><small>技术支持:<a href="www.leubao.com" target="_blank">leubao.com</a></small></div>
  </div>
  <!--弹出窗口-->
  <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="myModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content"> </div>
    </div>
  </div>
  <!-- /.modal --> 
</foooter>
<!--支付-->
<div id="payment">
    <div class="">当前订单总计：<strong>￥</strong><strong id="totalcash">0.00</strong></div>
    <div class="">账户可用余额：<strong>￥</strong><strong id="money"></strong></div>
    <div class="action">
    	<if condition="$uinfo['group']['type'] neq '3'">
			<button type="button" class="btn btn-success" id="balancePay">余额支付</button>
    	<else />
			<button type="button" class="btn btn-success" id="govPay">立即预订</button>
    	</if>
    	
    	<button type="button" class="btn btn-success" id="wxpay">微信支付</button>
	</div>
</div>
<script src="{$config_siteurl}static/layer/layer.js"></script>
<script src="{$config_siteurl}static/home/js/common.js?=?v={$Config.js_version}"></script>
<script>
//弹窗每次重新加载
$("#myModal").on("hidden.bs.modal", function() {$(this).removeData("bs.modal");});
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
				child_html.push('<li><a href="'+n.url+'" title="' + n.name + '" data-id="' + n.id + '">' + n.name + '</a></li>');
			});
			//结束
			html.push('<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">' + o.name + '<span class="caret"></span></a><ul class="dropdown-menu" role="menu">'+ child_html.join('') +'</ul>');
			child_html = [];
		}else{
			/*一级导航*/
			html.push('<li><a href="' + o.url + '" title="' + o.name + '" data-id="' + o.id + '">' + o.name + '</a></li>');
		}
    });
	user_html.push('<li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span>'+USER_INFO.nickname+'<span class="caret"></span></a><ul class="dropdown-menu" role="menu">  <li><a href="#"></a></li><li><a href="<?php echo U('Home/User/uinfo'); ?>">信息维护</a></li><li><a href="<?php echo U('Home/User/passwords');?>" data-toggle="modal" data-target="#myModal">密码修改</a></li><li class="divider"></li> <li><a href="<?php echo U('Public/logout'); ?>">退出登录</a></li> </ul> </li>');
	$('#nav').html(html.join(''));
	$('#user').html(user_html.join(''));
	<?php if($uinfo['group']['type'] == '1'){?>
		$('#cash').html(USER_INFO.crm.cash);
	<?php }else{?>
		$('#cash').html(USER_INFO.cash);
	<?php }?>
	$('.print').click(function(){
		if(USER_INFO.param.print == '1'){
			var url = $(this).data('url');
			layer.open({type: 2,title: '门票打印',skin: 'layui-layer-rim',shadeClose: true,area: ['180px', '210px'],content: [url,'no']});
		}else{
			layer.msg("您没有此项操作权限!");
		}
	});
});
/*格式化日期时间*/
$('.form_date').datetimepicker({language: 'zh-CN', format: 'yyyy-mm-dd',weekStart: 1,todayBtn:  1,autoclose: 1,todayHighlight: 1,startView: 2,minView: 2,forceParse: 0});
/*提示信息*/
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
});
</script> 