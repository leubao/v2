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
function scenic_drifting_plan(plantime,type,product,actid){
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
                        }
                        if(type == '1'){
                            content += "<li role='presentation'><a href='#' aria-controls='profile' data-id="+item.id+" data-type="+item.type+" role='tab' data-toggle='tab' onclick='getprice("+item.id+","+item.type+",\""+item.tooltype+"\")'>"+item.name+"</a></li>";
                        }
                        //活动
                        if(type == '3'){
                            content += "<li role='presentation'><a href='#' aria-controls='profile' data-id="+item.id+" data-type="+item.type+" role='tab' data-toggle='tab' onclick='getActivtyPrice("+item.id+","+actid+","+item.type+",\""+item.tooltype+"\")'>"+item.name+"</a></li>";
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
    var data = 'info={"area":'+plan+',"type":'+type+',"sale":2,"method":general,"plan":'+plan+'}',
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
                    if(type == '1'){
                      content += "<tr id='tro_"+ticket.id+"_"+ticket.area_id+"' class='tro' data-id='"+ticket.id+"' data-area='"+ticket.area_id+"' data-name='"+ticket.name+"' data-discount='"+ticket.discount+"' data-price='"+ticket.price+"'>"
                              +"<td align='center'>["+ticket.area+"]"+ticket.name+"</td>"
                              +"<td>"+show_price+"</td>"
                              +"<td align='center'>"+ticket.area_num+"</td>"
                              +"</tr>";
                    }
                    if(type == '2'){
                        content += "<tr id='tro_"+ticket.id+"_"+ticket.id+"' class='tro' data-id='"+ticket.id+"' data-area='"+ticket.id+"' data-name='"+ticket.name+"' data-discount='"+ticket.discount+"' data-price='"+ticket.price+"'>"
                              +"<td align='center'>"+ticket.name+"</td>"
                              +"<td>"+show_price+"</td>"
                              +"<td align='center'>"+ticket.area_num+"</td>"
                              +"</tr>";
                    }
                    if(type == '3'){
                        content += "<tr id='tro_"+ticket.id+"_"+ticket.id+"' class='tro' data-id='"+ticket.id+"' data-area='"+ticket.id+"' data-name='"+ticket.name+"' data-discount='"+ticket.discount+"' data-price='"+ticket.price+"'>"
                              +"<td align='center'>["+tooltype+"]"+ticket.name+"</td>"
                              +"<td>"+show_price+"</td>"
                              +"<td align='center'>"+ticket.area_num+"</td>"
                              +"</tr>";
                    }
                    //活动
                    if(type == '4'){

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
//活动加载价格  根据销售计划加载价格
function getActivtyPrice(plan,actid,type) {
  var postData = 'info={"type":'+type+',"seale":2,"actid":'+actid+',"plan":'+plan+',"method":"activity"}',
      content = '',
      url = 'index.php?g=Home&m=Product&a=quickPrice';
      empty_cart_ticket();
      $('#planID').val(plan);
    $.ajax({
        type:'POST',
        url: url,
        data:postData,
        dataType:'json',
        timeout: 3500,
        error: function(){
          layer.msg('服务器请求超时，请检查网络...');
        },
        success:function(rdata){
            if(rdata.statusCode == '200'){
               if(rdata.price != null){
                    $(rdata.price).each(function(idx,ticket){
                        if(USER_INFO.group.settlement == '1'){
                            show_price = ticket.price;
                        }else{
                            show_price = ticket.discount;
                        }
                        content += "<tr id='tro_"+ticket.id+"_"+ticket.area_id+"' class='acttro' data-id='"+ticket.id+"' data-area='"+ticket.area_id+"' data-name='"+ticket.name+"' data-discount='"+ticket.discount+"' data-price='"+ticket.price+"'>"
                                +"<td align='center'>["+ticket.area+"]"+ticket.name+"</td>"
                                +"<td>"+show_price+"</td>"
                                +"<td align='center'>"+ticket.area_num+"</td>"
                                +"</tr>";
                        
                        
                    });
                    $("#tro").html(content); 
               }else{
                   var error_msg = "<tr><td style='padding:15px;' colspan='4' align='center'><strong style='color:red;font-size:18px;'>未找到可售票型</strong></td></tr>";
                   $("#tro").html(error_msg); 
               }
            }
        }
    });
}
//切换日期、场次时刷新
function empty_cart_ticket(){
    //刷新购物车
    $("#cart").empty();
    $('#tro').empty();
}

//身份证校验
function check_idcard(code) {
  /*
  if(!/^\d{6}(17|18|19|20)?\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|Xx)$/i.test(code)){
    console.log(code);
    return false;
  }*/
  code = code.split('');
  //∑(ai×Wi)(mod 11)
  //加权因子
  var factor = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 ];
  //校验位
  var parity = [ 1, 0, 'x', 9, 8, 7, 6, 5, 4, 3, 2, 'X' ];
  var sum = 0;
  var ai = 0;
  var wi = 0;
  for (var i = 0; i < 17; i++)
  {
      ai = code[i];
      wi = factor[i];
      sum += ai * wi;
  }
  var last = parity[sum % 11];
  if(parity[sum % 11] != code[17]){
    console.log(code+'2');
    return false;
  }else{
    return true;
  }
}
/**
 * @Company  承德乐游宝软件开发有限公司
 * @Author   zhoujing      <zhoujing@leubao.com>
 * @DateTime 2017-12-22
 * @param    {string}      code                  身份证号码
 * @param    {objct}      area                  允许区域
 * @return   {[type]}                            [description]
 */
function check_idcard_area(code,area,actid) {
    var length = 0,retu = '';
    for (var i = 0; i < area.length; i++) {
        length = area[i].length;
        var site = code.substr(0,length);
        //var log = 'length:'+length+'code:'+code+'site:'+site+'item:'+area[i]+'area:'+area.length;
        //console.log(log);
        //console.log(site === area[i]);
        if(site === area[i]){
          //发送到服务器验证 TODO
          $.ajax({
            url: 'index.php?g=Home&m=Check&a=public_check_idcard',
            type: 'GET',
            dataType: 'JSON',
            async:false,
            data: {'ta': '31','idcard': code, 'actid': actid},
            error: function(){
              layer.msg('服务器请求超时，请检查网络...');
            },
            success:function(rdata){
              if(rdata.status){
                retu = true;
              }else{
                retu = false;
              }
            }
          });
        }
    }
    if(retu){
      return true;
    }else{
      return false;
    }
}
/**
 * @Company  承德乐游宝软件开发有限公司
 * @Author   zhoujing      <zhoujing@leubao.com>
 * @DateTime 2017-12-23
 * @param    {array}      arr                   验证数组
 * @return   存在重复返回true  不存在false
 */
function is_array_unique(arr){
  return /(\x0f[^\x0f]+)\x0f[\s\S]*\1/.test("\x0f"+arr.join("\x0f\x0f") +"\x0f");
}