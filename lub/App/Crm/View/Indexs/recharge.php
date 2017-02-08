<div class="pageContent">
  <form method="post" action="{:U('Crm/Index/recharge',array('navTabId'=>$navTabId));}" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
    <div class="pageFormContent" layoutH="58">
    <div class="unit">
        <label>当前渠道商：</label>
        {$crmid|crmName}
      </div>
      <div class="unit">
        <label>充值金额：</label>
        <input type="text" name="cash" size="30" maxlength="20" class="required"/>
      </div>
      <div class="unit">
        <label>备 注：</label>
        <textarea name="remark" cols="40" rows="3"></textarea>
      </div>
	</div>
    <div class="formBar">
      <ul>
        <li>
          <div class="buttonActive">
            <div class="buttonContent">
              <input name="crmid" value="{$crmid}" type="hidden"> 
              <button type="submit">提交</button>
            </div>
          </div>
        </li>
        <li>
          <div class="button">
            <div class="buttonContent">
              <button type="button" class="close">取消</button>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </form>
</div>