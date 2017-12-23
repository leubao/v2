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
//普通加载价格
function getprice(event, treeId, treeNode){
    var data = 'info={"area":'+treeNode.id+',"type":'+treeNode.type+',"plan":'+treeNode.plan+',"method":"general"}',
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
//活动加载价格  根据销售计划加载价格
function getActivtyPrice(plan,actid,type,seale) {
  var postData = 'info={"type":'+type+',"seale":'+seale+',"actid":'+actid+',"plan":'+plan+',"method":"activity"}',
      content = '',
      url = 'index.php?g=Item&m=Work&a=getprice';
    //刷新购物车
    $(this).bjuiajax('refreshDiv', 'promotions-price-select');
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
            if(rdata.statusCode == "200"){
               if(rdata.price != null && rdata.price != false){
                  $(rdata.price).each(function(idx,ticket){
                    content += "<tr data-id='"+ticket.id+"' data-area='"+ticket.area_id+"' data-name='"+ticket.name+"' data-discount='"+ticket.discount+"' data-price='"+ticket.price+"'><td align='center'>["+ticket.area+"]"+ticket.name+"</td><td>"+ticket.price+"</td><td>"+ticket.discount+"</td><td align='center'>"+ticket.area_nums+"</td><td align='center'>"+ticket.area_num+"</td>"
                    +"</tr>";
                  });
                  $("#promotions-price").html(content);
               }else{
                  var error_msg = "<tr><td style='padding:15px;' colspan='6' align='center'><strong style='color:red;font-size:48px;'>未找到可售票型</strong></td></tr>";
                  $("#promotions-price").html(error_msg);
               }
            }else{
                $(this).alertmsg('error','出票失败!');
            }
        }
    });
}
  /**
 * 活动加载销售计划
 * @Company  承德乐游宝软件开发有限公司
 * @Author   zhoujing      <zhoujing@leubao.com>
 * @DateTime 2017-12-21
 * @param    {string}      plantime              销售日期
 * @param    {string}      type                  活动类型
 */
function activity_plan(plantime){
    var postData = 'info={"plantime":"'+plantime+'"}',
        content = '';
    $("#promotions_plan").empty();
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
                  content += "<option value=''>+=^^=请选择销售计划=^^=+</option>";
                  $(rdata.plan.children).each(function(idx,item){
                    content += "<option data-id='"+item.id+"' value='"+item.plan+"'>"+item.name+"</option>";
                  });
               }else{
                  content += "<option value=''>+=^^=未找到可售计划=^^=+</option>";
               }
               $("#promotions_plan").append(content);
               $("#promotions_plan").selectpicker('refresh');
            }else{
                $(this).alertmsg('error','出票失败!');
            }
        }
    });
}
/** 
* 判断是否null 
* @param data 
*/
function isNull(data){ 
    return (data == "" || data == undefined || data == null) ? false : data; 
}
/**
* 分销模型
* @param  {int} price_group_full 价格分组
* @param  {int} status          分销开启
* @param  {string} vmodel      加载区域
* @param  {string} type        分销类型
* @return {[type]}                  [description]
*/
function auto_fenix(price_group,status,vmodel,type,scene = '4'){
    console.log(type);
    var l1 = '',l2 = '', l3 = '';
    if(price_group != '' || null || undefined && status == '1'){
      var content = '';
        $.ajax({
          url: "index.php?g=manage&m=index&a=public_get_group_ticket&group_id="+price_group+"&scene"+scene,
          type: 'GET',
          dataType: 'JSON',
          timeout: 1500,
          error: function(){
              layer.msg('服务器请求超时，请检查网络...');
          },
          success: function(rdata){
            if(rdata.statusCode == '200'){
              if(type == 'full'){
                  content = "<table class='table  table-bordered'><tr><td>票型名称</td><td>票面价</td><td>补贴</td></tr>";
                  $(rdata.data).each(function(idx,vo){
                    if(vo.full != null){
                        l1 = vo.full;
                    }
                    content += "<tr><td align='center'>"+vo.name+"</td><td>"+vo.price+"</td>"+
                    "<td><input type='text' name='tic["+vo.id+"]' class='form-control' size='8' value='"+l1+"'/></td></tr>";                
                  });
              }
              if(type == 'level3'){
                content = "<table class='table  table-bordered'><tr><td>票型名称</td><td>票面价</td><td>一级补贴</td><td>二级补贴</td><td>三级补贴</td></tr>";
                $(rdata.data).each(function(idx,vo){
                  if(vo.level3 != null){
                    l1 = vo.level3.l1; l2 = vo.level3.l2; l3 = vo.level3.l3;
                  }
                  content += "<tr><td align='center'>"+vo.name+"</td><td>"+vo.price+"</td>"+
                  "<td><input type='text' name='tic["+vo.id+"][]' class='form-control' size='8' value='"+l1+"'/></td>"+
                  "<td><input type='text' name='tic["+vo.id+"][]' class='form-control' size='8' value='"+l2+"'/></td>"+
                  "<td><input type='text' name='tic["+vo.id+"][]' class='form-control' size='8' value='"+l3+"'/></td></tr>";                
                });
              }
              /*智游宝*/
              if(type == 'zyb'){
                  content = "<table class='table  table-bordered'><tr><td>票型名称</td><td>票面价格</td><td>智游宝编码</td></tr>";
                  $(rdata.data).each(function(idx,vo){
                    if(vo.zyb != null){
                        l1 = vo.zyb;
                    }
                    content += "<tr><td align='center'>"+vo.name+"</td><td>"+vo.price+"</td>"+
                    "<td><input type='text' name='tic["+vo.id+"]' class='form-control' size='8' value='"+l1+"'/></td></tr>";                
                  });
              }
              content += "</table>";
            }
            $("#"+vmodel).html(content); 
          }
      });
    }
}
/**
 * 订单类发送到服务器
 * @param  {string} postData 表单数据
 * @param  {string} url      URL地址
 * @param  {string} asside   请求来源
 * 返回数据  statusCode 200|300|400 dialog true|false forwardUrl 在dialog  为true有效  refresh 要刷新的弹窗或tab框架
 */
function post_server(postData,url,asside){
  $.ajax({
    type:'POST',
    url:url,
    data:postData,
    dataType:'json',
    timeout: 3500,
    error: function(){
        layer.msg('服务器请求超时，请检查网络...');
    },
    success:function(data){
        if(data.statusCode == "200"){
            switch(asside){
                case 'payment':
                  $(this).dialog('close','payment');
                  break;
                case 'work_quick':
                  $(this).dialog('refresh', 'work_quick');
                  break;
                case 'preseat':
                  //超量排座  会关闭打印窗口
                  $(this).dialog("closeCurrent","true");
                  break;
            }
            //刷新
            if(data.dialog){
              //弹窗
              $(this).dialog('refresh', data.refresh);
              $(this).dialog({id:data.pageid, url:''+data.forwardUrl+'', title:data.title,width:data.width,height:data.height,resizable:false,maxable:false,mask:true});
            }else{
              //navtab
              $(this).navtab('refresh', data.refresh);
            }
        }else{
            $(this).alertmsg('error',data.message);
        }
    }
  });
}
/****  购物车结算 ***/

/*计算小计金额*/
function amount(num,price){
    var count = parseFloat(num * price).toFixed(2);
    return count;
}
//身份证校验
function check_idcard(code) {
  code = code.split('');
  if(!/^\d{6}(17|18|19|20)?\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|Xx)$/i.test(code)){
    return false;
  }
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
function check_idcard_area(code,area) {
    var length = 0;
    area.each(function(index,item){
      length = item.length();
      var site = code.substr(1,length);
      if(site === item){
        //发送到服务器验证 TODO 
        return true;
      }
    });
    return false;
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
