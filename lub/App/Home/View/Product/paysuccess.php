<?php if (!defined('LUB_VERSION')) exit(); ?>
<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<Managetemplate file="Home/Public/cssjs"/>
<title>支付成功  - by LubTMP</title>
</head>

<body>
<div class="container">
<Managetemplate file="Home/Public/menu"/>
<!--内容主体区域 start-->
<div class="main row">
  <div class="row">
    <!--导游信息START-->
    <div class="col-sm-6">
      <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">导游信息</h3>
        </div>
        <div class="panel-body">
            <table class="table">
              <thead>
                <tr>
                  <th>导游姓名</th>
                  <th>导游手机号</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>张三</td>
                  <td>13422233222</td>
                </tr>
              </tbody>
            </table>
        </div>
      </div>
    </div>
    <!--导游信息END-->
    <!--联系人信息START-->
    <div class="col-sm-6">
      <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">联系人信息</h3>
        </div>
        <div class="panel-body">
            <table class="table">
              <thead>
                <tr>
                  <th>联系人姓名</th>
                  <th>联系人电话</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>李然</td>
                  <td>13422233222</td>
                </tr>
              </tbody>
            </table>
        </div>
      </div>
    </div>
    <!--联系人END-->   
    <div class="col-sm-12">
      <div class="panel panel-primary">
        <div class="panel-heading">
          <h3 class="panel-title">订单信息</h3>
        </div>
        <div class="panel-body">
            <table class="table">
              <thead>
                <tr>
                  <th>编号</th>
                  <th>票型</th>
                  <th>单价</th>
                  <th>数量</th>
                  <th>小计</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>A区双号</td>
                  <td>258.00</td>
                  <td>1</td>
                  <td>258.00</td>
                </tr>
                <tr>
                  <td>2</td>
                  <td>B区双号</td>
                  <td>258.00</td>
                  <td>1</td>
                  <td>258.00</td>
                </tr>                
              </tbody>
              <tr>
                <td colspan="3"></td>
                <td><strong>总价</strong></td>
                <td>516.00</td>
              </tr>
              <tr>
                <td></td>
                <td colspan="2"><button type="button" class="btn btn-lg btn-primary">打印纸质票</button></td>
                <td colspan="2"><button type="button" class="btn btn-lg btn-info">发送电子票</button></td>
              </tr>              
            </table>
        </div>
      </div>
    </div>    
  </div>
</div>
<div> 
  <!--内容主体区域 end--> 
  <!--页脚-->
  <Managetemplate file="Home/Public/footer"/>
  <!--页脚--> 
</div>
</body>
</html>
