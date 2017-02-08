/*获取鼠标坐标位置*/
function getMousePoint(ev) {  
    // 定义鼠标在视窗中的位置  
    var point = {  
        x:0,  
        y:0  
    };  
   
    // 如果浏览器支持 pageYOffset, 通过 pageXOffset 和 pageYOffset 获取页面和视窗之间的距离  
    if(typeof window.pageYOffset != 'undefined') {  
        point.x = window.pageXOffset;  
        point.y = window.pageYOffset;  
    }  
    // 如果浏览器支持 compatMode, 并且指定了 DOCTYPE, 通过 documentElement 获取滚动距离作为页面和视窗间的距离  
    // IE 中, 当页面指定 DOCTYPE, compatMode 的值是 CSS1Compat, 否则 compatMode 的值是 BackCompat  
    else if(typeof document.compatMode != 'undefined' && document.compatMode != 'BackCompat') {  
        point.x = document.documentElement.scrollLeft;  
        point.y = document.documentElement.scrollTop;  
    }  
    // 如果浏览器支持 document.body, 可以通过 document.body 来获取滚动高度  
    else if(typeof document.body != 'undefined') {  
        point.x = document.body.scrollLeft;  
        point.y = document.body.scrollTop;  
    }  
   
    // 加上鼠标在视窗中的位置  
    point.x += ev.clientX;  
    point.y += ev.clientY;  
   
    // 返回鼠标在视窗中的位置  
    return point;  
}
/*贤心弹窗*/
function progress_bar(){
    var str ="<div class='cache_msg'></div>";
    layer.msg(str);
}
/*格式化日期*/
function FormatDate (strTime) {
    var date = new Date(strTime);
    return date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
}
//景区漂流加载价格
function scenic_drifting_plan(plantime,type){
    var postData = 'info={"plantime":"'+plantime+'","type":"'+type+'"}',
        zTreeObj,
        setting = {
            callback: {
                onClick: getprice
            }
        };
    //切换日期查询场次
    $.ajax({
        type:'POST',
        url:'index.php?g=Item&m=Work&a=public_get_date_plan',
        data:postData,
        dataType:'json',
        timeout: 3500,
        error: function(){
          layer.msg('服务器请求超时，请检查网络...');
        },
        success:function(rdata){
            if(rdata.statusCode == "200"){
               if(rdata.plan != null){
                    var zTree = $.fn.zTree.init($("#plan_games"),setting,rdata.plan);
               }else{
                   var error_msg = "<tr><td style='padding:15px;' colspan='5' align='center'><strong style='color:red;font-size:18px;'>未找到可售计划</strong></td></tr>";
                   $("#plan_games").html(error_msg); 
               }
            }else{
                $(this).alertmsg('error','出票失败!');
            }
        }
    });
}
//加载价格
function getprice(event, treeId, treeNode){
    var data = 'info={"area":'+treeNode.id+',"type":'+treeNode.type+',"plan":'+treeNode.plan+'}',
        content = '',
        url = 'index.php?g=Item&m=Work&a=getprice';
    //刷新购物车
    $(this).bjuiajax('refreshDiv', 'quick-price-select');
    //写入plan
    $("#planID").val(treeNode.plan)
    $.post(url, data, function(rdata) {
        if(rdata.statusCode == '200'){
           if(rdata.price != null){
                $(rdata.price).each(function(idx,ticket){
                  content += "<tr data-id='"+ticket.id+"' data-area='"+treeNode.id+"' data-name='"+ticket.name+"' data-discount='"+ticket.discount+"' data-price='"+ticket.price+"'><td align='center'>"+ticket.name+"</td><td>"+ticket.price+"</td><td>"+ticket.discount+"</td><td align='center'>"+ticket.area_nums+"</td><td align='center'>"+ticket.area_num+"</td>"
                  +"</tr>";
                });
                $("#quick-price").html(content); 
           }else{
               var error_msg = "<tr><td style='padding:15px;' colspan='5' align='center'><strong style='color:red;font-size:18px;'>未找到可售票型</strong></td></tr>";
               $("#quick-price").html(error_msg); 
           }
        }
    },"json");
    event.preventDefault();
}
/** 
* 判断是否null 
* @param data 
*/
function isNull(data){ 
    return (data == "" || data == undefined || data == null) ? false : data; 
}
/*窗口打印*/
(function($){
    $.printBox = function(rel){
        var _printBoxId = 'printBox';
        var $contentBox = rel ? $('#'+rel) : $("body"),
            $printBox = $('#'+_printBoxId);
        if ($printBox.size()==0){
            $printBox = $('<div id="'+_printBoxId+'"></div>').appendTo("body");
        }
        $printBox.html($contentBox.html()).height("auto");
        window.print();
        $printBox.empty();//加上这句来清空printBox。
    }
    
    
})(jQuery);
