<form class="form-horizontal" action="{:U('Crm/Member/add_type',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
    <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <td width="100px">类型名称:</td>
          <td>{$data.title}</td>
        </tr>
        <tr>
          <td width="100px">类型属性:</td>
          <td>
            {$data.type|memberType}
          </td>
        </tr>
        <tr>
          <td width="100px">办理期:</td>
          <td>{$data.rule.datetime.starttime} 至 {$data.rule.datetime.endtime}</td>
        </tr>
        <tr>
          <td width="100px">有效期:</td>
          <td>{$data.rule.efftime.start} 至 {$data.rule.efftime.end}</td>
        </tr>
        <tr>
          <td width="100px">次数:</td>
          <td>{$data.rule.number} 次</td>
        </tr>
        <tr>
          <td width="100px">金额:</td>
          <td>{$data.money}</td>
        </tr>
        <tr>
          <td width="100px">可办理区域:</td>
          <td>{$data.rule.area}</td>
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