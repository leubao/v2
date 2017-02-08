<div class="pageContent">
  <form method="post" action="{:U('Crm/Index/groupadd',array('navTabId'=>$navTabId));}" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
    <div class="pageFormContent" layoutH="58">
      <div class="unit">
        <label>分组名称：</label>
        <input type="text" name="name" size="30" maxlength="20" class="required"/>
      </div>
      <div class="unit">
         <label>价格政策：</label>
        <select name="price_group" class="required combox">
          <option selected value="0">===请选择===</option>
          <volist name="price" id="vo">
            <option value="{$vo.id}">{$vo.name}</option>
          </volist>
        </select>
      </div>
      <div class="unit">
        <label>分组属性：</label>
          <label><input type="radio" name="type" value="1" />企业</label>
          <label><input type="radio" name="type" value="2" />个人</label>
          <label><input type="radio" name="type" value="3" />政府</label>
          <label><input type="radio" name="type" value="4" />散客</label>
      </div>
      <div class="unit">
        <label>分组特权：</label>
          <label><input type="radio" name="privilege" value="1" />窗口付费+预留座位+手工排座</label>
          <label><input type="radio" name="privilege" value="2" />手工排座</label>
      </div>
      <div class="unit">
        <label>结算方式：</label>
          <label><input type="radio" name="settlement" value="1" />票面价结算(返佣)</label>
          <label><input type="radio" name="settlement" value="2" />底价结算(无返佣)</label>
      </div>
      <div class="unit">
        <label>分组补贴：</label>
          <input type="text" name="group_rebate" value="" size="5" />
      </div>
      <div class="unit">
         <label>状态：</label>
        <select name="status" class="required combox">
          <option value="1" selected>启用</option>
          <option value="0">不启用</option>
        </select>
      </div>
    </div>
    <input name="product_id" value="{$product_id}" type="hidden">
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

