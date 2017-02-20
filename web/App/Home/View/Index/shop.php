<include file="Index:header" />

<style type="text/css">
  .m-box-goods {
  position: relative;
  z-index:3;
}
.m-box-goods .hd {
  zoom: 1;
  position: relative;
  background-color: #f6f6f6;
  border-bottom: 1px solid #eaeceb;
}
.m-box-goods .hd:after {
  content: "";
  display: block;
  clear: both;
}
.m-box-goods .hd .m-crm {
  float: left;
}
.m-box-goods .hd .ops {
  float: right;
}
.m-box-goods .hd .u-btn {
  height: 50px;
  line-height: 50px;
  padding: 0 20px 0 18px;
  border-left: 1px solid #eaeceb;
  background-color: transparent;
  color: #666;
  font-size: 12px;
  float: left;
}
.m-box-goods .hd .u-btn .txt {
  display: inline-block;
  *display: inline;
  *zoom: 1;
}
.m-box-goods .hd .u-btn:hover {
  color: #e71446;
  background-color: transparent;
}
.m-box-goods .hd .ops {
  float: right;
}
.m-box .subitem ul li {
  position: relative;
  z-index: 10;
  border-bottom: 1px dashed #cfcfcf;
  height: 78px;
}
.m-box .subitem ul li a {
  display: block;
  padding: 15px 0 10px 80px;
  height: 53px;
}
.m-box .subitem ul li a:hover {
  background-color: #f9f9f9;
}
.m-box .subitem ul li strong {
  font-weight: normal;
}
.m-box .subitem ul li.s-ion1 a {
  background: url(http://new.leubao.com/static/web/img/i-01.png) no-repeat scroll 15px 10px;
}
.m-box .subitem ul li.s-ion2 a {
  background: url(http://new.leubao.com/static/web/img/i-01.png) no-repeat scroll 15px -78px;
}
.m-box .subitem ul li.s-ion3 {
  border-bottom: 1px solid #e5e5e5;
}
.m-box .subitem ul li.s-ion3 a {
  background: url(http://new.leubao.com/static/web/img/i-01.png) no-repeat scroll 15px -167px;
}
.m-box .subitem ul li .s-t1 {
  font-family: "Microsoft YaHei";
  font-size: 16px;
  color: #555;
  line-height: 22px;
  padding-bottom: 2px;
}
.m-box .subitem ul li .s-t2 {
  font-size: 12px;
  color: #999;
  line-height: 16px;
}
.m-box .subitem ul li .s-ewm {
  position: absolute;
  left: -124px;
  top: 0;
  width: 123px;
  height: 168px;
  text-align: center;
  color: #323232;
  font-family: "Microsoft YaHei";
  line-height: 18px;
  display: none;
  background: url(http://new.leubao.com/static/web/img/i-02.png) no-repeat scroll 0 0;
}
.m-box .subitem ul li .s-ewm img {
  display: block;
  margin: 5px auto 10px;
}
.m-box .subitem ul li .f14 {
  font-size: 14px;
}

/* 项目模块 */
.m-goods {
  float: left;
  width: 620px;
  padding: 30px 20px 30px 30px;
  position: relative;
}
.m-goods .tt {
  position: relative;
  top: -8px;
  font-weight: bold;
  font-size: 18px;
  line-height: 2em;
  color: #000;
  padding-bottom: 10px;
}
.m-goods .stt {
  color: #999;
  font-weight: normal;
  font-size: 14px;
  line-height: 20px;
  padding: 16px 0;
  margin-top: -15px;
  vertical-align: top;
}
.m-goods .stt .quotl,
.m-goods .stt .quotr {
  display: inline-block;
  *display: inline;
  *zoom: 1;
  vertical-align: top;
  background: url(http://new.leubao.com/static/web/img/ultimate-sprites.png) no-repeat 9999px 9999px;
  width: 20px;
  height: 20px;
}
.m-goods .stt .quotl {
  background-position: -94px -700px;
  margin-right: 5px;
}
.m-goods .stt .quotr {
  background-position: -126px -700px;
  margin-left: 5px;
}
/* 产品模块 */
.m-product {
  position: relative;
  z-index: 1;
  height: 100%;
  zoom: 1;
}
.m-product .m-choose {
  padding: 14px 0 0 0;
}
.m-product .m-choose-date {
  padding-top: 20px;
}
.m-product .m-choose-1 {
  padding-top: 20px;
}
.m-product .m-cart {
  padding-top: 15px;
}
.m-product .m-problem {
  margin-top: 20px;
}
.m-product .m-soldout {
  margin-top: -1px;
}
.m-product .m-qrcode {
  position: absolute;
  z-index: 3;
  right: 0;
  bottom: 0;
  zoom: 1;
}
.m-product-1 {
  padding-bottom: 50px;
}
.m-product-1 .m-cart .tt {
  display: none;
}
.m-product-1 .m-cart .tt,
.m-product-1 .m-cart .ct {
  height: 0;
  overflow: hidden;
  visibility: hidden;
}
.m-product-1 .m-qrcode {
  bottom: 50px;
}
.m-product-2 {
  padding-bottom: 50px;
}
.m-product-2 .m-qrcode {
  bottom: 0;
}
/* 选择模块 */
.m-choose {
  zoom: 1;
  /* 加载中 */
}
.m-choose:after {
  content: "";
  display: block;
  clear: both;
}
.m-choose .tt,
.m-choose .ct {
  float: left;
}
.m-choose .tt {
  line-height: 24px;
  font-size: 12px;
  font-weight: normal;
  color: #999;
  padding-top: 6px;
  width: 60px;
  top: 0;
}
.m-choose .ct {
  width: 560px;
}
.m-choose .lst {
  zoom: 1;
  padding: 0;
}
.m-choose .lst:after {
  content: "";
  display: block;
  clear: both;
}
.m-choose .lst,
.m-choose .lst a {
  color: #222;
}
.m-choose .lst .itm {
  float: left;
  margin: 0 0 5px 6px;
  border: 1px solid #eaeceb;
  display: inline;
  overflow: hidden;
  background-color: #fff;
  /* 手机专享 */
  /* 暂时无货 */
  /* 查看更多 */
}
.m-choose .lst .itm a {
  display: block;
  min-width: 122px;
  _width: 122px;
  line-height: 24px;
  padding: 4px;
  text-align: center;

  border: 1px solid #fff;
}
.m-choose .lst .itm-sel {
  border-color: #ff4a00;
  background: #ffffff url(http://new.leubao.com/static/web/img/m-choose-sel.png) no-repeat right bottom;
}
.m-choose .lst .itm-sel a {
  border-color: #ff4a00;
}
.m-choose .lst .itm-mobile {
  background-color: #fdf7f5;
  border: 1px dashed #c7c7c7;
}
.m-choose .lst .itm-mobile a {
  color: #bfbfbf;
  cursor: default;
}
.m-choose .lst .itm-mobile .price {
  padding-left: 16px;
  background: url(http://new.leubao.com/static/web/img/ultimate-sprites.png) no-repeat -180px -62px;
}
.m-choose .lst .itm-oos {
  background-color: #f6f6f6;
  border: 1px dashed #c7c7c7;
}
.m-choose .lst .itm-oos a {
  color: #bfbfbf;
  cursor: default;
}
.m-choose .lst .itm-more {
  background: #ffffff url(http://new.leubao.com/static/web/img/m-choose-more.png) no-repeat 82px 16px;
}
.m-choose .lst .itm-more a {
  height: 24px;
  line-height: 24px;
}
.m-choose .lst .itm-more a span {
  display: inline-block;
  *display: inline;
  *zoom: 1;
}
.m-choose .lst .itm-more-sel {
  border: 1px solid #eaeceb;
  background-position: 82px -34px;
}
.m-choose .lst .itm-more-sel a {
  border: 1px solid #fff;
}
.m-choose .lst-dis .itm {
  background-color: #f5f5f5;
}
.m-choose .loading {
  clear: both;
  display: block;
  height: 36px;
  line-height: 36px;
  padding-left: 6px;
}
.m-choose .tips {
  position: absolute;
  line-height: 24px;
  padding: 2px 16px;
  display: none;
  /* 暂时无货 */
}
.m-choose .tips.z-show {
  display: block;
}
.m-choose .tips-warn {
  margin: 10px 0 0 6px;
  line-height: 24px;
  padding: 2px 16px;
  background-color: #fff1dc;
  border: 1px solid #ffdeb3;
  color: #fe7237;
}
.m-choose .tips-warn.z-hide {
  display: none;
}
.m-choose .tips-warn.z-show {
  display: block;
}
.m-choose .tips-oos {
  background-color: #fff1dc;
  border: 1px solid #ffdeb3;
  color: #fe7237;
}
.m-choose .tips-oos .ico {
  position: absolute;
  left: 30px;
  top: -6px;
  width: 10px;
  height: 6px;
  overflow: hidden;
  background: url(http://new.leubao.com/static/web/img/ultimate-sprites.png) no-repeat -96px -64px;
}
/* 数量模块 */
.m-nums {
  border: 1px solid #eaeceb;
  zoom: 1;
}
.m-nums:after {
  content: "";
  display: block;
  clear: both;
}
.m-nums .btn,
.m-nums .ipt {
  float: left;
}
.m-nums .btn {
  width: 28px;
  height: 28px;
  line-height: 9999px;
  overflow: hidden;
  background: url(http://new.leubao.com/static/web/img/ultimate-sprites.png) no-repeat 9999px 9999px;
  background-color: #fafafa;
}
.m-nums .btn:active {
  background-color: #e1e1e1;
}
.m-nums .btn-low {
  background-position: -128px -96px;
}
.m-nums .btn-add {
  background-position: -160px -96px;
}
.m-nums .ipt {
  width: 34px;
  height: 28px;
  line-height: 28px;
  border: 1px solid #eaeceb;
  border-top: none;
  border-bottom: none;
  color: #333;
  text-align: center;
}
/* 购物车模块 */
.m-cart {
  zoom: 1;
  padding-bottom: 0;
  /* 提示 */
}
.m-cart:after {
  content: "";
  display: block;
  clear: both;
}
.m-cart .tt,
.m-cart .ct {
  visibility: visible;
}
.m-cart .tt {
  display: block;
  float: left;
  line-height: 24px;
  font-size: 12px;
  font-weight: normal;
  color: #999;
  width: 64px;
  padding-top: 8px;
}
.m-cart .ct {
  position: relative;
  float: right;
  width: 554px;
  zoom: 1;
}
.m-cart .ct:after {
  content: "";
  display: block;
  clear: both;
}
.m-cart .lst {
  width: 476px;
  line-height: 24px;
  font-size: 14px;
  color: #333;
}
.m-cart .lst .itm {
  position: relative;
  margin-bottom: 15px;
  border: 1px solid #eaeceb;
  zoom: 1;
}
.m-cart .lst .itm:after {
  content: "";
  display: block;
  clear: both;
}
.m-cart .lst .itm .txt {
  float: left;
  padding: 8px 10px;
}
.m-cart .lst .itm .txt-datetime {
  width: 174px;
}
.m-cart .lst .itm .txt-price {
  width: 96px;
  overflow: hidden;
  word-wrap: normal;
  white-space: nowrap;
  text-overflow: ellipsis;
}
.m-cart .lst .itm .m-nums {
  position: relative;
  z-index: 2;
  float: left;
  margin-top: 5px;
  margin-left: 60px;
}
.m-cart .tips {
  /* 库存紧张 */
}
.m-cart .tips-stock {
  position: absolute;
  z-index: 1;
  right: -3px;
  top: 0;
  width: 60px;
  height: 100%;
  background: url(http://new.leubao.com/static/web/img/tips-stock.png) repeat-y 0 0;
}
.m-cart .tips-stock strong {
  position: absolute;
  z-index: 1;
  left: 50%;
  top: 50%;
  margin-left: -15px;
  margin-top: -16px;
  width: 32px;
  height: 32px;
  line-height: 16px;
  font-size: 12px;
  font-weight: normal;
  letter-spacing: 3px;
}
.m-cart .btn-del {
  position: absolute;
  z-index: 1;
  right: -75px;
  top: 12px;
  width: 60px;
  line-height: 16px;
}
.m-cart .btn-del i {
  float: left;
  width: 16px;
  height: 16px;
  margin: 0 5px 0 0;
  background: url(http://new.leubao.com/static/web/img/ultimate-sprites.png) no-repeat -96px 0;
}
.m-cart .ops {
  padding: 0 0 0 66px;
}
.m-cart .ops .u-btn {
  display: inline-block;
  *display: inline;
  *zoom: 1;
  width: 186px;
  margin-right: 15px;
}
/* 二维码 */
.m-qrcode .tt {
  position: relative;
  z-index: 1;
  top: 0;
  margin: 0;
  font-size: 12px;
  font-weight: normal;
  padding: 4px 0 0 14px;
  width: 128px;
  height: 34px;
  line-height: 16px;
  color: #fff;
  background: url(http://new.leubao.com/static/web/img/ultimate-sprites.png) no-repeat 0 -192px;
  cursor: pointer;
  overflow: hidden;
}
.m-qrcode .tt em {
  display: block;
  color: #fff;
  font: normal 12px/16px "宋体";
}
.m-qrcode .ct {
  position: absolute;
  z-index: 0;
  left: 0;
  bottom: 35px;
  width: 120px;
  height: 120px;
  padding: 10px;
  border: 1px solid #eaeceb;
  background-color: #fff;
  border-bottom: none;
  display: none;
}
.m-qrcode .ct img {
  display: block;
  width: 120px;
  height: 120px;
}
.m-qrcode.z-show .tt {
  background-position: 0 -256px;
}
.m-qrcode.z-show .ct {
  display: block;
}
/* 票务总代 */
.m-agent {
  padding: 16px 16px 16px 20px;
  vertical-align: middle;
  zoom: 1;
}
.m-agent:after {
  content: "";
  display: block;
  clear: both;
}
.m-agent .tt,
.m-agent .ct {
  display: inline-block;
  *display: inline;
  *zoom: 1;
  vertical-align: middle;
}
.m-agent .tt {
  margin-right: 0px;
}
.m-agent .tt a,
.m-agent .tt a img {
  display: block;
}
.m-agent .ct {
  line-height: 20px;
}
.m-agent .ct .itm {
  display: block;
  color: #999;
}
/* 侧栏容器 */
.m-sdbox {
  border-top: 1px dotted #ccc;
  margin: 0 20px;
  padding: 15px 0;
  font-size: 12px;
  line-height: 2;
  position: relative;
  z-index: 3;
}
.m-sdbox .tt {
  font-weight: normal;
  font-size: 12px;
  line-height: 2;
  color: #262626;
}
.m-sdbox .ct {
  zoom: 1;
}
.m-sdbox .ct:after {
  content: "";
  display: block;
  clear: both;
}
.m-sdbox .ct,
.m-sdbox .ct a {
  color: #909090;
}
.m-sdbox .ct a:hover {
  color: #909090;
  text-decoration: underline;
  background-color: transparent;
}
.m-sdbox .u-btn {
  float: left;
  background-color: transparent;
  height: auto;
  font-size: 12px;
  line-height: 2;
  /* 日历 */
  /* 交通路线 */
  /* 查看座位图 */
  /* 选座 */
  /* 超级票 */
  /* 电子钱包 */
  /* 电子票 */
  /* 返积分 */
  /* 分期付款 */
  /* 上门自取 */
  /* 快递 */
  /* 先付先抢 */
  /* 模拟选座 */
  /* 自助换票 */
}
.m-sdbox .u-btn i {
  float: left;
  line-height: 9999px;
  width: 16px;
  height: 16px;
  margin: 4px 4px 0 0;
  overflow: hidden;
  background: url(http://new.leubao.com/static/web/img/ultimate-sprites.png) no-repeat 9999px 9999px;
}
.m-sdbox .u-btn-cal i {
  background-position: 0 -30px;
  width: 14px;
  height: 14px;
}
.m-sdbox .u-btn-traffic i {
  background-position: 2px -62px;
}
.m-sdbox .u-btn-seatmap i {
  width: 22px;
  background-position: -32px -30px;
}
.m-sdbox .u-btn-choose i {
  background-position: -32px -62px;
}
.m-sdbox .u-btn-super i {
  background-position: -64px -30px;
}
.m-sdbox .u-btn-wallet i {
  background-position: -64px -62px;
}
.m-sdbox .u-btn-etkt i {
  background-position: -158px -382px;
}
.m-sdbox .u-btn-credit i {
  background-position: -158px -414px;
}
.m-sdbox .u-btn-stage i {
  background-position: -160px -446px;
}
.m-sdbox .u-btn-selftake i {
  background-position: -126px -62px;
}
.m-sdbox .u-btn-express i {
  background-position: -126px -638px;
}
.m-sdbox .u-btn-payrob i {
  background-position: -128px -382px;
}
.m-sdbox .u-btn-simseat i {
  background-position: -127px -414px;
}
.m-sdbox .u-btn-selftkt i {
  background-position: -127px -445px;
}
/* 侧栏容器2 */
.m-sdbox2 .hd {
  height: 44px;
  background-color: #f6f6f6;
  border-top: 1px solid #e5e5e5;
  border-bottom: 1px solid #e5e5e5;
  overflow: hidden;
}
.m-sdbox2 .hd .tt {
  margin: 10px 20px 0 20px;
  padding: 0 10px;
  border-left: 2px solid #ff4a00;
  font-weight: normal;
  font-size: 14px;
  line-height: 24px;
}
.m-sdbox2-first .hd {
  border-top: none;
}
/* 演出时间 */
.m-showtime {
  zoom: 1;
}
.m-showtime:after {
  content: "";
  display: block;
  clear: both;
}
.m-showtime .txt {
  float: left;
  margin-right: 5px;
}
/* 模块容器 */
.m-box {
  background-color: #fff;
  /*border: 1px solid #eaeceb;*/
  margin-bottom: 20px;
  /* 2015-04-07  GuoJinjin 今天玩什么入口*/
}
.m-box-col2 {
  background-image: url(http://new.leubao.com/static/web/img/m-box-line.png);
  background-repeat: repeat-y;
  background-position: 740px 0;
  zoom: 1;
}
.m-box-col2 .mn {
  position: relative;
  z-index:0;
  float: left;
  width: 720px;
  zoom: 1;
}
.m-box-col2 .mn:after {
  content: "";
  display: block;
  clear: both;
}
.m-box-col2 .sd {
  position: relative;
  z-index: 1;
  float: right;
  width: 220px;
  zoom: 1;
}
.m-box-col2 .sd:after {
  content: "";
  display: block;
  clear: both;
}
.m-box-col2:after {
  content: "";
  display: block;
  clear: both;
}
/* 侧栏选项卡 */
.m-mantab {
  position: relative;
}
.m-mantab .itm {
  width: 72px;
  height: 24px;
}
.m-mantab .u-btn {
  text-align: left;
  width: 72px;
  margin: -1px;
  border: 1px solid #fff;
}
.m-mantab .layer {
  position: absolute;
  z-index: 0;
  left: 0;
  top: 24px;
  width: 226px;
  margin-left: -1px;
  border: 1px solid #e0e0e0;
  background-color: #f6f6f6;
  display: none;
}
.m-mantab .layer a {
  color: #2f97b4;
}
.m-mantab .layer a:hover {
  color: #e51a45;
  text-decoration: none;
}
.m-mantab .layer .hd {
  border: 0;
}
.m-mantab .layer .hd .btn-close {
  position: absolute;
  right: 4px;
  top: 4px;
  width: 10px;
  height: 10px;
  line-height: 9999px;
  overflow: hidden;
  background: url(http://new.leubao.com/static/web/img/ultimate-sprites.png) no-repeat -64px -704px;
}
.m-mantab .layer .hd .btn-close:hover {
  opacity: .8;
  filter: alpha(opacity=80);
}
.m-mantab .layer .bd {
  padding: 14px 25px 10px 14px;
  color: #999;
  line-height: 18px;
}
.m-mantab .layer .ft {
  zoom: 1;
  padding: 0 14px 10px 14px;
}
.m-mantab .layer .ft:after {
  content: "";
  display: block;
  clear: both;
}
.m-mantab .layer .ft .lnk {
  float: left;
}
.m-mantab .layer .ft .btn-close {
  float: right;
  color: #e41b45;
}
.m-mantab .layer .ft .btn-close:hover {
  color: #e9496a;
}
.m-mantab .z-crt {
  position: relative;
  z-index: 1;
}
.m-mantab .z-crt .u-btn {
  position: absolute;
  left: 0;
  top: 0;
  z-index: 1;
  width: 74px;
  height: 25px;
  margin: -1px;
}
.m-mantab .z-crt .u-btn,
.m-mantab .z-crt .u-btn:hover {
  border: 1px solid #e0e0e0;
  border-bottom: 0;
  background-color: #f6f6f6;
  text-decoration: none;
}
.m-mantab .z-crt .layer {
  display: block;
}

.m-qrcode{
    margin: 0 20px;
    padding: 15px 0;
    font-size: 12px;
    line-height: 2;
    position: relative;
    z-index: 3;
}

</style>
<div class="sui-container mt10">
  <div class="sui-steps steps-large steps-auto">
    <div class="wrap">
      <div class="current">
        <label><span class="round">1</span><span>第一步 选择观演日期</span></label><i class="triangle-right-bg"></i><i class="triangle-right"></i>
      </div>
    </div>
    <div class="wrap">
      <div class="todo">
        <label><span class="round">2</span><span>第二步 填写与核对订单</span></label><i class="triangle-right-bg"></i><i class="triangle-right"></i>
      </div>
    </div>
    <div class="wrap">
      <div class="todo">
        <label><span class="round">3</span><span>第三步 订单支付</span></label><i class="triangle-right-bg"></i><i class="triangle-right"></i>
      </div>
    </div>
    <div class="wrap">
      <div class="todo">
        <label><span class="round">3</span><span>第四步 完成</span></label><i class="triangle-right-bg"></i><i class="triangle-right"></i>
      </div>
    </div>
  </div>
  <div class="m-box m-box-col2 m-box-goods" id="projectInfo">
        <div class="mn">

          
          <!-- 项目模块 begin -->
          <div class="m-goods">

            <!-- 产品模块 begin -->
            <div class="m-product m-product-2-m-product-1 j_goodsDetails">
                        

              <!-- 选择日期模块 begin -->
              <div class="m-choose m-choose-date  " data-col="4">
                <h3 class="tt">演出时间：</h3>
                <div class="ct">
                  <ul class="lst " id="planlist">
                  <volist name="plan" id="vo">
                    <li class=" <if condition="$i eq 1">itm-sel</if> itm j_more" data-plid="{$vo.id}" data-pltime="{$vo.title}" data-buycount="20" data-pid="{$vo.pid}"><a href="javascript:;">{$vo.title}</a></li>
                  </volist>
                  </ul>
                </div>
              </div>
              <!-- 选择日期模块 end -->

              <!-- 选择票价模块 begin -->
              <div class="m-choose m-choose-price " data-col="4" data-performid="8764073">
                <h3 class="tt">选择票价：</h3>
                <div class="ct">
                  <ul class="lst" id="priceList">
                  </ul>
          <div class="tips-warn z-hide" id="warnXiangou"><span class="txt"></span></div>
          <div class="tips tips-oos" style="left: 90px; top: 346px;"><span class="ico"></span><span class="txt">暂时无货，登录试试运气~</span></div>
                </div>
              </div>
              <!-- 选择票价模块 end -->
                       
              <!-- 购物车模块 begin -->
              <div class="m-cart  ">
                <h3 class="tt" style="">您选择了：</h3>
                <div class="ct" style="">
                  <ul class="lst" id="cartList">
                  </ul>
                </div>
                <div class="ops">
                  <a href="#" class="sui-btn google_btn btn-danger" id="btnbuy">立即购买</a>
                </div>
              </div>
              <!-- 购物车模块 end -->                
            </div>
            <!-- 产品模块 end -->
          </div>
          <!-- 项目模块 end -->
        </div>
        
        <!-- 侧栏 begin -->
        <div class="sd">  
          <div class="m-sdbox m-showtime">
            <h2 class="tt">演出时间</h2>
            <div class="ct">
              <span class="txt">{$proconf.pstarttime}至{$proconf.pendtime}</span>
            </div>
          </div>


          <!-- 演出场馆 begin -->
          <div class="m-sdbox m-venue">
            <h2 class="tt">演出场馆</h2>
            <div class="ct">
              <p class="txt">{$proconf.venues} </p>
            </div>
          </div>
          <!-- 演出场馆 end -->
          <div class="m-qrcode">
            <h3 class="tt"><span id="ErWeiMaTips">手机扫一扫<br>下单更快捷</span></h3>
            <p class="ct"><img original="static/images/wechat.jpg" alt="{$proconf.iname}" width="109" height="108" src="static/images/wechat.jpg" style="display: block;"></p>
          </div>
          <div class="m_heighlight_tip"></div>
        </div>
               
      
        <!-- 侧栏 end -->
        
      </div>

</div>
<script type="text/javascript" src="http://g.alicdn.com/sj/lib/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="http://g.alicdn.com/sj/dpl/1.5.1/js/sui.min.js"></script>
<script src="http://new.leubao.com/static/js/layer.js" type="text/javascript" charset="utf-8"></script>
<script>
$(document).ready(function($) {
  var PRODUCT = {$area};
  var plan = '0',
      plantime = '0',
      area = '0',
      ticket = '0',
      price = '0',
      priceid = '0',
      discount = '0',
      name = '',
      phone = '',
      card = '',
      msg = '',
      pay = '',
      crm = '',
      param = '',
      toJSONString = '',
      postData = '',
      subtotal = '0',
      remark = '';
    init();
    function init() {
        bind()
    }
    function bind() {
      bindQrcode()
    }
    //默认选择第一个场次
    var def_plan =  $("#planlist li.itm-sel");
        plan = def_plan.data('plid'),
        plantime = def_plan.data('pltime'),
        pid = def_plan.data('pid'),
        getPrice(plan);
    //选择场次
    $("#planlist li").click(function(){
      //判断是否当前选择之前是否已选择
      var lengCart = $("#cartList li").length;
      if(lengCart){
        layer.confirm('亲，你确定要取消当前选择吗？', {
          btn: ['取消','确定'] //按钮
        }, function(){}, function(){emptyCart();});
      }
      
      //检查当前被选择的元素是否已经有已选中的
      $("#planlist li").each(function(){
        if($(this).hasClass("itm-sel")){ toggle($(this))};
      });
      //为当前选择加上
      active($(this));
      //refreshNum();
      plan = $(this).data('plid');
      plantime = $(this).data('pltime');
      pid = $(this).data('pid');
      //加载价格
      getPrice(plan);
    });
    $("#btnbuy").click(function(){post_server();});
    /*加载价格*/
    function getPrice(plid){
      /*客户端交互*/
      var priceList = PRODUCT[plid],
          content = '';
      $(priceList).each(function(idx,area){
            if(area.num == '0'){
              content += "<li class='itm itm-oos'"
                +"data-area='"+area.area+"'" 
                +"data-pricename='"+area.name+"'" 
                +"data-priceid='"+area.priceid+"'"
                +"data-price='"+area.money+"'"
                +"data-taopiao='false'>"
                +"<a href='javascript:;'><span class='price'>"+area.name +"&nbsp;"+area.money+"</span><i></i></a></li>";
            }else{
              content += "<li class='itm'"
                +"data-area='"+area.area+"'" 
                +"data-pricename='"+area.name+"'" 
                +"data-priceid='"+area.priceid+"'"  
                +"data-price='"+area.money+"'"
                +"data-taopiao='false'>"
                +"<a href='javascript:;'><span class='price'>"+area.name +"&nbsp;"+area.money+"</span><i></i></a></li>";
            }
            
         });
        $("#priceList").html(content);
    }

    $(document).on("click","#priceList li",function(){
       //判断是否已经选择计划
      if(!$(this).hasClass("itm-oos")){
        if(plan != 0){
          //检查当前被选择的元素是否已经有已选中的
          $("#priceList li").each(function(){
            if($(this).hasClass("itm-sel")){ toggle($(this))};
          });
          area = $(this).data('area');
          pricename = $(this).data('pricename');
          priceid = $(this).data('priceid');
          price = $(this).data('price');
          addCart(area,priceid,pricename,price);
          active($(this));
          //假如购物车
          //refreshNum();
        }else{
          layer.msg("请选择演出日期!", {icon: 2});
        }
      } 
    });
    function addCart(area,priceid,pricename,price){
      var content = '',falg = false;
      //判断是否当前选择之前是否已选择
      $("#cartList li").each(function(){
          if(priceid == $(this).data("priceid")){
              falg = true;
              return false;
          }
      });
      if(falg){
          layer.msg("亲,它已经在您的购物车中了", {icon: 2});
      }else{
        content += "<li class='itm' data-plid='"+plan+"' data-prname='"+pricename+"' data-area='"+area+"' data-price='"+price+"' data-priceid='"+priceid+"'>"
          +"<span class='txt txt-datetime '>"+""+plantime+""+"</span>"
          +"<span class='txt txt-price'>"+""+pricename+"&nbsp;"+price+""+"</span>"
          +"<span class='m-nums'><a class='btn btn-low' href='#' onclick='delNum("+priceid+")'>减</a>"
          +"<input class='ipt ipt-num' id='num-"+priceid+"' type='text' value='1'><a class='btn btn-add' href='#' onclick='addNum("+priceid+")'>加</a></span>"
          +"<span class='tips tips-stock'><strong></strong></span><a class='btn btn-del' href='#' onclick=delRow(this);><i></i>删除</a></li>";
        $('#cartList').append(content);
      }
      
     // cartList

    }
    
    //删除选中状态
    function toggle(t){
      t.toggleClass("itm-sel");
      
    }
    //选中
    function active(t){
      t.addClass("itm-sel");
    }
    //删除选中
    function deActive(t){
      t.removeClass("itm-sel");
    }
    //清空购物车切换场次
    function emptyCart(){
      $("#cartList li").remove();
    }
    /*二维码*/
    function bindQrcode() {
        var $qrcode = $('.m-qrcode');
        $qrcode.on('mouseenter',
        function() {
            $qrcode.addClass('z-show')
        });
        $qrcode.on('mouseleave',
        function() {
            $qrcode.removeClass('z-show')
        })
    }
    //表单提交
    function post_server(){
        var postData = '',
          toJSONString = '',
          length =  $("#cartList li").length;
      if(length <= 0){
          layer.msg("请选择演出时间和票价!", {icon: 2});          
          return false;
      }
      $("#cartList li").each(function(i){
          var fg = i+1 < length ? ',':' ';/*判断是否增加分割符*/
          toJSONString = toJSONString + '{"areaId":'+$(this).data("area")+',"pricename":"'+$(this).data("prname")+'","priceid":' +$(this).data("priceid")+',"price":'+parseFloat($(this).data('price')).toFixed(2)+',"num":"'+$("#num-"+$(this).data("priceid")).val()+'"}'+fg;
      });
      /*获取支付相关数据*/
      postData = 'info={"plan_id":'+plan+',"pid":'+pid+',"data":['+ toJSONString + ']}';
      /*提交到服务器*/
      $.ajax({
          type:'POST',
          url:'<?php echo U('Home/Index/cart');?>',
          data:postData,
          dataType:'json',
          timeout: 3500,
          error: function(){
            layer.msg('服务器请求超时，请检查网络...', {icon: 2});
          },
          success:function(data){
              if(data.statusCode == "200"){
                  window.location.href=data.url;
              }else{
                layer.msg("下单失败!");  
              }
          }
      });
    }
});
/*数量增加与减少*/
function addNum(priceId){
    var cnum = $("#num-"+priceId).val();//当前数量
    var num1 = parseInt(cnum)+1;
    if(check_num()){
      $("#num-"+priceId).val(num1);
    }
}
function delNum(priceId){
    var cnum = $("#num-"+priceId).val();//当前数量
    if(cnum == 1){
        layer.msg('亲，已经是最少了！');
        return false;
    }
    var num1 = parseInt(cnum)-1;
    $("#num-"+priceId).val(num1);
}
/*数量验证*/
function check_num(){
  var nums = 1,
      num = '5';
  $("#cartList li").each(function(i){
    nums += parseInt($("#num-"+$(this).data("priceid")).val());
  });
  if(nums > num){
    layer.msg('亲，一次只能买这么多了...');
    return false;
  }else{
    return true;
  }
}
//删除购物车
function delRow(rows) {
  $(rows).parent("li").remove();
}
</script>
</body>
</html>