<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Manage/config/third_sales',array('menuid'=>$menuid))}" method="post" data-toggle="validate">
<div class="bjui-pageContent tableContent">
  <div class="tabs-container" style="padding: 15px">
      <div class="tabs-left">
          <ul class="nav nav-tabs nav-stacked">
              <li  class="active"><a data-toggle="tab" href="#wtab-8" aria-expanded="false"><i class="fa fa-lastfm-square"></i>  智游宝分销</a></li>
          </ul>
          <div class="tabs-content">
              <div id="wtab-8" class="tab-pane active">
                <div class="panel-body">
                    <fieldset style="height:100%;">
                      <legend>智游宝分销</legend>
                      <div style="height:94%; overflow:hidden;">
                      <table class="table  table-bordered">
                      <tbody>
                        <tr>
                          <td width="120px">当前产品:</td>
                          <td>{$product.name}
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">产品编码:</td>
                          <td><input type="text" name="zyb_pro_code" class="form-control" size="40" value="{$product.param.zyb_pro_code}" placeholder="产品配置">
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">票型分组:</td>
                          <td>
                          <select name="zyb_pro_code_group" id="zyb_pro_code_group" class="required combox">
                            <option selected value="0">===请选择===</option>
                            <volist name="ticket_group" id="v">
                              <option data-group="{$v.id}" value="{$v.id}" <if condition="$vo['zyb_pro_code_group'] eq $v['id']">selected</if>>{$v.name}</option>
                            </volist>
                          </select>
                          </td>
                        </tr>
                        <tr>
                          <td width="120px">票型编码:</td>
                          <td id="price_third">
                           
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
<input name="zyb_pro_code_group" id="zyb_pro_code_group" value="{$vo.zyb_pro_code_group}" type="hidden">
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
  var zyb_pro_code = "1",
      zyb_pro_code_group = "{$vo['zyb_pro_code_group']}";

    auto_fenix(zyb_pro_code_group,zyb_pro_code,'price_third','zyb');
    $('#zyb_pro_code_group').change(function(){
      zyb_pro_code_group = $(this).children('option:selected').data('group');
      zyb_pro_code = $('input[name="zyb_pro_code"]:checked').val();
      $('#zyb_pro_code_group').val(zyb_pro_code_group);
      auto_fenix(zyb_pro_code_group,zyb_pro_code,'price_third','zyb');
    });

</script>