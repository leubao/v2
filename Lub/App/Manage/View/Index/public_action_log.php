<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader"> 
<style type="text/css" media="screen">
.main {
 width: 100%;
 margin-top: 100px;
 text-align: center;
 font-size: 12.5px;
}
 
th, td {
 border: 1px solid #ccc;
 line-height: 40px;
 padding-left: 5px;
}
.item:hover {
 background-color: #efefef;
}
.item:nth-child(2n) {
 background-color: #efefef;
}
.ListView {
 width: 600px;
 overflow: hidden;
 margin: 0 auto;
 padding: 10px;
 height:372px;
 border: 1px solid #dddddd;
}
.ListView .c {
 width: 1200px;
 margin: 0 auto;
 border-collapse: collapse;
}
.Item {
 border-bottom: 1px dashed #dddddd;
 padding: 10px 0 10px 0;
 overflow: hidden;
 margin-left:600px;
}
.Item span {
 float: left;
 text-align: left;
}
.Item span:first-child {
 color: #6AA8E8;
}
.Item span:last-child {
 text-align: center;
}
</style>
</div>
<div class="bjui-pageContent">
<div class="main">
 <div class="ListView">
  <div class="c">
  <div class="Item"> <span>test</span> <span>男/0</span> <span>四川省，成都市，锦江区</span> <span>详细说明</span> </div>
  <div class="Item"> <span>test</span> <span>男/0</span> <span>四川省，成都市，锦江区</span> <span>详细说明</span> </div>
  <div class="Item"> <span>test</span> <span>男/0</span> <span>四川省，成都市，锦江区</span> <span>详细说明</span> </div>
  <div class="Item"> <span>test</span> <span>男/0</span> <span>四川省，成都市，锦江区</span> <span>详细说明</span> </div>
  <div class="Item"> <span>test</span> <span>男/0</span> <span>四川省，成都市，锦江区</span> <span>详细说明</span> </div>
  <div class="Item"> <span>test</span> <span>男/0</span> <span>四川省，成都市，锦江区</span> <span>详细说明</span> </div>
  <div class="Item"> <span>test</span> <span>男/0</span> <span>四川省，成都市，锦江区</span> <span>详细说明</span> </div>
  <div class="Item"> <span>test</span> <span>男/0</span> <span>四川省，成都市，锦江区</span> <span>详细说明</span> </div>
  <div class="Item"> <span>test</span> <span>男/0</span> <span>四川省，成都市，锦江区</span> <span>详细说明</span> </div>
  <div class="Item"> <span>test</span> <span>男/0</span> <span>四川省，成都市，锦江区</span> <span>详细说明</span> </div>
 </div>
 </div>
</div>
http://demo.jb51.net/js/2015/jquery-dtlb/
<p style="text-align:center;"><a href="javascript:void(0);" onClick="ListView.ListUpdate();">刷新数据</a></p>
</div>
<script type="text/javascript">
$(function(){
	ListView.ListInit();
});
var ListView = {
	ListInit:function(){
		$(".Item span").css("width",$(".ListView").width()/4+"px");
		for(var i=0;i<$(".Item").length;i++){
			var target=$(".Item")[i];
			$(target).animate({marginLeft:"0px"},300+i*100);
		}
	},
	ListUpdate:function(){
		$(".ListView .c .Item").remove();
		for(var i=0;i<10;i++){
			var newItem=$("<div class=\"Item\"> <span>test</span> <span>男/"+i+"</span> <span>四川省，成都市，锦江区</span> <span>详细说明</span> </div>");
			$(newItem).find("span").css("width",$(".ListView").width()/4+"px");
			$(".ListView .c").append(newItem);
			$(newItem).animate({marginLeft:"0px"},300+i*100);
		}
	}
}
</script>
