<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Manage/Pay/index',array('menuid'=>$menuid))}" method="post" data-toggle="validate">
<div class="bjui-pageContent tableContent">
  <div class="tabs-container" style="padding: 15px">
      <div class="tabs-left">
          <ul class="nav nav-tabs nav-stacked">
              <li  class="active"><a data-toggle="tab" href="#paytab-1" aria-expanded="false"><i class="fa fa-lastfm-square"></i>  快算聚合</a></li>
              <li><a data-toggle="tab" href="#paytab-4" aria-expanded="false"><i class="fa fa-lastfm-square"></i>  微信支付</a></li>
              <li class=""><a data-toggle="tab" href="#paytab-5" aria-expanded="false"><i class="fa fa-lastfm-square"></i>  支付宝支付</a></li>
              <li class=""><a data-toggle="tab" href="#paytab-6" aria-expanded="false"><i class="fa fa-lastfm-square"></i>  建设银行</a></li>
          </ul>
          <div class="tabs-content">
            <div id="paytab-1" class="tab-pane active">
                <div class="panel-body">
                    <fieldset style="height:100%;">
                      <legend>快算聚合</legend>
                      <div style="height:94%; overflow:hidden;">
                      <table class="table  table-bordered">
                      <tbody>
                        <tr>
                          <td width="120px">APPID:</td>
                          <td><input type="text" name="app_id" class="form-control" size="30" value="{$vo.app_id}" placeholder="appID">
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">商户id:</td>
                          <td><input type="text" name="mch_id" class="form-control" value="{$vo.mch_id}" size="20" placeholder="商户id">
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">商户支付密钥Key:</td>
                          <td><input type="text" name="mch_key" class="form-control" value="{$vo.mch_key}" size="40" placeholder="商户支付密钥Key">
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">资金处理类型:</td>
                          <td>
                            <select name="profit_type" data-toggle="selectpicker" data-rule="required">
                                <option value="REAL_TIME" <eq name="vo.profit_type" value='REAL_TIME'>selected</eq>>实时订单</option>
                                <option value="DELAY_SETTLE" <eq name="vo.profit_type" value='DELAY_SETTLE'>selected</eq>>延迟结算</option>
                                <option value="REAL_TIME_DIVIDE" <eq name="vo.profit_type" value='REAL_TIME_DIVIDE'>selected</eq>>实时分账</option>
                                <option value="SPLIT_ACCOUNT_IN" <eq name="vo.profit_type" value='SPLIT_ACCOUNT_IN'>selected</eq>>实时拆分入账</option>
                            </select>
                          </td>
                        </tr>
                      </tbody>
                      </table>
                    </div>
                    </fieldset>
                </div>
              </div>
              <div id="paytab-4" class="tab-pane">
                <div class="panel-body">
                    <fieldset style="height:100%;">
                      <legend>微信支付</legend>
                      <div style="height:94%; overflow:hidden;">
                      <table class="table  table-bordered">
                      <tbody>
                        <tr>
                          <td width="120px">APPID:</td>
                          <td><input type="text" name="wx_sub_appid" class="form-control" size="30" value="{$vo.wx_sub_appid}" placeholder="appID">
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">商户id:</td>
                          <td><input type="text" name="wx_sub_mchid" class="form-control" value="{$vo.wx_sub_mchid}" size="20" placeholder="商户id">
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">商户支付密钥Key:</td>
                          <td><input type="text" name="wx_sub_mchkey" class="form-control" value="{$vo.wx_sub_mchkey}" size="40" placeholder="商户支付密钥Key">
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">apiclient_cert.pem:</td>
                          <td>
                          <span style="float: left;">{$path.w_cert|if_file}</span>
                              <div id="doc_pic_up" data-toggle="upload" data-uploader="{:U('Wechat/Wechat/public_upload');}"
                                        data-file-size-limit="1024000000"
                                        data-file-type-exts="*.pem"
                                        data-multi="true"
                                        data-auto="true"
                                        data-on-upload-success="doc_upload_success"
                                        data-icon="cloud-upload"></div>
                              
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">apiclient_key.pem:</td>
                          <td> <span style="float: left;">{$path.w_key|if_file}</span> <div id="doc_pic_up" data-toggle="upload" data-uploader="{:U('Wechat/Wechat/public_upload');}"
                                        data-file-size-limit="1024000000"
                                        data-file-type-exts="*.pem"
                                        data-multi="true"
                                        data-auto="true"
                                        data-on-upload-success="doc_upload_success"
                                        data-icon="cloud-upload"></div>
                              
                          </td>
                        </tr>
                      </tbody>
                      </table>
                    </div>
                    </fieldset>
                    
                </div>
              </div>
              <div id="paytab-5" class="tab-pane">
                  <div class="panel-body">
                      <fieldset style="height:100%;">
                        <legend>支付宝支付</legend>
                        <div style="height:94%; overflow:hidden;">
                          <table class="table  table-bordered">
                          <tbody>
                            <tr>
                              <td width="120px">partner_id:</td>
                              <td><input type="text" name="ali_partner_id" class="form-control" value="{$vo.ali_partner_id}" size="20" placeholder="partner">
                              </td>
                            </tr>
                            <tr>
                              <td width="120px">APPID:</td>
                              <td><input type="text" name="ali_app_id" class="form-control" value="{$vo.ali_app_id}" size="20" placeholder="appid">
                              </td>
                            </tr>
                            <tr>
                              <td width="120px">公共Key:</td>
                              <td><input type="text" name="ali_public_key" class="form-control" value="{$vo.ali_public_key}" size="40" placeholder="公共Key">
                              </td>
                            </tr>
                            <tr>
                              <td width="120px">rsa_private_key:</td>
                              <td>
                              <span style="float: left;">{$path.rsa_private_key|if_file}</span>
                                  <div id="doc_pic_up" data-toggle="upload" data-uploader="{:U('Wechat/Wechat/public_upload');}"
                                            data-file-size-limit="1024000000"
                                            data-file-type-exts="*.pem"
                                            data-multi="true"
                                            data-auto="true"
                                            data-on-upload-success="doc_upload_success"
                                            data-icon="cloud-upload"></div>
                                  
                              </td>
                            </tr>
                          </tbody>
                          </table>
                        </div>
                      </fieldset>
                  </div>
              </div>
              <div id="paytab-6" class="tab-pane">
                  <div class="panel-body">
                      <fieldset style="height:100%;">
                        <legend>建设银行</legend>
                        <div style="height:94%; overflow:hidden;">
                          <table class="table  table-bordered">
                          <tbody>
                            <tr>
                              <td width="120px">商户柜台代码:</td>
                              <td><input type="text" name="ccb_posid" class="form-control" value="{$vo.ccb_posid}" size="20" placeholder="商户柜台代码">
                              </td>
                            </tr>
                            <tr>
                              <td width="120px">商户代码:</td>
                              <td><input type="text" name="ccb_merchantid" class="form-control" value="{$vo.ccb_merchantid}" size="20" placeholder="商户代码">
                              </td>
                            </tr>
                            <tr>
                              <td width="120px">分行代码:</td>
                              <td><input type="text" name="ccb_branchid" class="form-control" value="{$vo.ccb_branchid}" size="20" placeholder="分行代码">
                              </td>
                            </tr>
                            <tr>
                              <td width="120px">查询密码:</td>
                              <td><input type="text" name="ccb_qupwd" class="form-control" value="{$vo.ccb_qupwd}" size="20" placeholder="查询密码">
                              </td>
                            </tr>
                            <tr>
                              <td width="120px">公钥后30位:</td>
                              <td><input type="text" name="ccb_pub" class="form-control" value="{$vo.ccb_pub}" size="40" placeholder="公钥后30位">
                              </td>
                            </tr>
                            <tr>
                              <td width="120px">公钥:</td>
                              <td><input type="text" name="ccb_pubkey" class="form-control" value="{$vo.ccb_pubkey}" size="40" placeholder="柜台公钥">
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
<input name="type" value="2" type="hidden">
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