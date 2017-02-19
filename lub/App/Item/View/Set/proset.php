<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Set/proset',array('menuid'=>$menuid))}" method="post" data-toggle="validate">
<div class="bjui-pageContent tableContent">
  <div class="tabs-container" style="padding: 15px">
      <div class="tabs-left">
          <ul class="nav nav-tabs nav-stacked">
              <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true"><i class="fa fa-codepen"></i> 运营设置</a></li>
              <li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false"><i class="fa fa-print"></i> 打印设置</a></li>
              <li class=""><a data-toggle="tab" href="#tab-6" aria-expanded="false"><i class="fa fa-lastfm-square"></i>  官网设置</a></li>
          </ul>
          <div class="tabs-content">
              <div id="tab-1" class="tab-pane active">
                  <div class="panel-body">
                      <fieldset style="height:100%;">
                        <legend>运营设置</legend>
                        <div style="height:94%; overflow:hidden;">
                          <table class="table  table-bordered">
                            <tbody>
                              <tr>
                                <td width="120px">窗口渠道售票:</td>
                                <td><input type="radio" name="window_channel" data-toggle="icheck" value="1" <eq name="vo['window_channel']" value="1"> checked</eq> data-label="开启&nbsp;">
                                <input type="radio" name="window_channel" data-toggle="icheck" value="0" <eq name="vo['window_channel']" value="0"> checked</eq> data-label="关闭">
                                <span class="remark">窗口是否可售团队票</span>
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">代理商制度:</td>
                                <td><input type="radio" name="agent" data-toggle="icheck" value="1" <eq name="vo['agent']" value="1"> checked</eq> data-label="开启&nbsp;">
                              <input type="radio" name="agent" data-toggle="icheck" value="0" <eq name="vo['agent']" value="0"> checked</eq> data-label="关闭">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">销售配额:</td>
                                <td>
                                <input type="radio" name="quota" data-toggle="icheck" value="1" <eq name="vo['quota']" value="1"> checked</eq> data-label="开启&nbsp;">
                                <input type="radio" name="quota" data-toggle="icheck" value="0" <eq name="vo['quota']" value="0"> checked</eq> data-label="关闭">
                                </td>
                              </tr>

                              <tr>
                                <td width="120px">全员销售:</td>
                                <td>
                                <input type="radio" name="wechat_full" data-toggle="icheck" value="1" <eq name="vo['wechat_full']" value="1"> checked</eq> data-label="开启&nbsp;">
                                <input type="radio" name="wechat_full" data-toggle="icheck" value="0" <eq name="vo['wechat_full']" value="0"> checked</eq> data-label="关闭">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">三级分销:</td>
                                <td>
                                <input type="radio" name="wechat_level" data-toggle="icheck" value="1" <eq name="vo['wechat_level']" value="1"> checked</eq> data-label="开启&nbsp;">
                                <input type="radio" name="wechat_level" data-toggle="icheck" value="0" <eq name="vo['wechat_level']" value="0"> checked</eq> data-label="关闭">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">退过期票:</td>
                                <td>
                                <input type="radio" name="plan_refund" data-toggle="icheck" value="0" <eq name="vo['plan_refund']" value="0"> checked</eq> data-label="开启&nbsp;">
                                <input type="radio" name="plan_refund" data-toggle="icheck" value="1" <eq name="vo['plan_refund']" value="1"> checked</eq> data-label="关闭">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">黑名单管理:</td>
                                <td>
                                <input type="radio" name="black" data-toggle="icheck" value="1" <eq name="vo['black']" value="1"> checked</eq> data-label="开启&nbsp;">
                                <input type="radio" name="black" data-toggle="icheck" value="0" <eq name="vo['black']" value="0"> checked</eq> data-label="关闭">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">渠道销售配额:</td>
                                <td>
                               <input type="text" name="channel_quota" value="{$vo.channel_quota}" size="10"><span class="remark">开启销售配额后，单计划默认配额</span>
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">渠道订单限额:</td>
                                <td>
                               <input type="text" name="channel_order" value="{$vo.channel_order}" size="10"><span class="remark">渠道版单笔定单最大预定数</span>
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">散客订单限额:</td>
                                <td>
                               <input type="text" name="retail_order" value="{$vo.retail_order}" size="10"><span class="remark">官网、微信售票单笔定单最大预定数</span>
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">渠道账户最低余额:</td>
                                <td>
                               <input type="text" name="money_low" value="{$vo.money_low}" size="10"><span class="remark">渠道账户最低余额报警阀值,短信通知渠道商联系人</span>
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">窗口结算方式:</td>
                                <td>
                                <input type="radio" name="settlement" data-toggle="icheck" value="1" <eq name="vo['settlement']" value="1"> checked</eq> data-label="票面价计算&nbsp;">
                                <input type="radio" name="settlement" data-toggle="icheck" value="2" <eq name="vo['settlement']" value="2"> checked</eq> data-label="结算价计算">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">渠道出票:</td>
                                <td>
                                <input type="radio" name="channel_print" data-toggle="icheck" value="1" <eq name="vo['channel_print']" value="1"> checked</eq> data-label="开启&nbsp;">
                              <input type="radio" name="channel_print" data-toggle="icheck" value="0" <eq name="vo['channel_print']" value="0"> checked</eq> data-label="关闭">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">报表统计方式:</td>
                                <td>
                                <input type="radio" name="report" data-toggle="icheck" value="1" <eq name="vo['report']" value="1"> checked</eq> data-label="按销售日期&nbsp;">
                              <input type="radio" name="report" data-toggle="icheck" value="0" <eq name="vo['report']" value="0"> checked</eq> data-label="按场次日期">
                                </td>
                              </tr>
                               <tr>
                                <td width="120px">全员销售:</td>
                                <td>
                                <input type="radio" name="full_sales" data-toggle="icheck" value="1" <eq name="vo['full_sales']" value="1"> checked</eq> data-label="开启&nbsp;">
                              <input type="radio" name="full_sales" data-toggle="icheck" value="0" <eq name="vo['full_sales']" value="0"> checked</eq> data-label="关闭">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">渠道停止售票时间:</td>
                                <td>
                                <input type="text" name="channel_time" value="{$vo.channel_time}" size="10"><span class="remark">如开演前30分钟，开演后-10分钟,0为开演即停止售票</span>
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">默认(计划)时间:</td>
                                <td>
                                <label for="j_dialog_operation" class="control-label x90">开始时间:</label>
                                <input type="text" data-toggle="datepicker" data-pattern='HH:mm:ss' name="plan_start_time" value="{$vo.plan_start_time}">
                                <label for="j_dialog_operation" class="control-label x90">结束时间:</label>
                                <input type="text" data-toggle="datepicker" data-pattern='HH:mm:ss' name="plan_end_time" value="{$vo.plan_end_time}">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">窗口核减:</td>
                                <td>
                                <input type="radio" name="win_subtract" data-toggle="icheck" value="1" <eq name="vo['win_subtract']" value="1"> checked</eq> data-label="开启&nbsp;">
                                <input type="radio" name="win_subtract" data-toggle="icheck" value="0" <eq name="vo['win_subtract']" value="0"> checked</eq> data-label="关闭">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">渠道核减:</td>
                                <td>
                                <input type="text" data-toggle="datepicker" data-pattern='HH:mm' name="subtract_time" value="{$vo.subtract_time}">
                                <span class="remark">渠道最后核减时间</span>
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">订单短信(详情):</td>
                                <td>
                                <input type="checkbox" name="ticket_sms" data-toggle="icheck" value="1" <eq name="vo['ticket_sms']" value="1"> checked</eq> data-label="发送票型信息&nbsp;">
                                <input type="checkbox" name="area_sms" data-toggle="icheck" value="1" <eq name="vo['area_sms']" value="1"> checked</eq> data-label="发送区域信息">
                                <span class="remark">区域和票型任选其一，注意结合模板短信使用</span>
                                </td>
                              </tr>
                            </tbody>
                          </table>   
                        </div>
                      </fieldset>
                  </div>
              </div>
              <div id="tab-2" class="tab-pane">
                  <div class="panel-body">
                       <fieldset style="height:100%;">
                        <legend>打印设置</legend>
                        <div style="height:94%; overflow:hidden;">
                          <table class="table  table-bordered">
                            <tbody>
                              <tr>
                                <td width="120px">打印票型备注:</td>
                                <td><input type="radio" name="print_remark" data-toggle="icheck" value="1" <eq name="vo['print_remark']" value="1"> checked</eq> data-label="开启&nbsp;">
                                <input type="radio" name="print_remark" data-toggle="icheck" value="0" <eq name="vo['print_remark']" value="0"> checked</eq> data-label="关闭">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">打印出票员:</td>
                                <td><input type="radio" name="print_user" data-toggle="icheck" value="1" <eq name="vo['print_user']" value="1"> checked</eq> data-label="开启&nbsp;">
                                <input type="radio" name="print_user" data-toggle="icheck" value="0" <eq name="vo['print_user']" value="0"> checked</eq> data-label="关闭">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">打印入场时间:</td>
                                <td><input type="radio" name="print_field" data-toggle="icheck" value="1" <eq name="vo['print_field']" value="1"> checked</eq> data-label="开启&nbsp;">
                                <input type="radio" name="print_field" data-toggle="icheck" value="0" <eq name="vo['print_field']" value="0"> checked</eq> data-label="关闭">
                                <span class="remark">默认入场时间为开演前30分钟</span>
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">座位号:</td>
                                <td><input type="radio" name="print_seat" data-toggle="icheck" value="1" <eq name="vo['print_seat']" value="1"> checked</eq> data-label="x排y号&nbsp;">
                                <input type="radio" name="print_seat" data-toggle="icheck" value="2" <eq name="vo['print_seat']" value="2"> checked</eq> data-label="x排&nbsp;">
                                <input type="radio" name="print_seat" data-toggle="icheck" value="3" <eq name="vo['print_seat']" value="3"> checked</eq> data-label="自定义&nbsp;"><span class="remark">自定义打印内容在票型中设置</span>
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">自定义入场口:</td>
                                <td>
                                <input type="radio" name="print_mouth" data-toggle="icheck" value="1" <eq name="vo['print_mouth']" value="1"> checked</eq> data-label="开启&nbsp;">
                                <input type="radio" name="print_mouth" data-toggle="icheck" value="0" <eq name="vo['print_mouth']" value="0"> checked</eq> data-label="关闭">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">默认出票方式:</td>
                                <td>
                                <input type="radio" name="print_type" data-toggle="icheck" value="1" <eq name="vo['print_type']" value="1"> checked</eq> data-label="一人一票&nbsp;">
                                <input type="radio" name="print_type" data-toggle="icheck" value="2" <eq name="vo['print_type']" value="2"> checked</eq> data-label="一单一票">
                                </td>
                              </tr>
                          </table>
                      
                        </div>
                      </fieldset>
                  </div>
              </div>
              <div id="tab-6" class="tab-pane">
              <script>KindEditor.create('textarea[name="agreement"]',{
                          minWidth : '340px',
                          minHeight : '182px',
                          resizeType : 1,
                          uploadJson : '{:U('Crm/Customer/upload');}',
                          allowFileManager : true,
                          allowImageUpload : true, 
                          items : [
                              'source', '|', 'undo', 'redo','|','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
                              'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                              'insertunorderedlist', '|', 'emoticons', 'image', 'link']
                      });

              </script>
                  <div class="panel-body">
                       <fieldset style="height:100%;">
                        <legend>官网设置</legend>
                        <div style="height:94%; overflow:hidden;">
                          <table class="table  table-bordered">
                            <tbody>
                              <tr>
                                <td width="120px">演出日期范围:</td>
                                <td>
                                <input type="text" data-toggle="datepicker" name="pstarttime" value="{$vo.pstarttime}">
                                -
                                <input type="text" data-toggle="datepicker" name="pendtime" value="{$vo.pendtime}">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">场馆地址:</td>
                                <td><input type="text" name="venues" class="form-control" value="{$vo.venues}" size="30" placeholder="场馆地址">
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">微信二维码:</td>
                                <td>
                               <div class="col-sm-6 col-md-3">
                                  <div class="thumbnail"> <img data-src="holder.js/100%x200" alt="100%x200" src="{$config_siteurl}static/images/wechat.jpg" data-holder-rendered="true" style="height: 150px; width: 100%; display: block;">
                                    <div class="caption">
                                      <div style="display: inline-block; vertical-align: middle;">
                                        <div style="display: inline-block; vertical-align: middle;">
                                          <div data-toggle="uload" data-uploader="{:U('Manage/Config/up_img',array('menuid'=>$menuid,'name'=>'wechat'));}" 
                                                              data-file-size-limit="1024000000"
                                                              data-file-type-exts="*.jpg;"
                                                              data-multi="true"
                                                              data-on-upload-success="pic_upload_success"
                                                              data-icon="cloud-upload">微信二维码</div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                </td>
                              </tr>
                              <tr>
                                <td width="120px">预购协议:</td>
                                <td>
                                <textarea name="agreement" style="width: 300px; height: 200px;"  cols="80" rows="6">
                                  {$vo.agreement}
                                </textarea>
                                </td>
                              </tr>
                          </table>
                        </div>
                      </fieldset>
                  </div>
              </div>
          </div>
      </div>
  </div>                   
</div>
  <input name="product_id" value="{$pid}" type="hidden">
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