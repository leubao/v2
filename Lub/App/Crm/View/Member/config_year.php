<form class="form-horizontal" action="{:U('Crm/Member/config_year',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
    <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <td width="130px">可办理区域:</td>
          <td><input type="text" name="area" size="35" value="{$data.rule.area}" /><span class="fun_tips">请输入允许办理的身份证号前4位，多个区域用”,“隔开</span></td>
        </tr>
        <tr>
          <td width="130px">单日入园次数:</td>
          <td><input type="text" name="day" size="5" value="{$data.rule.day}" /><span class="fun_tips">0表示不限制</span></td>
        </tr>
        <tr>
          <td width="130px">年卡价格:</td>
          <td><input type="text" name="money" size="15" value="{$data.money}" /></td>
        </tr>
        <tr>
          <td width="130px">办理时间:</td>
          <td>
            <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$data.rule.datetime.starttime}">
              <label>至</label>
            <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$data.rule.datetime.endtime}">
          </td>
        </tr>
        <tr>
          <td width="130px">年卡过期时间:</td>
          <td><input type="text" size="11" name="overdue" data-toggle="datepicker" value="{$data.rule.overdue}"></td>
        </tr>
        <tr>
          <td width="130px">申请地址:</td>
          <td>
            <textarea name="url">{$url}</textarea><a href="https://www.leubao.com/tool/qr/data={$url}">二维码下载</a>
          </td>
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