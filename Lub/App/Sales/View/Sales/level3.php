<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Sales/Sales/level3',array('menuid'=>$menuid))}" method="post" data-toggle="validate">
<div class="bjui-pageContent tableContent">
  <div class="tabs-container" style="padding: 15px">
      <div class="tabs-left">
          <ul class="nav nav-tabs nav-stacked">
              <li  class="active"><a data-toggle="tab" href="#wtab-8" aria-expanded="false"><i class="fa fa-lastfm-square"></i>  三级分销</a></li>
          </ul>
          <div class="tabs-content">
              <div id="wtab-8" class="tab-pane active">
                <div class="panel-body">
                    <fieldset style="height:100%;">
                      <legend>三级分销</legend>
                      <div style="height:94%; overflow:hidden;">
                      <table class="table  table-bordered">
                      <tbody>
                        <tr>
                          <td width="120px">默认分组:</td>
                          <td>
                          <select name="level_group" id="level_group" class="required combox">
                            <option selected value="0">===请选择===</option>
                            <volist name="group" id="v">
                              <option data-group="{$v.price_group}" value="{$v.id}" <if condition="$vo['level_group'] eq $v['id']">selected</if>>{$v.name}</option>
                            </volist>
                          </select>
                          </td>
                        </tr>
                        <tr style="display: <if condition=" $proconf['wechat_level_3'] == '1' ">none</else>block</if>;">
                          <td width="120px">返利配置:</td>
                          <td id="wechat_level">
                            服务未开启
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">注册地址:</td>
                          <td><textarea cols="80" rows="3">{$reg}</textarea></td>
                        </tr>
                        <tr>
                          <td width="120px">提现限制:</td>
                          <td><input type="text" name="level_cash" class="form-control" size="40" value="{$vo.level_cash}" placeholder="200">
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">单场配额:</td>
                          <td><input type="text" name="level_quota" class="form-control" size="40" value="{$vo.level_quota}" placeholder="单场配额">
                          </td>
                        </tr>
                      </tbody>
                      </table>
                      </div>
                    </fieldset>
                </div>
              </div>
          </div>
      </div>
  </div>                   
</div>
  <input name="price_group_level" id="price_group_level" value="{$vo.price_group_level}" type="hidden">
  <div class="bjui-pageFooter">
    <ul>
      <li>
        <button type="button" class="btn-close" data-icon="close">取消</button>
      </li>
      <li>
        <button type="submit" class="btn-default" data-icon="save">保存</button>
      </li>
    </ul>
  </div>
</form>
<script>
  //页面渲染自动加载
  var level_3 = "{$vo['wechat_level_3']}",
      price_group_level = "{$vo['price_group_level']}";
  if(PRODUCT_CONF.wechat_level == '1'){
    auto_fenix(price_group_level,level_3,'wechat_level','level3');
    $('#level_group').change(function(){
      price_group_level = $(this).children('option:selected').data('group');
      level_3 = $('input[name="wechat_level_3"]:checked').val();
      $('#price_group_level').val(price_group_level);
      auto_fenix(price_group_level,level_3,'wechat_level','level3');
    });
  }
</script>