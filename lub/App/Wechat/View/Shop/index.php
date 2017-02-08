<!DOCTYPE html>
<html>
	<head>
	    <title>{$product.title}</title>
	    <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
    	<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="description" content="">

		<link rel="stylesheet" href="//cdn.bootcss.com/weui/0.4.3/style/weui.min.css">
		<link rel="stylesheet" href="//cdn.bootcss.com/jquery-weui/0.8.0/css/jquery-weui.min.css">
		<style type="text/css">
		.page-detail {
		    color: #333;
		    min-height: 100%;
		    background-color: #f2f3f4;
		}
		
		.page-detail .section-cell {
		    background-color: #fff;
		    position: relative;
		    margin-bottom: .3125rem;
		    border-bottom: 1px solid #e0e0e0;
		    border-top: 1px solid #e0e0e0;
		}
		.page-detail .base-info {
		    margin-bottom: .3125rem;
		    border-top: 0;
		}
		.page-detail .base-info .header {
		    position: relative;
		    border-bottom: 1px solid #fff;
		}
		/*top img*/
		.page-detail .base-info .scenic-img img {
		    width: 100%;
		}
		.page-detail .backup-img {
		    background: #e3edf4 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAALQAAAC0CAMAAAAKE/YAAAAAolBMV…0TYMjYrOIok1R1OBhoyJwy/Mim6zf5ix1BEARBEARBeGs/Aa79IjRhT5asAAAAAElFTkSuQmCC) no-repeat center;
		}
		.page-detail .base-info .scenic-img {
		    height: 5.0625rem;
		    overflow: hidden;
		}
		/*top desc*/
		.page-detail .base-info .header-bottom {
		    position: absolute;
		    bottom: 0;
		    width: 9.25rem;
		    color: #fff;
		    padding: 0 .375rem .28125rem;
		    background-image: linear-gradient(-180deg,transparent 0,#000 100%);
		}

		.page-detail .base-info .scenic-title {
		    font-size: 18px;
		    line-height: .75rem;
		    height: .75rem;
		}
		.page-detail .base-info .scenic-title {
		    font-size: 36px;
		}
		.page-detail .base-info .scenic-des {
		    display: -webkit-box;
		    display: box;
		    line-height: .59375rem;
		    height: .59375rem;
		    font-size: 13px;
		}
		.page-detail .base-info .scenic-des {
		    font-size: 26px;
		}
		.page-detail .base-info .scenic-des span {
		    display: block;
		}

		.page-detail .base-info .scenic-more {
		    height: 1.15625rem;
		    line-height: 1.0625rem;
		    font-size: 13px;
		    color: #666;
		    text-align: center;
		}
		.page-detail .base-info .scenic-detail-des {
		    padding: .34375rem .375rem 0;
		    font-size: 13px;
		    line-height: .59375rem;
		    max-height: 100%;
		    overflow: hidden;
		}
		.page-detail .base-info .scenic-detail-des {
		    font-size: 26px;
		}
		/*介绍合拢*/
		.page-detail .base-info .scenic-detail-des.close {
		    height: 1.1875rem;
		    text-overflow: ellipsis;
		    display: -webkit-box;
		    -webkit-line-clamp: 2;
		    -webkit-box-orient: vertical;
		}
		/*介绍展开*/
		.page-detail .base-info .scenic-detail-des.expend {
		    text-align: justify;
		}
		

		.page-detail .sale-detail .sale-item {
		    line-height: 1.5625rem;
		    height: 1.5625rem;
		    padding-right: .375rem;
		    margin-left: .375rem;
		    position: relative;
		    overflow: hidden;
		    display: -webkit-box;
		    display: box;
		    border-bottom: 1px solid #e0e0e0;
		}

		.page-detail .sale-detail .sale-item-list {
    background-color: #f2f3f4;
}
.page-detail .sale-detail .sale-sku-item {
    line-height: 2.5rem;
    height: 2.5rem;
}
.page-detail .sale-detail .sale-item .sale-item-des {
    -webkit-box-flex: 1;
    box-flex: 1;
    font-size: 15px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.page-detail .sale-detail .sale-sku-item .sale-item-des {
    padding: .3125rem 0;
}
[data-dpr="2"] .page-detail .sale-detail .sale-item .sale-item-des {
    font-size: 30px;
}
/**
		 * 价格
		 */

.page-detail .price {
    font-family: 'Helvetica Neue',regular;
    font-size: 20px;
    color: #ff5b45;
    text-shadow: none;
}
.page-detail .sale-detail .sale-item .sale-item-price {
    margin-right: .375rem;
}
.page-detail .sale-detail .sale-item .sale-item-opt {
    text-align: center;
}
.page-detail .price em {
    font-size: 12px;
}
.page-detail .sale-detail .sale-sku-item .sale-item-seller, .page-detail .sale-detail .sale-sku-item .sale-item-ticket, .page-detail .sale-detail .sale-sku-item .sale-item-tag {
    line-height: .46875rem;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.page-detail .sale-detail .sale-sku-item .sale-item-tag {
    display: -webkit-box;
    padding: .21875rem 0 .21875rem .03125rem;
    height: .375rem;
}
	</style>
	</head>

<body ontouchstart>
	<div class="weui_grids page-detail">
	<!--头部图片 s-->
	<section class="base-info section-cell">
		<div class="header" data-spm-click="gostr=/tbtrip;locaid=d30;">
		<div class="scenic-img backup-img">
		<img src="//img.alicdn.com/bao/uploaded/i3/TB1JhGqFVXXXXaXXFXXqPUd.pXX-768-495.jpg" alt=""></div>

		<div class="header-bottom" 1"><div class="scenic-title">颐和园</div><div class="scenic-des"><span class="scenic-short-des" 1>饱含中国皇家园林的恢弘富丽气势</span><span class="scenic-score"></span></div></div></div>


	
	<!--头部图片 e-->

	<!--注意信息 s-->
	

	<div class="weui_panel">
	  <div class="weui_panel_bd">
	  	<div class="weui_cells weui_cells_access">
		  <a class="weui_cell" href="javascript:;">
		    <div class="weui_cell_hd">
		      <i class="weui_icon_warn"></i>
		    </div>
		    <div class="weui_cell_bd weui_cell_primary">
		      <p>旺季06:30-18:00；淡季07:00-17:00</p>
		    </div>
		  </a>
		  <a class="weui_cell" href="javascript:;">
		    <div class="weui_cell_hd">
		      <img src="" alt="icon" style="width:20px;margin-right:5px;display:block">
		    </div>
		    <div class="weui_cell_bd weui_cell_primary">
		      <p>新建宫门路19号</p>
		    </div>
		    <div class="weui_cell_ft">
		    </div>
		  </a>
		</div>
	    <div class="weui_media_box weui_media_text scenic-detail-des close">
	      <span class="weui_media_desc">颐和园位于北京西北郊海淀区内，是三山五园中最后兴建的一座园林，是我国现存规模最大，保存最完整的皇家园林，享誉世界的旅游胜地之一。颐和园原是清朝帝王的行宫和花园。公元1750年，乾隆皇帝弘历经十余年，利用昆明湖、万寿山为基址，以杭州西湖风景为蓝本，汲取江南园林的某些设计手法和意境建起一座大型天然山水园，命名清漪园。公元1860年，清漪园被英法联军焚毁。公元1886年，慈禧太后挪用海军经费3000万两白银重建该园，并于1888年改园名为颐和园。1961年3月4日，颐和园被公布为第一批全国重点文物保护单位。1998年12月2日，颐和园以其丰厚的历史文化积淀，优美的自然环境景观，卓越的保护管理工作被联合国教科文组织列入《世界遗产名录》，誉为世界几大文明之一的有力象征。2007年5月8日，颐和园经国家旅游局正式批准为国家5A级旅游景区。2009年，入选中国世界纪录协会中国现存最大的皇家园林。</span><br 1"><br 2"><span 3">交通信息：其他驾车从西北四环香山方向出口出去，行至中坞路口，一直向北（玉泉山方向）直行，大约５００米（北坞村）见红绿灯右转，一直向东行驶即到颐和园西门。这里收费停车场不大，但可以将车辆停在路边（不收费）。有时中坞路口也会发生拥堵，还有一条线路适合从东四环过来的朋友。从东四环向西行驶至火器营桥出口出去，沿辅路穿过火器营桥，下桥后右边有一条小路（汽车检测场东墙外），沿小路一直向北直行，行驶大约１５００米即到颐和园西门。这条路车辆很少，但路面较窄。游完后可原路返回。公交颐和园：209、330、331、332、346、394、712、718、726、732、696、683、801、808、817、926颐和园北宫门：303、330、331、346、375、384、393、634、716、718、696、683、801、808、817、834、特5颐和园新建宫门：374、437、704、992、481、952颐和园西门：469</span>
	      <div class="scenic-more">查看更多</div>
	    </div>
	  </div>
	</div>
	<!--注意地址 e-->

	<!--票型价格 s-->
	<div class="weui_tab sale-detail">
	  <div class="weui_navbar">
	    <a href="#tab1" class="weui_navbar_item weui_bar_item_on">
	     	成人票
	    </a>
	    <a href="#tab2" class="weui_navbar_item">
	      选项二
	    </a>
	  </div>
	  <div class="weui_tab_bd">
	  	<div id="tab1" class="weui_tab_bd_item weui_tab_bd_item_active">
	  	<!--
	  		<div class="weui_panel weui_panel_access">
			  <div class="weui_panel_bd">
			    <div class="weui_media_box weui_media_text">
			      <h4 class="weui_media_title">成人票</h4>
			      <p class="weui_media_desc">可定今日门票</p>
			      <ul class="weui_media_info">
			        <li class="weui_media_info_meta">票型说明</li>
			      </ul>
			      
			    </div>
			    <div class="weui_media_hd">
			        <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_primary">按钮</a>
			    </div>
			  </div>
			</div>
		-->
	      <div class="sale-item-list" data-reactid=".0.1.2.$0.$sku0">
	      <div class="sale-item sale-sku-item" data-reactid=".0.1.2.$0.$sku0.$0">
	      <div class="sale-item-des" data-sku="3190479287757" data-reactid=".0.1.2.$0.$sku0.$0.0"><div class="sale-item-seller" data-reactid=".0.1.2.$0.$sku0.$0.0.0">常州凤祥旅游专营店</div><div class="sale-item-tag" data-reactid=".0.1.2.$0.$sku0.$0.0.1"><span style="color:#009ff0;border-color:#009ff0;" data-reactid=".0.1.2.$0.$sku0.$0.0.1.$0">可订今日票</span><span style="color:#ff5b45;border-color:#ff5b45;" data-reactid=".0.1.2.$0.$sku0.$0.0.1.$1">最低</span></div><div class="sale-item-ticket" data-spm-click="gostr=/tbtrip;locaid=d510" data-reactid=".0.1.2.$0.$sku0.$0.0.2">票型说明</div></div><div class="sale-item-price price" data-reactid=".0.1.2.$0.$sku0.$0.2"><div class="price" data-reactid=".0.1.2.$0.$sku0.$0.2.0"><em data-reactid=".0.1.2.$0.$sku0.$0.2.0.0">¥&nbsp;</em><span data-reactid=".0.1.2.$0.$sku0.$0.2.0.1">59</span></div></div><div class="sale-item-opt" data-reactid=".0.1.2.$0.$sku0.$0.3"><span data-skuid="3190479287757" data-jump="//h5.m.taobao.com/trip/confirm/calendar/index.html?itemId=534765436007&amp;packageName=%E6%88%90%E4%BA%BA%E7%A5%A8" class="sale-item-btn" data-spm-click="gostr=/tbtrip;locaid=d500;" data-reactid=".0.1.2.$0.$sku0.$0.3.0">预订</span></div></div><div class="sale-item sale-sku-item" data-reactid=".0.1.2.$0.$sku0.$1"><div class="sale-item-des" data-sku="3181714118493" data-reactid=".0.1.2.$0.$sku0.$1.0"><div class="sale-item-seller" data-reactid=".0.1.2.$0.$sku0.$1.0.0">驴妈妈旅游专卖店</div><div class="sale-item-tag" data-reactid=".0.1.2.$0.$sku0.$1.0.1"><span style="color:#009ff0;border-color:#009ff0;" data-reactid=".0.1.2.$0.$sku0.$1.0.1.$0">可订今日票</span><span style="color:#ff5b45;border-color:#ff5b45;" data-reactid=".0.1.2.$0.$sku0.$1.0.1.$1">最低</span></div><div class="sale-item-ticket" data-spm-click="gostr=/tbtrip;locaid=d511" data-reactid=".0.1.2.$0.$sku0.$1.0.2">票型说明</div></div><div class="sale-item-price price" data-reactid=".0.1.2.$0.$sku0.$1.2"><div class="price" data-reactid=".0.1.2.$0.$sku0.$1.2.0"><em data-reactid=".0.1.2.$0.$sku0.$1.2.0.0">¥&nbsp;</em><span data-reactid=".0.1.2.$0.$sku0.$1.2.0.1">59</span></div></div><div class="sale-item-opt" data-reactid=".0.1.2.$0.$sku0.$1.3"><span data-skuid="3181714118493" data-jump="//h5.m.taobao.com/trip/confirm/calendar/index.html?itemId=520381274516&amp;packageName=%E6%88%90%E4%BA%BA%E7%A5%A8" class="sale-item-btn" data-spm-click="gostr=/tbtrip;locaid=d501;" data-reactid=".0.1.2.$0.$sku0.$1.3.0">预订</span></div></div><div class="sale-item sale-sku-item" data-reactid=".0.1.2.$0.$sku0.$2"><div class="sale-item-des" data-sku="3201917675335" data-reactid=".0.1.2.$0.$sku0.$2.0"><div class="sale-item-seller" data-reactid=".0.1.2.$0.$sku0.$2.0.0">苏州汇景天下旅游专营店</div><div class="sale-item-tag" data-reactid=".0.1.2.$0.$sku0.$2.0.1"><span style="color:#009ff0;border-color:#009ff0;" data-reactid=".0.1.2.$0.$sku0.$2.0.1.$0">可订今日票</span></div><div class="sale-item-ticket" data-spm-click="gostr=/tbtrip;locaid=d512" data-reactid=".0.1.2.$0.$sku0.$2.0.2">票型说明</div></div><div class="sale-item-price price" data-reactid=".0.1.2.$0.$sku0.$2.2"><div class="price" data-reactid=".0.1.2.$0.$sku0.$2.2.0"><em data-reactid=".0.1.2.$0.$sku0.$2.2.0.0">¥&nbsp;</em><span data-reactid=".0.1.2.$0.$sku0.$2.2.0.1">60</span></div></div><div class="sale-item-opt" data-reactid=".0.1.2.$0.$sku0.$2.3"><span data-skuid="3201917675335" data-jump="//h5.m.taobao.com/trip/confirm/calendar/index.html?itemId=536596948724&amp;packageName=%E6%88%90%E4%BA%BA%E7%A5%A8" class="sale-item-btn" data-spm-click="gostr=/tbtrip;locaid=d502;" data-reactid=".0.1.2.$0.$sku0.$2.3.0">预订</span></div></div>
	      <div class="item-more" data-spm-click="gostr=/tbtrip;locaid=d52;" data-reactid=".0.1.2.$0.$sku0.$more">查看更多代理商</div></div>
	    </div>
	    <div id="tab2" class="weui_tab_bd_item">
			<p>sdasd</p>
	    </div>
	  </div>
	</div>
	<!--票型价格 e-->
	</section>
	</div>
</body>
</html>