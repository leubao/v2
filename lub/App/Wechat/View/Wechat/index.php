<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Wechat/Wechat/index',array('menuid'=>$menuid))}" method="post" data-toggle="validate">
<div class="bjui-pageContent tableContent">
  <div class="tabs-container" style="padding: 15px">
      <div class="tabs-left">
          <ul class="nav nav-tabs nav-stacked">
              <li  class="active"><a data-toggle="tab" href="#wtab-4" aria-expanded="false"><i class="fa fa-lastfm-square"></i>  微信公众平台</a></li>
          </ul>
          <div class="tabs-content">
              <div id="wtab-4" class="tab-pane active">
                <div class="panel-body">
                    <fieldset style="height:100%;">
                      <legend>微信公众平台</legend>
                      <div style="height:94%; overflow:hidden;">
                      <table class="table  table-bordered">
                      <tbody>
                        <tr>
                          <td width="120px">APPID:</td>
                          <td><input type="text" name="wx_sub_appid" class="form-control" size="30" value="{$vo.wx_sub_appid}" placeholder="appID">
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">appSecret:</td>
                          <td><input type="text" name="wx_appsecret" class="form-control" size="40" value="{$vo.wx_appsecret}" placeholder="appsecret">
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">Token:</td>
                          <td><input type="text" name="wx_token" class="form-control" size="40" value="{$vo.wx_token}" placeholder="Token">
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">encodingASEKey:</td>
                          <td><input type="text" name="wx_encoding" class="form-control" value="{$vo.wx_encoding}" size="40" placeholder="encodingASEKey">
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">URL:</td>
                          <td><input type="text" name="wx_url" class="form-control" value="{$vo.wx_url}" size="40" placeholder="url">
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">默认价格组:</td>
                          <td>
                            <select name="wx_price_group" class="required combox">
                            <option selected value="0">===请选择===</option>
                            <volist name="price" id="v">
                              <option value="{$v.id}" <if condition="$vo['wx_price_group'] eq $v['id']">selected</if>>{$v.name}</option>
                            </volist>
                          </select>
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">价格显示:</td>
                          <td>
                            <input type="radio" name="price_view" value="1" <eq name="vo.price_view" value="1"> checked</eq>> 票型名称
                            <input type="radio" name="price_view" value="2" <eq name="vo.price_view" value="2"> checked</eq>> 座位区域名称+票型备注
                            <input type="radio" name="price_view" value="3" <eq name="vo.price_view" value="3"> checked</eq>> 票型名称+票型备注
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">立即购票地址:</td>
                          <td><textarea cols="30" rows="3">{$view}</textarea></td>
                        </tr>
                        <tr>
                          <td width="120px">注册地址:</td>
                          <td><textarea cols="30" rows="3">{$reg}</textarea></td>
                        </tr>
                        <tr>
                          <td width="120px">渠道绑定地址:</td>
                          <td><textarea cols="30" rows="3">{$channel}</textarea></td>
                        </tr>
                        <tr>
                          <td width="120px">活动地址:</td>
                          <td><textarea cols="30" rows="3">{$acty}</textarea></td>
                        </tr>
                        <tr>
                          <td width="120px">个人中心:</td>
                          <td><textarea cols="30" rows="3">{$uinfo}</textarea></td>
                        </tr>
                        <tr>
                          <td width="120px">订单管理:</td>
                          <td><textarea cols="30" rows="3">{$uorder}</textarea></td>
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
</div>
<input name="type" value="5" type="hidden">
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

</script>