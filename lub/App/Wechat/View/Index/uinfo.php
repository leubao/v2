<Managetemplate file="Wechat/Public/header"/>
<div class="page">
  <header class="bar bar-nav">
    <button class="button button-link button-nav pull-left"  ontouchend="window.history.back()">
      <span class="icon icon-left"></span>
    </button>
    <button class="button button-link button-nav pull-right"  ontouchend="window.location.href='{:U('Wechat/Index/uinfo');}'">
      <span class="icon icon-me"></span>
    </button>
    <h1 class="title">个人中心</h1>
  </header>
  <div class="content">
    <div class="list-block">
      <ul>
        <li class="item-content">
          <div class="item-media"><i class="icon icon-me"></i></div>
          <div class="item-inner">
            <div class="item-title">姓名:</div>
            <div class="item-after">{$data.nickname}</div>
          </div>
        </li>
        <if condition="$data['is_scene'] eq '3'">
        <li class="item-content">
          <div class="item-media"><i class="icon icon-browser"></i></div>
          <div class="item-inner">
            <div class="item-title">所属商户:</div>
            <div class="item-after">{$data.nickname}</div>
          </div>
        </li>
        <li class="item-content">
          <div class="item-media"><i class="icon icon-emoji"></i></div>
          <div class="item-inner">
            <div class="item-title">可用授信额:</div>
            <div class="item-after">{$data.cash}</div>
          </div>
        </li>
        <else />
        <li class="item-content">
          <div class="item-media"><i class="icon icon-emoji"></i></div>
          <div class="item-inner">
            <div class="item-title">账户余额:{$data.cash}元</div>
          </div>
        </li>
        <li class="item-content">
          <div class="item-media"><i class="icon icon-emoji"></i></div>
          <div class="item-inner">
            <div class="item-title">有效售出:{$ocount}张</div>
          </div>
        </li>
       </if>
        
      </ul>
    </div>
    <if condition="$data['is_scene'] neq '3'">
    <div class="card">
    <div class="card-content">
      <div class="list-block">
        <ul>
          <li>
            <a href="{:U('Wechat/Index/orderlist');}" class="item-link item-content external">
              <div class="item-media"><i class="icon icon-code"></i></div>
              <div class="item-inner">
                <div class="item-title">我的订单</div>
              </div>
            </a>
          </li>
          
          
          <li>
            <a href="{:U('Wechat/Index/promote');}" class="item-link item-content external">
              <div class="item-media"><i class="icon icon-share"></i></div>
              <div class="item-inner">
                <div class="item-title">销售推广</div>
              </div>
            </a>
          </li>
           <!--
          <li>
            <a href="{:U('Wechat/Index/remove');}" class="item-link item-content external">
              <div class="item-media"><i class="icon icon-remove"></i></div>
              <div class="item-inner">
                <div class="item-title">注销用户</div>
              </div>
            </a>
          </li>
         
          <li>
            <a href="{:U('Wechat/Index/reg_code');}" class="item-link item-content external">
              <div class="item-media"><i class="icon icon-share"></i></div>
              <div class="item-inner">
                <div class="item-title">推广码</div>
              </div>
            </a>
          </li>
          -->
          
        </ul>
      </div>
    </div>
    </if>
  </div>
  </div>
</div>
<Managetemplate file="Wechat/Public/footer"/>
</body>
</html>