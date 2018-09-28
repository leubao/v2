<form class="form-horizontal" action="{:U('Crm/Member/add_type',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
    <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <td width="100px">类型名称:</td>
          <td><input type="text" name="title" size="30" maxlength="20" class="required"/></td>
        </tr>
        <tr>
          <td width="100px">类型属性:</td>
          <td>
            <input type="radio" name="type" value="1" data-toggle="icheck" data-label="按次计费" />
            <input type="radio" name="type" value="3" data-toggle="icheck" data-label="时间段计费" />
            <input type="radio" name="type" value="4" data-toggle="icheck" data-label="身份识别" />
            <input type="radio" name="type" value="5" data-toggle="icheck" data-label="按天计费" />
          </td>
        </tr>
        <tr>
          <td width="100px">办理期:</td>
          <td><input type="text" size="11" id="starttime" name="starttime" data-toggle="datepicker" value="{$starttime}">
              <label>至</label>
              <input type="text" size="11" id="endtime" name="endtime" data-toggle="datepicker"  value="{$endtime}">
            </td>
        </tr>
        <tr>
          <td width="100px">有效期:</td>
          <td><input type="text" size="11" name="eff_starttime" id="eff_starttime" data-toggle="datepicker" value="">
              <label>至</label>
              <input type="text" size="11" name="eff_endtime" id="eff_endtime" data-toggle="datepicker"  value="">
            </td>
        </tr>
        <tr>
          <td width="100px">天/次数:</td>
          <td><input type="text" name="number" id="number" value="0" size="5" /><span class="fun_tips">按天、次卡选择</span></td>
        </tr>
        <tr>
          <td width="100px">金额:</td>
          <td><input type="text" name="money" value="0" size="5" /><span class="fun_tips">金额</span></td>
        </tr>
        <tr>
          <td width="130px">可办理区域:</td>
          <td><input type="text" name="area" size="35" value="{$data.rule.area}" /><span class="fun_tips">请输入允许办理的身份证号前4位，多个区域用”,“隔开</span></td>
        </tr>
        <tr>
            <td>打印模板:</td>
            <td>
              <select name="print_tpl" data-toggle="selectpicker">
                  <option value="">打印模板</option>
                  <volist name="printer" id='pri'>
                    <option value="{$pri.id}">{$pri.title}</option>
                  </volist>
                </select>
                <span class="fun_tips">临时凭证时使用</span>
            </td>
        </tr>
        </tbody>
        <input type="hidden" name="rule" value="">
    </table>
  </div>
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
   $('input[name="type"]').on('ifChanged', function(e) {
      var checked = $(this).is(':checked'), val = $(this).val();
      if(checked){
        if(val == '1'){
          $('#starttime').attr("disabled",true);
          $('#endtime').attr("disabled",true);
          $('#number').attr("disabled",false);
        }
        if(val == '2'){
          $('#starttime').attr("disabled",true);
          $('#endtime').attr("disabled",true);
          $('#number').attr("disabled",true);
        }
        if(val == '3'){
          $('#starttime').attr("disabled",false);
          $('#endtime').attr("disabled",false);
          $('#number').attr("disabled",true);
        }
        if(val == '4'){
          $('#starttime').attr("disabled",true);
          $('#endtime').attr("disabled",true);
          $('#eff_starttime').attr("disabled",false);
          $('#eff_endtime').attr("disabled",false);
          $('#number').attr("disabled",true);
        }
        if(val == '5'){
          $('#starttime').attr("disabled",true);
          $('#endtime').attr("disabled",true);
          $('#eff_starttime').attr("disabled",true);
          $('#eff_endtime').attr("disabled",true);
          $('#number').attr("disabled",false);
        }
      }
      
   });  
</script>