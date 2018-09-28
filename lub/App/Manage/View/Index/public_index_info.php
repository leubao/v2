<div class="bjui-pageHeader" style="background:#FFF;">
 
</div>
<div class="bjui-pageContent">
  <div class="col-md-8">
    <h4><i class="fa fa-bank"></i> {$userInfo.item_id|itemName}</h4>
    <h4 style="margin-bottom:20px;">
      当前用户:{$userInfo.nickname}  <small>{$role_name}</small>  当前产品:{$pid|product_name}
    </h4>
    <!-- 
    <div class="widget-box widget-gray-box">
      <div class="widget-box-content widget-store-overview">
        <ul class="store-overview--column">
          <li>
            <p>今日门店订单数</p>
            <a href="">0</a>
            <span>昨日：0</span>
          </li>
          <li>
            <p>今日网店订单数</p>
            <a href="">0</a>
            <span>昨日：0 </span>
          </li>
          <li>
            <p>今日门店营业额（元）</p>
            <a href="">0.00</a>
            <span>昨日：0.00 </span>
          </li>
          <li>
            <p>今日网店营业额（元）</p>
            <a href="">0.00</a>
            <span>昨日：0.00 </span>
          </li>
          <li>
            <p>可提现余额（元）</p>
            <a href="">0.00</a>
            <i class="ico-overview-eye"></i>
          </li>
        </ul>
      </div>
    </div>
  -->
    <div class="widget-box widget-gray-box">
      <div class="widget-box-content widget-store-overview">
        <ul>
          <li>
            <p>可售场次</p>
            <a href="">{$totup.normal_plan}</a>
          </li>
          <li>
            <p>今日售出</p>
            <a href="">{$totup.today_count}</a>
          </li>
          <li>
            <p>待取票订单</p>
            <a href="">{$totup.pre_order_count}</a> | {$totup.pre_order_sum}
          </li>
          <li>
            <p>历史累计销售计划</p>
            <a href="">{$totup.plan_count}</a>
          </li>
          <li>
            <p>历史累计人数</p>
            <a href="">{$totup.people_count}</a>
          </li>
        </ul>
      </div>
    </div>
    <!---->
    <div class="widget-box widget-gray-box">
      <div class="widget-box-content widget-store-overview">
        <ul>
          <li>
            <p>今日入园</p>
            <a href="">{$totup.today_garden}</a>
          </li>
          <li>
            <p>今日待入园</p>
            <a href="">{$totup.today_pre_garden}</a>
          </li>
           <li>
            <p>昨日入园</p>
            <a href="">{$totup.yesterday}</a>
          </li>
        </ul>
      </div>
    </div>
    <div class="widget-box widget-gray-box">
      <div class="widget-box-content widget-store-overview">
        <ul>
          <li>
            <p>年卡数</p>
            <a href="">{$year.year_count}</a>
          </li>
          <li>
            <p>今日新增</p>
            <a href="">{$year.year_today}</a>
          </li>
          <li>
            <p>待支付</p>
            <a href="">{$year.year_pre}</a>
          </li>
          <li>
            <p>入园数</p>
            <a href="">{$year.year_into}</a>
          </li>
        </ul>
      </div>
    </div>
    <!--折线图
    <div style="mini-width:400px;height:350px" data-toggle="echarts" data-type="line,bar" data-url="http://ticket.leubao.com/bar.json"></div>
    <div class="widget-box recommended-apps">
      <div class="widget-box-header">
          <h3 class="widget-box-title">
              营销应用
              <div class="widget-box-opts">
                  <a href="">营销中心</a>
              </div>
          </h3>
      </div>
      <div class="widget-box-content">
          <div class="tofu-container">
              <a class="tofu tofu-green" href="">
                  <h4>多人拼团</h4>
                  <p>裂变式营销玩法</p>
              </a>
              <a class="tofu tofu-cyan" href="" target="_blank">
                  <h4>支付有礼</h4>
                  <p>客户付款后引导参与营...</p>
              </a>
              <a class="tofu tofu-blue" href="" target="_blank">
                  <h4>秒杀</h4>
                  <p>刺激客户购买，吸粉促活</p>
              </a>
              <a class="tofu tofu-purple" href="">
                  <h4>有赞小程序</h4>
                  <p>一键生成抢占先机</p>
              </a>
          </div>
      </div>
    </div>
    -->
  </div>
  <div class="col-md-4">
    <div class="service-hotline">
      电话协助：0314 - 2157 - 042
    </div>
    <!--
    <div class="ui-box widget-box widget-cms-list">
      <div class="widget-box-header">
          <h4 class="widget-box-title">帮助中心</h4>
      </div>
      <div class="widget-box-content">
        <ul>
            <li class="">
              <a href="#" target="_blank">
                有赞零售和有赞微商城有什么区别？                            
              </a>
            </li>
            <li class="">
              <a href="#" target="_blank">
                    已经订购了微商城的店铺，怎么使用有赞零售？
              </a>
            </li>
        </ul>
      </div>
    </div>
  -->
    <div class="alert alert-info" role="alert" style="margin:0 0 5px; padding:5px 15px;">
      <strong>常用软件下载</strong>
      <h5>打印插件 : <a href="http://ticket.leubao.com/d/print_32.exe" target="_blank">32位</a>　|
      <a href="http://ticket.leubao.com/d/print_64.exe" target="_blank">64位</a></h5>
      <h5>打印机驱动 : <a href="http://ticket.leubao.com/d/TOSHIBA_TEC_7.3.exe" target="_blank">东芝（TOSHIBA)B-EX4T1-GS14驱动</a></h5>
      <h5>浏览器 : <a href="http://chrome.360.cn/" target="_blank">推荐360急速浏览器</a></h5>
      <h5><a href="javascript:CheckIsInstall();">打印控件测试</a> | <a href="">打印测试</a></h5>
    </div>     
    <div class="alert alert-success" role="alert" style="margin:0 0 5px; padding:5px 15px;">
      <strong>手册及其它</strong>
      <h5>渠道版手册 : <a href="http://ticket.leubao.com/help.pdf" target="_blank">渠道版V1.0手册</a></h5>
      <h5>系统手册 : <a href="http://www.leubao.com/index.php?g=Manual" target="_blank">云鹿票务管理平台 V2.1.3 </a></h5>
      <h5>新功能预览 : <a href="http://ticket.leubao.com/" target="_blank">LUB-Tickets</a>　</h5>
      <h5>更新日志 : <a href="http://www.leubao.com/index.php?a=up_log" target="_blank">[更新日志]</a>　</h5>
      <h5>最新操作 : <a href="{:U('Manage/Index/public_action_log');}" data-id="public_action_log" data-toggle="navtab" data-title="最新操作日志">[更新日志]</a>　</h5>
      <h5>销售情况 : <a href="{$seale}" target="_blank" data-title="销售情况">{$seale}</a>　</h5>
    </div>
    <!--
    <div class="alert alert-warning" role="alert" style="margin:0 0 5px; padding:5px 15px;">
      <strong>通知公告</strong>
      <br><span class="label label-default">开发：</span>孤星</a>
      <br><span class="label label-default">测试 & 推广：</span> <a href="#">@Jack Yuan （成都锦杨）</a>
      <br><span class="label label-default">测试 & 试用：</span> <a href="#">@管书清 （小小正能量）</a>
    </div>
    -->
  </div>
</div>
<script>
function CheckIsInstall() {
  try{ 
    var LODOP=getLodop(); 
    if (LODOP.VERSION) {
       if (LODOP.CVERSION)
       alert("打印控件安装正常!\n 云控件版本:"+LODOP.CVERSION+"(本地控件版本"+LODOP.VERSION+")"); 
       else
       alert("本机已成功安装了打印控件！\n 版本号:"+LODOP.VERSION); 
    };
   }catch(err){ 
    alert("未找到打印控件,请下载安装,安装完成后请重启浏览器..."); 
   } 
}; 
</script>