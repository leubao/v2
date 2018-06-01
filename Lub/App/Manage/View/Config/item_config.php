<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Manage/config/item_config',array('menuid'=>$menuid))}" method="post" data-toggle="validate">
<div class="bjui-pageContent tableContent">
  <div class="tabs-container" style="padding: 15px">
      <div class="tabs-left">
          <ul class="nav nav-tabs nav-stacked">
              <li  class="active"><a data-toggle="tab" href="#wtab-8" aria-expanded="false"><i class="fa fa-lastfm-square"></i>  商户设置</a></li>
          </ul>
          <div class="tabs-content">
              <div id="wtab-8" class="tab-pane active">
                <div class="panel-body">
                    <fieldset style="height:100%;">
                      <legend>商户设置</legend>
                      <div style="height:94%; overflow:hidden;">
                      <table class="table  table-bordered">
                      <tbody>
                        <tr>
                          <td width="120px">年卡开启:</td>
                          <td><input type="radio" name="year_card" data-toggle="icheck" value="1" <eq name="vo['year_card']" value="1"> checked</eq> data-label="开启&nbsp;">
                                <input type="radio" name="year_card" data-toggle="icheck" value="0" <eq name="vo['year_card']" value="0"> checked</eq> data-label="关闭">
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">渠道分级扣款:</td>
                          <td><input type="radio" name="level_pay" data-toggle="icheck" value="1" <eq name="vo['level_pay']" value="1"> checked</eq> data-label="开启&nbsp;">
                                <input type="radio" name="level_pay" data-toggle="icheck" value="0" <eq name="vo['level_pay']" value="0"> checked</eq> data-label="关闭">
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">低授信额告警:</td>
                          <td><input type="radio" name="if_money_low" value="1" <eq name="vo.if_money_low" value="1"> checked</eq>> 开启
                            <input type="radio" name="if_money_low" value="0" <eq name="vo.if_money_low" value="0"> checked</eq>> 关闭</td>
                        </tr>
                        <tr>
                          <td width="120px">告警金额:</td>
                          <td><input type="text" name="money_low" value="{$vo.money_low}" size="40"></td>
                        </tr>
                        <tr>
                          <td width="120px">分销补贴方式:</td>
                          <td><input type="radio" name="rebate_pay" value="1" <eq name="vo.rebate_pay" value="1"> checked</eq>> 企业付款到个人
                            <input type="radio" name="rebate_pay" value="2" <eq name="vo.rebate_pay" value="2"> checked</eq>> 微信企业红包</td>
                        </tr>
                        <tr>
                          <td width="120px">返利红包模板:</td>
                          <td><select name="red_tpl" data-toggle="selectpicker">
                              <option value="">红包模板</option>
                              <volist name="redtpl" id='red'>
                                <option value="{$red.id}" <if condition="$vo['red_tpl'] eq $red['id']">selected</if>>{$red.act_name}</option>
                              </volist>
                            </select></td>
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
<input name="type" value="1" type="hidden">
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