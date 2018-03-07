<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Manage/Pay/index',array('menuid'=>$menuid))}" method="post" data-toggle="validate">
<div class="bjui-pageContent tableContent">
  <div class="tabs-container" style="padding: 15px">
      <div class="tabs-left">
          <ul class="nav nav-tabs nav-stacked">
              <li  class="active"><a data-toggle="tab" href="#paytab-4" aria-expanded="false"><i class="fa fa-lastfm-square"></i>  微信支付</a></li>
              <li class=""><a data-toggle="tab" href="#paytab-5" aria-expanded="false"><i class="fa fa-lastfm-square"></i>  支付宝支付</a></li>
          </ul>
          <div class="tabs-content">
              <div id="paytab-4" class="tab-pane active">
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