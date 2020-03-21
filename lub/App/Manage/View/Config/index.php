<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Manage/Config/index',array('menuid'=>$menuid))}" method="post" data-toggle="validate">
<div class="bjui-pageContent tableContent">
  <div class="tabs-container" style="padding: 15px">
      <div class="tabs-left">
          <ul class="nav nav-tabs nav-stacked">
              <li class="active"><a data-toggle="tab" href="#sys-tab-1" aria-expanded="true"><i class="fa fa-codepen"></i> 系统配置</a></li>
              <li class=""><a data-toggle="tab" href="#sys-tab-2" aria-expanded="false"><i class="fa fa-print"></i> 邮件设置</a></li>
              <li class=""><a data-toggle="tab" href="#sys-tab-3" aria-expanded="false"><i class="fa fa-lastfm-square"></i>  版权信息</a></li>
              <li class=""><a data-toggle="tab" href="#sys-tab-4" aria-expanded="false"><i class="fa fa-lastfm-square"></i>  短信网关</a></li>
          </ul>
          <div class="tabs-content">
              <div id="sys-tab-1" class="tab-pane active">
                  <div class="panel-body">
                      <fieldset style="height:100%;">
                        <legend>系统配置</legend>
                        <div style="height:94%; overflow:hidden;">
                          <table class="table  table-bordered">
                            <tbody>
                              <tr>
                                <td width="120px">访问地址:</td>
                                <td><input type="text" name="siteurl" value="{$Site.siteurl}" size="40">
                                <span class="remark">请以“/”结尾</span>
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">附件地址:</td>
                                <td><input type="text" name="sitefileurl" value="{$Site.sitefileurl}" size="40">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">系统帮助:</td>
                                <td><input type="text" name="online_help" value="{$Site.online_help}" size="40">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">联系邮箱:</td>
                                <td><input type="text" name="siteemail" value="{$Site.siteemail}" size="40">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">验证码类型:</td>
                                <td><select name="checkcode_type">
                                    <option value="0" <if condition="$Site['checkcode_type'] eq '0' "> selected</if>>数字字母混合</option>
                                    <option value="1" <if condition="$Site['checkcode_type'] eq '1' "> selected</if>>纯数字</option>
                                    <option value="2" <if condition="$Site['checkcode_type'] eq '2' "> selected</if>>纯字母</option>
                                  </select>
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">渠道版权限组:</td>
                                <td>
                                  <select name="channel_role_id">
                                  <volist name="role" id="vo">
                                    <option value="{$vo.id}" <if condition="$Site['channel_role_id'] eq $vo['id'] "> selected</if>>{$vo.name}</option>
                                    </volist>
                                  </select>
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">微信APPID:</td>
                                <td><input type="text" name="wx_appid" value="wx72bcf45e0f57a192" size="40">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">微信商户号:</td>
                                <td><input type="text" name="wx_mchid" value="1377282902" size="40">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">商户key:</td>
                                <td><input type="text" name="wx_mchkey" value="02c397be9ce49f9544bd768b6480330e" size="40">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">微信appsecret:</td>
                                <td><input type="text" name="appsecret" value="66c9d92697c0496c1e72aa02f9ef2cf7" size="40">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">ICP备案号:</td>
                                <td><input type="text" name="icp" value="{$Site.icp}" size="40">
                                <span class="remark">icp 备案号</span>
                                </td>
                              </tr>
                            </tbody>
                          </table>   
                        </div>
                      </fieldset>
                      <fieldset style="height:100%;">
                        <legend>运营相关</legend>
                        <div style="height:94%; overflow:hidden;">
                          <table class="table  table-bordered">
                            <tbody>
                            <tr>
                              <td align="right"><label class="label-control">授信额阀值:</label></td>
                              <td><input type="text" name="money_low" id="mail_from" size="30" value="{$Site.money_low}"/> </td>
                            </tr>
                            </tbody>
                          </table>
                        </div>
                      </fieldset>
                  </div>
              </div>
              <div id="sys-tab-2" class="tab-pane">
                  <div class="panel-body">
                       <fieldset style="height:100%;">
                        <legend>邮件设置</legend>
                        <div style="height:94%; overflow:hidden;">
                          <table class="table  table-bordered">
                            <tbody>
                            <tr>
                              <td align="right"><label class="label-control">邮件发送模式：</label></td>
                              <td><input name="mail_type" checkbox="mail_type" value="1"  type="radio"  checked>
                                SMTP 函数发送 </td>
                            </tr>
                           
                              <tr>
                                <td align="right"><label class="label-control">邮件服务器：</label></td>
                                <td><input type="text" name="mail_server" id="mail_server" size="30" value="{$Site.mail_server}"/></td>
                              </tr>
                              <tr>
                                <td align="right"><label class="label-control">邮件发送端口：</label></td>
                                <td><input type="text" name="mail_port" id="mail_port" size="30" value="{$Site.mail_port}"/></td>
                              </tr>
                              <tr>
                                <td align="right"><label class="label-control">发件人地址：</label></td>
                                <td><input type="text" name="mail_from" id="mail_from" size="30" value="{$Site.mail_from}"/></td>
                              </tr>
                              <tr>
                                <td align="right"><label class="label-control">发件人名称：</label></td>
                                <td><input type="text" name="mail_fname" id="mail_fname" size="30" value="{$Site.mail_fname}"/></td>
                              </tr>
                              <tr>
                                <td align="right"><label class="label-control">密码验证：</label></td>
                                <td><input name="mail_auth" id="mail_auth" value="1" type="radio"  <if condition=" $Site['mail_auth'] == '1' ">checked</if>> 开启 
                                <input name="mail_auth" id="mail_auth" value="0" type="radio" <if condition=" $Site['mail_auth'] == '0' ">checked</if>> 关闭</td>
                              </tr>
                              <tr>
                                <td align="right"><label class="label-control">验证用户名：</label></td>
                                <td><input type="text" name="mail_user" id="mail_user" size="30" value="{$Site.mail_user}"/></td>
                              </tr>
                              <tr>
                                <td align="right"><label class="label-control">验证密码：</label></td>
                                <td><input type="password" name="mail_password" id="mail_password" size="30" value="{$Site.mail_password}"/></td>
                              </tr>
                            </tbody>
                          </table>
                      
                        </div>
                      </fieldset>
                  </div>
              </div>
              <div id="sys-tab-3" class="tab-pane">
                  <div class="panel-body">
                      <fieldset style="height:100%;">
                        <legend>版权信息</legend>
                        <div style="height:94%; overflow:hidden;">
                            <div class="form-group">
                              <label class="col-sm-2 control-label">公司:</label>
                              <input type="text" name="company" value="{$Site.company}" class="form-control" size="30">
                            </div>
                            <div class="form-group">
                              <label class="col-sm-2 control-label">地址:</label>
                              <input type="text" name="address" value="{$Site.address}" class="form-control" size="30" />
                            </div>
                            <div class="form-group">
                              <label class="col-sm-2 control-label">电话:</label>
                              <input type="text" name="call" value="{$Site.call}" class="form-control" size="20">
                            </div>
                            <div class="form-group">
                              <label class="col-sm-2 control-label">官网:</label>
                              <input type="text" name="website" value="{$Site.website}"  class="form-control" size="30">
                            </div>
                            
                        </div>
                      </fieldset>
                  </div>
              </div>
              <div id="sys-tab-4" class="tab-pane">
                  <div class="panel-body">
                      <fieldset style="height:100%;">
                        <legend>短信网关</legend>
                        <div style="height:94%; overflow:hidden;">
                            <div class="form-group">
                              <label class="col-sm-2 control-label">短信网关:</label>
                              <select name="sms_type" class="required" data-toggle="selectpicker" data-rule="required">
                                <option value="1">阿里智游 云信</option>
                              </select>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-2 control-label">账号:</label>
                              <input type="text" name="sms_account" value="{$Site.sms_account}" class="form-control" size="30" />
                            </div>
                            <div class="form-group">
                              <label class="col-sm-2 control-label">秘钥:</label>
                              <input type="text" name="sms_key" value="{$Site.sms_key}" class="form-control" size="20">
                            </div>
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