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
    }
})(jQuery);
//景区漂流加载价格
function scenic_drifting_plan(plantime,type,product){
    empty_cart_ticket();
    var postData = 'info={"plantime":"'+plantime+'","type":"'+type+'","product":"'+product+'"}',content = "";
    //切换日期查询场次
    $.ajax({
        type:'POST',
        url:'index.php?g=Home&m=Product&a=get_date_plan',
        data:postData,
        dataType:'json',
        timeout: 3500,
        error: function(){
          layer.msg('服务器请求超时，请检查网络...');
        },
        success:function(rdata){
            if(rdata.statusCode == "200"){
               if(rdata.plan != null){
                    $.each(rdata.plan.children,function(idx,item){
                        if(type == '2'){
                            content += "<li role='presentation'><a href='#' aria-controls='profile' data-id="+item.id+" data-type="+item.type+" role='tab' data-toggle='tab' onclick='getprice("+item.id+","+item.type+")'>"+item.name+"</a></li>";
                        }else{
                            content += "<li role='presentation'><a href='#' aria-controls='profile' data-id="+item.id+" data-type="+item.type+" role='tab' data-toggle='tab' onclick='getprice("+item.id+","+item.type+",\""+item.tooltype+"\")'>"+item.name+"</a></li>";
                        }
                        
                    });
                    $("#tablelist").html(content);
               }else{
                   var error_msg = "<tr><td style='padding:15px;' colspan='5' align='center'><strong style='color:red;font-size:18px;'>未找到可售计划</strong></td></tr>";
                   $("#tablelist").html(error_msg); 
               }
            }else{
                layer.msg("远端服务器连接出错!");
            }
        }
    });
}
//加载价格
function getprice(plan,type,tooltype){
    var data = 'info={"area":'+plan+',"type":'+type+',"plan":'+plan+'}',
        content = '',
        show_price = '',
        url = 'index.php?g=Home&m=Product&a=quickPrice';
    empty_cart_ticket();
    //写入plan
    $("#planID").val(plan);
    $.post(url, data, function(rdata) {
        if(rdata.statusCode == '200'){
           if(rdata.price != null){
                $(rdata.price).each(function(idx,ticket){
                    if(USER_INFO.group.settlement == '1'){
                        show_price = ticket.price;
                    }else{
                        show_price = ticket.discount;
                    }
                    if(type == '2'){
                        content += "<tr id='tro_"+ticket.id+"_"+ticket.id+"' class='tro' data-id='"+ticket.id+"' data-area='"+ticket.id+"' data-name='"+ticket.name+"' data-discount='"+ticket.discount+"' data-price='"+ticket.price+"'>"
                              +"<td align='center'>"+ticket.name+"</td>"
                              +"<td>"+show_price+"</td>"
                              +"<td align='center'>"+ticket.area_num+"</td>"
                              +"</tr>";
                    }else{
                        content += "<tr id='tro_"+ticket.id+"_"+ticket.id+"' class='tro' data-id='"+ticket.id+"' data-area='"+ticket.id+"' data-name='"+ticket.name+"' data-discount='"+ticket.discount+"' data-price='"+ticket.price+"'>"
                              +"<td align='center'>["+tooltype+"]"+ticket.name+"</td>"
                              +"<td>"+show_price+"</td>"
                              +"<td align='center'>"+ticket.area_num+"</td>"
                              +"</tr>";
                    }
                    
                });
                $("#tro").html(content); 
           }else{
               var error_msg = "<tr><td style='padding:15px;' colspan='4' align='center'><strong style='color:red;font-size:18px;'>未找到可售票型</strong></td></tr>";
               $("#tro").html(error_msg); 
           }
        }
    },"json");
}
//切换日期、场次时刷新
function empty_cart_ticket(){
    //刷新购物车
    $("#cart").empty();
    $('#tro').empty();
}