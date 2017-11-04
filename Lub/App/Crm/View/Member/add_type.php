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
            <input type="radio" name="type" value="2" data-toggle="icheck" data-label="年卡" />
            <input type="radio" name="type" value="1" data-toggle="icheck" data-label="按次计费" />
            <input type="radio" name="type" value="3" data-toggle="icheck" data-label="时间段计费" />
            <input type="radio" name="type" value="4" data-toggle="icheck" data-label="身份识别" />
          </td>
        </tr>
        <tr>
          <td width="100px">时间段:</td>
          <td><input type="text" size="11" id="starttime" name="starttime" data-toggle="datepicker" value="{$starttime}">
              <label>至</label>
              <input type="text" size="11" id="endtime" name="endtime" data-toggle="datepicker"  value="{$endtime}">
            </td>
        </tr>
        <tr>
          <td width="100px">次数:</td>
          <td><input type="text" name="number" id="number" value="0" size="5" /><span class="fun_tips">次卡选择</span></td>
        </tr>
        <tr>
          <td width="100px">起售金额:</td>
          <td><input type="text" name="money" value="0" size="5" /><span class="fun_tips">起售金额</span></td>
        </tr>
        </tbody>
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
          $('#number').attr("disabled",true);
        }
      }
      
   });  
</script>