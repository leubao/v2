<!--年卡申请页面-->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>年卡办理</title>
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport" />
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <!-- 引入YDUI样式 -->
    <link rel="stylesheet" href="static/css/ydui.css" />
    <!-- 引入YDUI自适应解决方案类库 -->
    <script src="static/js/ydui.flexible.js"></script>
    <style type="text/css" media="screen">
     /*.action{margin-top: 5rem;}*/
     .action .cell-left{ width: 1.3rem;}
     /*.g-view{background: #00FF00 url(static/images/mllj2018.jpg) no-repeat fixed top;}*/
    </style>
</head>
<body>
<header class="m-navbar navbar-fixed">
    <a href="#" class="navbar-item"><i class="back-ico"></i></a>
    <div class="navbar-center"><span class="navbar-title">梦里老家演艺小镇年卡办理</span></div>
</header>
<div class="g-view">
    <div class="action">
        <div class="m-cell">
            <div class="cell-item">
                <div class="cell-left">姓  名：</div>
                <div class="cell-right"><input type="text" id="name" class="cell-input" placeholder="请输入姓名" autocomplete="off" /></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">身份证：</div>
                <div class="cell-right"><input type="text" id="card" class="cell-input" placeholder="请输入身份证号码" autocomplete="off" /></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">手机号：</div>
                <div class="cell-right"><input type="text" id="phone" class="cell-input" placeholder="请输入手机号" autocomplete="off" /></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">备 注：</div>
                <div class="cell-right"><input type="text" id="remark" class="cell-input" placeholder="备注" autocomplete="off" /></div>
            </div>
            <label class="cell-item">
                <span class="cell-left">年卡办理须知</span>
                <label class="cell-right">
                    <span class="badge badge-radius badge-danger" id="know">阅读</span>
                </label>
            </label>
            <a class="cell-item" href="tel:400-608-8889">
                <div class="cell-left"><i class="cell-icon demo-icons-tel"></i>联系客服</div>
                <div class="cell-right cell-arrow">400-608-8889</div>
            </a>
        </div>
        <div class="m-button">
            <a href="javascript:;" class="btn-block btn-primary" id="applySubmit">立即办理</a>
            <!--
            <a href="/card/confirm.html" class="btn-block btn-primary">立即确认</a>
            <a href="/card/renewal.html" class="btn-block btn-primary"立即续费</a>
            <a href="/card/succeed.html" class="btn-block btn-primary">成功</a>-->
            <a href="javascript:;" class="btn-block" id="clear">清理</a>
        </div>
    </div>
</div>
<div class="footer">
    <p class="c">云鹿票券@梦里老家演艺小镇</p>
</div>
<!-- 引入jQuery 2.0+ -->
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<!-- 引入YDUI脚本 -->
<script src="static/js/ydui.js"></script>
<script>
    !function ($) {
        var name = '', card = '', phone = '', code = '', err = '', openid = '0';
        var remark = "<h1>办理须知</h1>"+
        "<p style='text-align:left;'>"+
        "1、梦里老家演艺小镇年票为演艺小镇的出入凭证;<br/>"+
        "2、婺源地区居民可在线自助办理,也可前往景区售票处指定窗口办理;<br/>"+
        "3、常年在婺源工作,非本地身份证,可凭单位介绍信前往景区售票指定窗口办理;<br/>"+
        "4、办理费用为壹元，有效期为2018年3月3日至2019年4月3日;<br/>"+
        "5、在线自助办理成功后,凭身份证通过指定通道过闸入园,忘记携带身份证的,可在景区售票指定窗口办理临时入园凭证;<br/>"+
        "6、办理截止时间为2018年8月1日;<br/>"+
        "7、办理即为同意景区年卡管理制度;<br/>"+
        "</p>";
        YDUI.dialog.alert(remark);
        //判断会话ID是否存在，取得会话ID
        get_token();

       //判断是否存在openID
        openid = sessionStorage.getItem('openid');
        if(openid === null || openid === '0' || openid === 'undefined'){
            //console.log(openid);
            var reset = GetPostHttp('http://dp.wy-mllj.com/api.php?m=apply&a=oauth','{"url":"'+window.location.href+'"}');
            //console.log(reset);
            if(reset.code == '10003'){
                //一个微信多张年卡
                window.location.href = reset.data.auth;
            }else{
                sessionStorage.setItem('openid', reset.data.openid);
            }
        }
        console.log(sessionStorage.getItem('openid'));
        
        $('#applySubmit').on('click', function () {
            name = $('#name').val().replace(/ /g,'');
            phone = $('#phone').val().replace(/ /g,'');
            card = $('#card').val().replace(/ /g,'');
            remark = $('#remark').val();
            /*验证身份证*/
            if(name.length == 0){ err = "姓名不能为空";}
            if(!is_card(card)){return false;}
            if(!is_mobile(phone)){err = "手机号不正确";}
            if(err.length > 0){
               YDUI.dialog.toast(err+'', 'error', 1500);
               return false; 
            }

            var reset = GetPostHttp('/api.php?m=apply&a=apply',{'content':name,'card':card,'phone':phone,'remark':remark});
            if(reset.status){
                //提交成功
                sessionStorage.setItem('content',name);
                sessionStorage.setItem('card',card);
                sessionStorage.setItem('phone',phone);
                sessionStorage.setItem('remark',remark);
                //服务端存储一次数据，客户端同时存储数据
                window.location.href = '/card/confirm.html';
            }else{
                //主要判断身份证号段是否可用，以及手机验证码
                YDUI.dialog.alert(reset.msg);
            }
            //提交成功后判断是否
            
        });
        function is_card(str) {
            //身份证号码为15位或者18位，15位时全为数字，18位前17位为数字，最后一位是校验位，可能为数字或字符X
            var retu = false;
            //console.log(IdentityCodeValid(str));
            if(!IdentityCodeValid(str)){
                YDUI.dialog.alert('身份证号码输入有误');
                return false;
            }else{
                //发送请求至服务器，验证是否可用
                $.ajax({
                    url: '/api.php?m=apply&a=card',
                    dataType: 'json',
                    type: 'POST',
                    data: {'card':str},
                    async: false,
                    timeout: 1500,
                    success: function (ret) {
                        if(ret.status){
                            console.log(ret);
                            retu = true;
                        }else{
                            YDUI.dialog.alert(ret.msg);
                            retu = false;
                        }
                    }
                });
                console.log(retu);
                if(retu){
                  return true;
                }else{
                  return false;
                }
            }
        }
        function IdentityCodeValid(code) { 
            var city={11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",21:"辽宁",22:"吉林",23:"黑龙江 ",31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北 ",43:"湖南",44:"广东",45:"广西",46:"海南",50:"重庆",51:"四川",52:"贵州",53:"云南",54:"西藏 ",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外 "};
            var tip = "";
            var pass= true;

            if(!code || !/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[12])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/i.test(code)){
                tip = "身份证号格式错误";
                pass = false;
            }else if(!city[code.substr(0,2)]){
                tip = "地址编码错误";
                pass = false;
            }else{
                //18位身份证需要验证最后一位校验位
                if(code.length == 18){
                    code = code.split('');
                    //∑(ai×Wi)(mod 11)
                    //加权因子
                    var factor = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 ];
                    //校验位
                    var parity = [ 1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2 ];
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
                        tip = "校验位错误";
                        pass =false;
                    }
                }
            }
            //if(!pass) alert(tip);
            return pass;
        }
        function is_mobile(str) {
            var re = /^1\d{10}$/;
            if (re.test(str)) {
                return true;
            } else {
                return false;
            }
        }
        $('#clear').on('click', function () {
            sessionStorage.clear();
            alert('ok');
        });
        //阅读须知
        $('#know').bind("click",function(event){  
            YDUI.dialog.alert(remark);
            event.stopPropagation();    //  阻止事件冒泡  
        });
        /**
         * 封装会话token 的ajax请求包
         * @Author   zhoujing   <zhoujing@leubao.com>
         * @DateTime 2017-11-02
         * @param    {string}   urls                  请求url
         * @param    {json}     data                  请求数据
         * @param    {string}   method                请求方式
         */
        function GetPostHttp(urls,pdata,method = 'POST') {
            var reset = '';
            $.ajax({
                url: urls,
                dataType: 'json',
                type: method,
                async: false,
                data: pdata,
                timeout: 1500,
                beforeSend: function(request) {
                    request.setRequestHeader('authorization', get_token());
                },
                success: function (ret) {
                    //console.log(ret);//console.log(ret.data);
                    if(ret.status){
                        $reset = ret;
                    }else{
                        YDUI.dialog.toast('服务器响应异常...', 'error', 1500);
                        $reset = false;
                    }
                },
                error: function (ret) {
                    YDUI.dialog.toast('服务器请求超时...', 'error', 1500);
                    $reset = false;
                }
            });
            return $reset;
        }
        /**
         * 获取会话token
         * @Author   zhoujing   <zhoujing@leubao.com>
         * @DateTime 2017-11-02
         * @return   {string}   会话token
         */
        function get_token() {
            var token = sessionStorage.getItem('token');
            console.log(token);
            if(token === null || token === ''){
                $.ajax({
                    url: 'http://dp.wy-mllj.com/api.php?m=apply',
                    dataType: 'json',
                    async: true,
                    success: function(ret) {
                        if(ret.status){
                            sessionStorage.setItem('token', ret.data.token);
                            //console.log(ret.data.openid);
                            sessionStorage.setItem('openid', ret.data.openid);
                            //获取办理区域等参数
                            return true;
                        }
                    }
                });
            }else{
                return token;
            }
        }
        /**获取地址栏参数**/
        /**
         * @company  承德乐游宝软件开发有限公司
         * @Author   zhoujing      <zhoujing@leubao.com>
         * @DateTime 2017-11-04
         * @param    {string}
         */
        function GetQueryString(name)
        {
            var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
            var r = window.location.search.substr(1).match(reg);
            if(r!=null)return  unescape(r[2]); return null;
        }
    }(jQuery);
</script>
</body>
</html>