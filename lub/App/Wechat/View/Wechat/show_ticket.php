<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Wechat/Wechat/show_ticket',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
<!--分享配置-->
<table class="table  table-bordered">
    <tbody>
      <tr>
        <td width="100px">分享标题:</td>
        <td><input type="text" name="title" value="{$data.tpl.title}" class="form-control required" data-rule="required;" size="40" placeholder="分享标题">
        <span class="remark">出现在朋友圈中的标题</span>
        </td>
      </tr>
      <tr>
        <td width="100px">分享描述:</td>
        <td><textarea name="desc" cols="30" rows="3">{$data.tpl.desc}</textarea>
        </td>
      </tr>
      <tr>
        <td width="100px">分享图标:</td>
        <td><div class="col-sm-6 col-md-3">
          <div class="thumbnail"> <img data-src="holder.js/100%x200" alt="分享图标" src="{$config_siteurl}static/images/wshare_{$data.id}.jpg" data-holder-rendered="true" style="height: 150px; width: 100%; display: block;">
            <div class="caption">
              <div style="display: inline-block; vertical-align: middle;">
                <div style="display: inline-block; vertical-align: middle;">
                  <div data-toggle="upload" data-uploader="{:U('Manage/Config/up_img',array('menuid'=>$menuid,'name'=>'wshare_'.$data['id']));}" 
                                      data-file-size-limit="1024000000"
                                      data-file-type-exts="*.jpg;*.png;*.gif;"
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
      <tr>
        <td width="100px">背景图片:</td>
        <td><div class="col-sm-6 col-md-3">
          <div class="thumbnail"> <img data-src="holder.js/100%x200" alt="分享图标" src="{$config_siteurl}static/images/wsbj_1_{$data.id}.jpg" data-holder-rendered="true" style="height: 150px; width: 100%; display: block;">
            <div class="caption">
              <div style="display: inline-block; vertical-align: middle;">
                <div style="display: inline-block; vertical-align: middle;">
                  <div data-toggle="upload" data-uploader="{:U('Manage/Config/up_img',array('menuid'=>$menuid,'name'=>'wsbj_1_'.$data['id']));}" 
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
          <div class="thumbnail"> <img data-src="holder.js/100%x200" alt="分享图标" src="{$config_siteurl}static/images/wsbj_2_{$data.id}.jpg" data-holder-rendered="true" style="height: 150px; width: 100%; display: block;">
            <div class="caption">
              <div style="display: inline-block; vertical-align: middle;">
                <div style="display: inline-block; vertical-align: middle;">
                  <div data-toggle="upload" data-uploader="{:U('Manage/Config/up_img',array('menuid'=>$menuid,'name'=>'wsbj_2_'.$data['id']));}" 
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
          <div class="thumbnail"> <img data-src="holder.js/100%x200" alt="分享图标" src="{$config_siteurl}static/images/wsbj_3_{$data.id}.jpg" data-holder-rendered="true" style="height: 150px; width: 100%; display: block;">
            <div class="caption">
              <div style="display: inline-block; vertical-align: middle;">
                <div style="display: inline-block; vertical-align: middle;">
                  <div data-toggle="upload" data-uploader="{:U('Manage/Config/up_img',array('menuid'=>$menuid,'name'=>'wsbj_3_'.$data['id']));}" 
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
          <div class="thumbnail"> <img data-src="holder.js/100%x200" alt="分享图标" src="{$config_siteurl}static/images/wsbj_4_{$data.id}.jpg" data-holder-rendered="true" style="height: 150px; width: 100%; display: block;">
            <div class="caption">
              <div style="display: inline-block; vertical-align: middle;">
                <div style="display: inline-block; vertical-align: middle;">
                  <div data-toggle="upload" data-uploader="{:U('Manage/Config/up_img',array('menuid'=>$menuid,'name'=>'wsbj_4_'.$data['id']));}" 
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
<!--图片背景-->
<!--分享图片-->
</div>
<input type="hidden" name="id" value="{$data.id}"></input>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>