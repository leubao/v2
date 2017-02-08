<div class="pageContent">
  <form method="post" action="{:U('Crm/Index/quota',array('navTabId'=>$navTabId));}" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
    <div class="pageFormContent" layoutH="58">
      <div class="unit">
        <label>销售配额：</label>
        <input type="text" name="quota" size="30" maxlength="20" class="required" value="{$data.quota}"/>
      </div>
      <input type="hidden" name="id" value="{$data.id}"/>
	</div>
    <div class="formBar">
      <ul>
        <li>
          <div class="buttonActive">
            <div class="buttonContent">
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