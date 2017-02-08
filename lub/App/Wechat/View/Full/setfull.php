<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Wechat/Full/setfull',array('menuid'=>$menuid))}" method="post" data-toggle="validate">
<div class="bjui-pageContent tableContent">
  <div class="tabs-container" style="padding: 15px">
      <div class="tabs-left">
          <ul class="nav nav-tabs nav-stacked">
              <li  class="active"><a data-toggle="tab" href="#wtab-8" aria-expanded="false"><i class="fa fa-lastfm-square"></i>  全员销售</a></li>
          </ul>
          <div class="tabs-content">
              <div id="wtab-8" class="tab-pane active">
                <div class="panel-body">
                    <fieldset style="height:100%;">
                      <legend>全员销售</legend>
                      <div style="height:94%; overflow:hidden;">
                      <table class="table  table-bordered">
                      <tbody>
                        <tr>
                          <td width="120px">默认分组:</td>
                          <td>
                          <select name="full_group" id="full_group" class="required combox">
                            <option selected value="0">===请选择===</option>
                            <volist name="group" id="v">
                              <option data-group="{$v.price_group}" value="{$v.id}" <if condition="$vo['full_group'] eq $v['id']">selected</if>>{$v.name}</option>
                            </volist>
                          </select>
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">开启多级分销:</td>
                          <td><input name="wechant_full_level" value="1" type="radio"  <if condition=" $vo['wechant_full_level'] == '1' ">checked</if>> 开启 
                                <input name="wechant_full_level" value="0" type="radio" <if condition=" $vo['wechant_full_level'] == '0' ">checked</if>> 关闭
                          </td>
                        </tr>
                        <tr style="display: <if condition=" $vo['wechant_full_level'] == '1' ">none</else>block</if>;">
                          <td width="120px">返利配置:</td>
                          <td id="wechat_rebate">
                            <table class="table  table-bordered">
                              <tr>
                                <td>票型名称</td>
                                <td>票面价</td>
                                <td>一级利润</td>
                                <td>二级利润</td>
                                <td>三级利润</td>
                              </tr>
                             
                            </table>
                          </td>
                        </tr>
                        <!--
                        <tr>
                          <td width="120px">分销佣金:</td>
                          <td>


                          <table class="table table-bordered">
                            <tr>
                              <td width="120px">一级%:</td>
                              <td><input type="text" name="fullcash_1" class="form-control" size="20" value="{$vo.full_cash}" placeholder="200">
                              </td>
                            </tr>
                            <tr>
                              <td width="120px">二级%:</td>
                              <td><input type="text" name="fullcash_2" class="form-control" size="20" value="{$vo.full_cash}" placeholder="200">
                              </td>
                            </tr>
                            <tr>
                              <td width="120px">三级%:</td>
                              <td><input type="text" name="fullcash_3" class="form-control" size="20" value="{$vo.full_cash}" placeholder="200">
                              </td>
                            </tr>
                          </table>

                          
                          </td>
                        </tr>
                        -->
                        <tr>
                          <td width="120px">提现限制:</td>
                          <td><input type="text" name="full_cash" class="form-control" size="40" value="{$vo.full_cash}" placeholder="200">
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">单场配额:</td>
                          <td><input type="text" name="full_quota" class="form-control" size="40" value="{$vo.full_quota}" placeholder="单场配额">
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
  <input name="type" value="3" type="hidden">
  <input name="product_id" value="{$pid}" type="hidden">
  <input name="group" id="group" value="" type="hidden">
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
  $('#full_group').change(function(){
    var group_id = $(this).children('option:selected').data('group'),
        full_level = $('input[name="wechant_full_level"]:checked').val();
    //拉取该分组下所有票型
    if(group_id != '' || null || undefined && full_level == '1'){
        $('#group').val(group_id);
        var content = '';
          $.ajax({
            url: '{:U('Manage/Index/public_get_group_ticket')}'+"&group_id="+group_id+"&scene=4",
            type: 'GET',
            dataType: 'JSON',
            timeout: 1500,
            error: function(){
                layer.msg('服务器请求超时，请检查网络...');
            },
            success: function(rdata){
              if(rdata.statusCode == '200'){
                 /*写入*/
                content = "<table class='table  table-bordered'><tr><td>票型名称</td><td>票面价</td><td>一级利润</td><td>二级利润</td><td>三级利润</td></tr>";
                $(rdata.data).each(function(idx,vo){
                  content += "<tr><td align='center'>"+vo.name+"</td><td>"+vo.price+"</td>"+
                  "<td><input type='text' name='tic["+vo.id+"][]' class='form-control' size='8' value=''/></td>"+
                  "<td><input type='text' name='tic["+vo.id+"][]'' class='form-control' size='8' value=''/></td>"+
                  "<td><input type='text' name='tic["+vo.id+"][]'' class='form-control' size='8' value=''/></td></tr>";                
                });
                content += "</table>";
              }
              $("#wechat_rebate").html(content); 
            }
        });
    }
  });
</script>