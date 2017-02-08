<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--鼠标移动上去效果start--><!--鼠标移动上去效果end-->
<link href="{$config_siteurl}static/css/wechat.css" rel="stylesheet" type="text/css" />
<div class="bjui-pageHeader">
<Managetemplate file="Common/Nav"/>
	<div class="btn-group" role="group"> 
      <a type="button" class="btn btn-success" target="_blank" href="{:U('CustomTmpls/myDynamic',array('type'=>'myDynamicTmpls','token'=>$token))}" ><i class="fa fa-plus"></i> 新增</a> 
      <a type="button" class="btn btn-info" target="_blank" href="{:U('CustomTmpls/myDynamic',array('type'=>'dynamicTmpls','token'=>$token))}" data-id="编辑"><i class="fa fa-pencil"></i> 编辑</a> 
    </div>
</div>

<div class="bjui-pageContent">
	<ul class="cateradio g grid" id="grid">
		<volist id="tpl" name="tmpls">
			<li class="mix {$tpl.attr}<?php if($dynamicTmpls == $tpl['id']){echo ' ck active';} ?>" data-id="{$tpl.id}">
				<div class="mbtip">{$tpl.title|default='暂无模板描述'}</div>
					<img src="{$config_siteurl}static/images/loading.png" data-original="{$tpl.thumbnail}" style="width: 143px;height: 207px;margin-top: 88px;margin-left: 1px;margin-bottom: 70px;display: inline;">
					<if condition="$tpl['sys_tmpls'] eq 0">
						<a href="{:U('CustomTmpls/myDynamic',array('type'=>'editDynamicTmpls','id'=>$tpl['id'],'token'=>$token))}" target="_blank" class="tmpls_set">编辑</a>
					</if>
					<p>
					<input class="radio" type="radio"<if condition="$dynamicTmpls eq $tpl['id']"> checked</if> name="optype" value="{$tpl.id}">
					{$tpl.title}
					</p>
			</li>
			
		</volist>
	</ul>
</div>
<script src="{$config_siteurl}static/js/jquery.tools.min.js" type="text/javascript"></script> 
<script src="{$config_siteurl}static/js/jquery.mixitup.min.js" type="text/javascript"></script>
<script src="{$config_siteurl}static/js/jquery.lazyload.min.js" type="text/javascript"></script>
<script>
	$(document).ready(function(){
		$("img").lazyload();
		$('#grid').mixitup({layoutMode: 'grid'});
		//列表hover效果
		$(".grid li").click(function(){
			var d = $(this);
			var index = layer.confirm('设为首页模板？', {
    			btn: ['确定','取消'], //按钮
    			shade: false //不显示遮罩
			}, function(){
    			d.addClass("active ck").siblings().removeClass('active ck');
				d.find('.radio').attr('checked','checked');
				var myurl='index.php?g=User&m=CustomTmpls&a=dynamic&style='+d.attr('data-id')+'&r='+Math.random(); 
				$.ajax({url:myurl,async:false});
				layer.close(index);
			}, function(){
				layer.close(index);
			});	
		});
		$('.tmpls_set').click(function(e){
			e.stopPropagation();
		});
	});
</script>