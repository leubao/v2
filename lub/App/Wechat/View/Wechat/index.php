<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Wechat/Wechat/index',array('menuid'=>$menuid))}" method="post" data-toggle="validate">
<div class="bjui-pageContent tableContent">
  <div class="tabs-container" style="padding: 15px">
      <div class="tabs-left">
          <ul class="nav nav-tabs nav-stacked">
              <li  class="active"><a data-toggle="tab" href="#wtab-4" aria-expanded="false"><i class="fa fa-lastfm-square"></i>  微信公众平台</a></li>
              <li class=""><a data-toggle="tab" href="#wtab-5" aria-expanded="false"><i class="fa fa-lastfm-square"></i>  分享设置</a></li>
              <li class=""><a data-toggle="tab" href="#wtab-6" aria-expanded="false"><i class="fa fa-cc-visa"></i>  页面设置</a></li>
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
                          <td width="120px">订单模板消息:</td>
                          <td><input type="text" name="wx_tplmsg_order_id" class="form-control" value="{$vo.wx_tplmsg_order_id}" size="60" placeholder="订单模板消息id">
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">模板消息备注:</td>
                          <td><textarea name="wx_tplmsg_order_remark" cols="30" rows="3">{$vo.wx_tplmsg_order_remark}</textarea></td>
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
              <div id="wtab-5" class="tab-pane">
                  <div class="panel-body">
                      <fieldset style="height:100%;">
                        <legend>分享设置</legend>
                        <div style="height:94%; overflow:hidden;">
                          <table class="table  table-bordered">
                          <tbody>
                            <tr>
                              <td width="100px">分享标题:</td>
                              <td><input type="text" name="wx_share_title" value="{$vo.wx_share_title}" class="form-control required" data-rule="required;" size="40" placeholder="分享标题">
                              <span class="remark">出现在朋友圈中的标题</span>
                              </td>
                            </tr>
                            <tr>
                              <td width="100px">分享描述:</td>
                              <td><textarea name="wx_share_desc" cols="30" rows="3">{$vo.wx_share_desc}</textarea>
                              </td>
                            </tr>
                            <tr>
                              <td width="100px">分享图标:</td>
                              <td><div class="col-sm-6 col-md-3">
                                <div class="thumbnail"> <img data-src="holder.js/100%x200" alt="分享图标" src="{$config_siteurl}static/images/wshare_{$pid}.jpg" data-holder-rendered="true" style="height: 150px; width: 100%; display: block;">
                                  <div class="caption">
                                    <div style="display: inline-block; vertical-align: middle;">
                                      <div style="display: inline-block; vertical-align: middle;">
                                        <div data-toggle="upload" data-uploader="{:U('Manage/Config/up_img',array('menuid'=>$menuid,'name'=>'wshare_'.$pid));}" 
                                                            data-file-size-limit="1024000000"
                                                            data-file-type-exts="*.jpg;*.png;*.gif;*.pem"
                                                            data-multi="true"
                                                            data-on-upload-success="pic_upload_success"
                                                            data-icon="cloud-upload">分享图标</div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              </td>
                            </tr>
                          </tbody>
                          </table>
                        </div>
                      </fieldset>
                  </div>
              </div>
              <div id="wtab-6" class="tab-pane">
                  <div class="panel-body">
                       <fieldset style="height:100%;">
                        <legend>页面设置</legend>
                        <table class="table  table-bordered">
                          <tbody>
                            <tr>
                              <td width="100px">页面标题:</td>
                              <td><input type="text" name="wx_page_title" value="{$vo.wx_page_title}" class="form-control required" data-rule="required;" size="40" placeholder="页面标题">
                              <span class="remark">出现在页面中的标题</span>
                              </td>
                            </tr>
                            <tr>
                              <td width="100px">背景图片:</td>
                              <td>
                                <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="pic" value="{$cache.pic}" id="App-pic">
                                <span class="input-group-btn">
                                <button class="btn btn-default shiny" type="button" onclick="appImgviewer('App-pic')"><i class="fa fa-camera-retro"></i>预览</button><button class="btn btn-default shiny" type="button" onclick="appImguploader('App-pic',false)"><i class="glyphicon glyphicon-picture"></i>上传</button>
                            </span>
                            </div>
                              <div class="col-sm-6 col-md-3">
                                <div class="thumbnail"> <img data-src="holder.js/100%x200" alt="分享图标" src="{$config_siteurl}static/images/wsbj_1_{$pid}.jpg" data-holder-rendered="true" style="height: 150px; width: 100%; display: block;">
                                  <div class="caption">
                                    <div style="display: inline-block; vertical-align: middle;">
                                      <div style="display: inline-block; vertical-align: middle;">
                                        <div data-toggle="upload" data-uploader="{:U('Manage/Config/up_img',array('menuid'=>$menuid,'name'=>'wsbj_1_'.$pid));}" 
                                                            data-file-size-limit="1024000000"
                                                            data-file-type-exts="*.jpg;*.png;*.gif;"
                                                            data-multi="true"
                                                            data-on-upload-success="pic_upload_success"
                                                            data-icon="cloud-upload">背景1</div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-sm-6 col-md-3">
                                <div class="thumbnail"> <img data-src="holder.js/100%x200" alt="分享图标" src="{$config_siteurl}static/images/wsbj_2_{$pid}.jpg" data-holder-rendered="true" style="height: 150px; width: 100%; display: block;">
                                  <div class="caption">
                                    <div style="display: inline-block; vertical-align: middle;">
                                      <div style="display: inline-block; vertical-align: middle;">
                                        <div data-toggle="upload" data-uploader="{:U('Manage/Config/up_img',array('menuid'=>$menuid,'name'=>'wsbj_2_'.$pid));}" 
                                                            data-file-size-limit="1024000000"
                                                            data-file-type-exts="*.jpg;*.png;*.gif;"
                                                            data-multi="true"
                                                            data-on-upload-success="pic_upload_success"
                                                            data-icon="cloud-upload">背景2</div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-sm-6 col-md-3">
                                <div class="thumbnail"> <img data-src="holder.js/100%x200" alt="分享图标" src="{$config_siteurl}static/images/wsbj_3_{$pid}.jpg" data-holder-rendered="true" style="height: 150px; width: 100%; display: block;">
                                  <div class="caption">
                                    <div style="display: inline-block; vertical-align: middle;">
                                      <div style="display: inline-block; vertical-align: middle;">
                                        <div data-toggle="upload" data-uploader="{:U('Manage/Config/up_img',array('menuid'=>$menuid,'name'=>'wsbj_3_'.$pid));}" 
                                                            data-file-size-limit="1024000000"
                                                            data-file-type-exts="*.jpg;*.png;*.gif;"
                                                            data-multi="true"
                                                            data-on-upload-success="pic_upload_success"
                                                            data-icon="cloud-upload">背景3</div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-sm-6 col-md-3">
                                <div class="thumbnail"> <img data-src="holder.js/100%x200" alt="分享图标" src="{$config_siteurl}static/images/wsbj_4_{$pid}.jpg" data-holder-rendered="true" style="height: 150px; width: 100%; display: block;">
                                  <div class="caption">
                                    <div style="display: inline-block; vertical-align: middle;">
                                      <div style="display: inline-block; vertical-align: middle;">
                                        <div data-toggle="upload" data-uploader="{:U('Manage/Config/up_img',array('menuid'=>$menuid,'name'=>'wsbj_4_'.$pid));}" 
                                                            data-file-size-limit="1024000000"
                                                            data-file-type-exts="*.jpg;*.png;*.gif;"
                                                            data-multi="true"
                                                            data-on-upload-success="pic_upload_success"
                                                            data-icon="cloud-upload">背景4</div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              </td>
                            </tr>
                          </tbody>
                          </table>
                      </fieldset>
                  </div>
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
<script>
      //App默认图片上传管理器
function appImguploader(fbid, isall) {
  //fbid 查找带回的文本框ID,全局唯一
  //isall 多图,单图模式
  $.ajax({
      type: "post",
      url: "{:U('Multi/Upload/indeximg')}",
      data: {
          'fbid': fbid,
          'isall': isall
      },
      dataType: "json",
      //beforeSend:$.App.loading(),
      success: function(mb) {
          //$.App.loading();
          bootbox.dialog({
              message: mb,
              title: "图片上传管理器",
              className: "modal-darkorange",
              buttons: {
                  "追加": {
                      className: "btn-success",
                      callback: function() {
                          if (isall == 'false') {
                              $('#' + fbid).val($('#App-uploader-findback').val());
                          } else {
                              $('#' + fbid).val($('#' + fbid).val() + $('#App-uploader-findback').val());
                          }
                      }
                  },
                  "替换": {
                      className: "btn-blue",
                      callback: function() {
                          $('#' + fbid).val($('#App-uploader-findback').val());
                      }
                  },
                  "取消": {
                      className: "btn-danger",
                      callback: function() {}
                  }
              }
          });
      },
      error: function(xhr) {
          $.App.alert('danger', '通讯失败！请重试！');
      }
  });
  return false;
}
//App默认图片预览器
function appImgviewer(fbid) {
  //fbid 查找带回的文本框ID,全局唯一
  //isall 多图,单图模式
  var ids = $('#' + fbid).val();
  if (!ids) {
      $.App.alert('danger', '您还没有图片可以预览！');
      return false;
  }
  $.ajax({
      type: "post",
      url: "{:U('Multi/Index/appImgviewer')}",
      data: {
          'ids': ids
      },
      dataType: "json",
      success: function(mb) {
          bootbox.dialog({
              message: mb,
              title: "图片预览器",
              className: "modal-darkorange",
              buttons: {
                  success: {
                      label: "确定",
                      className: "btn-blue",
                      callback: function() {}
                  },
                  "取消": {
                      className: "btn-danger",
                      callback: function() {}
                  }
              }
          });
      },
      error: function(xhr) {
          $.App.alert('danger', '通讯失败！请重试！');
      }
  });
  return false;
}
</script>