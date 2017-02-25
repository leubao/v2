<Managetemplate file="Wechat/Public/header"/>
<style type="text/css" media="screen">

<if condition="$uinfo['status'] eq '1'">
  .page{background-color: #ff6600}
  <else/>
  .page{background-color: #9ea7b4}
  </if>
</style>
<div class="page">
  	<div class="content">
        <div class="list-block media-list inset">
        <ul>
          <li>
            <a href="#" class="item-link item-content">
              <div class="item-media"><img src="{$result.headimgurl}" style='width: 2.2rem;'></div>
              <div class="item-inner">
                <div class="item-title-row">
                  <div class="item-title">{$uinfo['nickname']}</div>
                </div>
                <div class="item-subtitle">欢迎你回来!</div>
              </div>
            </a>
          </li>
        </ul>
      </div>  		
      <div class="card">
        <div class="card-header">体验项目:</div>
        <div class="card-content">
          <div class="list-block">
            <ul>
              <li class="item-content">
                <div class="item-inner">
                  <div class="item-title-row" <if condition="$uinfo['status'] eq '4'">style="text-decoration:line-through;"</if>>
                    <div class="item-title" style="text-align: center;font-size: 60px">{$uinfo['ticket']}</div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
        <div class="card-footer">
          <span>状态:<if condition="$uinfo['status'] eq '1'">未使用<else />已使用</if></span>
          <span>{$uinfo['uptime']|date="Y-m-d H:s",###}</span>
        </div>
      </div>            
       <div class="card">
            <div class="card-header">使用说明</div>
            <div class="card-content">
                <div class="card-content-inner">
                    <ul>
                        <li>1. 该体验劵未免费赠予，领取人可凭此券免费体验对应项目一次;</li>
                        <li>2. 核销密码请由检票人员输入;</li>
                        <li>3. 活动最终解释权归承德海潮游乐场所有!</li>
                    </ul>
                </div>
            </div>
        </div>
        <if condition="$uinfo['status'] eq '1'">
  		<div class="content-block">
	      <p><a href="#" id="led" class="button button-big button-fill button-success external">立即使用</a></p>
	    </div>
      </if>
  	</div>
</div>
<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript"> 
    $(document).on('click','#led', function () {
        $.prompt('请输入核销密码', '门票核销',function (value) {
          if(value !== '8888'){
            $.toast('核销密码有误');
          }else{
            $.get('<?php echo U('Wechat/index/check',array('openid'=>$uinfo['openid'],'pid'=>$ginfo['pid']));?>',function(data){
              if(data.statusCode == '200'){
                $.toast('核销成功');
              }else{
                $.toast('核销失败');
              }
            })
            window.location.reload();
          }
        });
    });
</script>
</body>
</html>