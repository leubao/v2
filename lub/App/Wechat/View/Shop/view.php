<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>我的生活</title>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <link rel="shortcut icon" href="/favicon.ico">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">

    <link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm.min.css">
    <link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm-extend.min.css">
    <style type="text/css">
    .sale-detail {
	    
	    position: relative;
	    overflow: hidden;
	    display: box;
	    border-bottom: 1px solid #e0e0e0;
	}
	.sale-detail .sale-sku-item{
		height: 3.7rem;
		/*line-height: 3.7rem;*/
	}
    	.sale-detail .sale-sku-item .sale-item-ticket {
    		color: #a5a5a5;
    		font-size: .40625rem;
		}
		.sale-item-price {
    margin-right: .375rem;
}
.price {
    font-family: 'Helvetica Neue',regular;
    font-size: .925rem;
    color: #ff5b45;
    text-shadow: none;
}
	.bar-tab .jump{
        background-color: #ff5000;
    	color: #fff;
    }
    </style>
  </head>
  <body>

    <div class="page-group">
        <div class="page page-current">
        	<header class="bar bar-nav">
			  <h1 class='title'>标签页</h1>
			</header>
        	<div class="content">

			    <p>其他内容区域</p>
			    <p>其他内容区域</p>
			    <p>其他内容区域</p>
			    <div class="list-block">
				    <ul>
				      <li class="item-content">
				        <div class="item-media"><i class="icon icon-f7"></i></div>
				        <div class="item-inner">
				          <div class="item-title">商品名称</div>
				          <div class="item-after">杜蕾斯</div>
				        </div>
				      </li>
				      <li class="item-content">
				        <div class="item-media"><i class="icon icon-f7"></i></div>
				        <div class="item-inner">
				          <div class="item-title">型号</div>
				          <div class="item-after">极致超薄型</div>
				        </div>
				      </li>
				    </ul>
				  </div>
			    <p>其他内容区域</p>
			    <p>其他内容区域</p>
			    <div class="buttons-tab fixed-tab" data-offset="44">
			      <a href="#tab1" class="tab-link active button">全部</a>
			      <a href="#tab2" class="tab-link button">待付款</a>
			      <a href="#tab3" class="tab-link button">待发货</a>
			    </div>

			    <div class="tabs">
			      <div id="tab1" class="tab active">
			        <div class="row content-block">
			        <div class="sale-detail">
			        	<div class="row sale-sku-item">
			        		<div class="col-60">
					            <div class="sale-item-seller">要出发旅游</div>
					            <div class="sale-item-ticket open-ticket">票型说明</div>
					          </div>
						      <div class="col-20"><div class="price"><em>¥&nbsp;</em><span>76.9</span></div></div>
						      <div class="col-20"><a href="{:U('Wechat/Shop/calendar',array('pid'=>1));}" class="button button-fill button-danger">预定</a>
						    </div>
			        	</div>
				    	<div class="row sale-sku-item">
			        		<div class="col-60">
					            <div class="sale-item-seller">要出发旅游</div>
					            <div class="sale-item-ticket open-ticket">票型说明</div>
					          </div>
						      <div class="col-20"><div class="price"><em>¥&nbsp;</em><span>76.9</span></div></div>
						      <div class="col-20"><a href="{:U('Wechat/Shop/calendar',array('pid'=>1));}" class="button button-fill button-danger">预定</a>
						    </div>
			        	</div>
			        	<div class="row sale-sku-item">
			        		<div class="col-60">
					            <div class="sale-item-seller">要出发旅游</div>
					            <div class="sale-item-ticket"><a href="#" class="open-ticket" data-popup=".popup-ticket">票型说明</a></div>
					          </div>
						      <div class="col-20"><div class="price"><em>¥&nbsp;</em><span>76.9</span></div></div>
						      <div class="col-20"><a href="#" class="button button-fill button-danger open-ticket">预定</a>
						    </div>
			        	</div>
			        </div>
			          <p style="height:600px">This is tab 2 content start</p>
			       

			        </div>
			      </div>
			      <div id="tab2" class="tab">
			        <div class="content-block">
			          <p style="height:600px">This is tab 2 content start</p>
			          <p >This is tab 2 content end</p>
			        </div>
			      </div>
			      <div id="tab3" class="tab">
			        <div class="content-block">
			          <p style="height:600px">This is tab 3 content start</p>
			          <p >This is tab 3 content end</p>
			        </div>
			      </div>
			    </div>
			</div>
        <!-- 你的html代码 -->
        </div>

        
    </div>
    
	<div class="popup popup-ticket">
		 <!-- 标题栏 -->
	    <header class="bar bar-nav">
	        <h1 class="title">票型说明</h1>
	        <a class="icon icon-me pull-right open-panel close-popup"></a>
	    </header>

	    <!-- 工具栏 -->
	    <nav class="bar bar-tab">
	        <a class="tab-item external active" href="#">
	            <span class="tab-label"><div class="price"><em>¥&nbsp;</em><span>76.9</span></div></span>
	        </a>
	        <a class="tab-item external jump" href="#">
	            <span class="tab-label">立即预定</span>
	        </a>
	    </nav>

	    <!-- 这里是页面内容区 -->
	    <div class="content">
	        <div class="content-block">
	        	<div class="ticket-des-content" data-reactid=".0.1.2.$0.$sku2.$2.1.2"><div class="ticket-des-title" data-reactid=".0.1.2.$0.$sku2.$2.1.2.0">古北水镇 成人票 大门票+长城套票</div><div class="note-list" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$0"><p class="title" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$0.0">代理商</p><p class="desc" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$0.1">同程旅游专卖店</p></div><div class="note-list" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$1"><p class="title" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$1.0">换票地址</p><p class="desc" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$1.1">于游玩当天09:00 - 16:00至古北水镇景区检票口及司马台长城检票口换取门票入园，长城换票截止时间：16:00，入园需携带本人有效的身份证件</p></div><div class="note-list" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$2"><p class="title" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$2.0">入园地址</p><p class="desc" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$2.1">于游玩当天09:00 - 16:00至古北水镇景区检票口及司马台长城检票口换取门票入园，长城换票截止时间：16:00，入园需携带本人有效的身份证件</p></div><div class="note-list" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$3"><p class="title" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$3.0">费用包含</p><p class="desc" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$3.1">北京古北水镇门票+司马台长城门票，门票当天有效。</p></div><div class="note-list" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$4"><p class="title" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$4.0">退票说明</p><p class="desc" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$4.1">如需退票，请最晚在出游当天---19点前申请退款，并联系在线客服取消，逾期无法办理退票，敬请谅解！</p></div><div class="note-list" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$5"><p class="title" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$5.0">改期说明</p><p class="desc" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$5.1"></p></div><div class="note-list" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$6"><p class="title" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$6.0">入园限制</p><p class="desc" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$6.1">提前1天购买，且在22:30:00前下单并完成支付，订单方可生效。</p></div><div class="note-list" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$7"><p class="title" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$7.0">补充说明</p><p class="desc" data-reactid=".0.1.2.$0.$sku2.$2.1.2.1:$7.1">请在游玩当天15:30之前下单，并指定入园日期后付款</p></div></div>
	        </div>
	    </div>
	</div>
    <script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset='utf-8'></script>
    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm.min.js' charset='utf-8'></script>
    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm-extend.min.js' charset='utf-8'></script>
	<script type="text/javascript">
		$(document).on("pageInit", function() {
	      $('.buttons-tab').fixedTab({offset:44});
	    });
		$(document).on('click','.open-ticket', function () {
			$.popup('.popup-ticket');
		  	console.log('About Popup opened')
		});
	</script>
  </body>
</html>