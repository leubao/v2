<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
        <title>系统警告</title>
        <link rel="stylesheet" href="/static/web/css/weui.min.css"/>
    </head>
    <body>
        <div class="weui_msg">
            <div class="weui_icon_area"><i class="weui_icon_msg weui_icon_waiting"></i></div>
            <div class="weui_text_area">
                <h2 class="weui_msg_title"><?php echo($error); ?></h2>
                <p class="weui_msg_desc"><?php echo($error); ?></p>
            </div>
            <div class="weui_opr_area">
                <p class="weui_btn_area">
                    <a href="javascript:;" id="closeWindow" class="weui_btn weui_btn_primary">确定</a>
                </p>
            </div>
           
        </div>

<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js" charset="utf-8"></script>
<script type="text/javascript">
//关闭当前窗口
document.querySelector('#closeWindow').onclick = function () {
    wx.closeWindow();
  };
</script>
    </body>
</html>
