<div class="pageContent">
  <form method="post" action="{:U('Crm/Index/groupedit',array('navTabId'=>$navTabId));}" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
    <div class="pageFormContent" layoutH="58">
      <div class="unit">
        <label>分组名称：</label>
        <input type="text" value="{$data.name}" name="name" size="30" maxlength="20" class="required" />
      </div>
      <div class="unit">
         <label>价格政策：</label>
        <select name="price_group" class="required combox">
          <option selected value="0">===请选择===</option>
          <volist name="price" id="vo">
            <option value="{$vo.id}" <if condition="$data['price_group'] eq $vo['id']">selected</if>>{$vo.name}</option>
          </volist>
        </select>
      </div>
      <div class="unit">
        <label>结算方式：</label>
          <label><input type="radio" name="settlement" value="1" <eq name="data['settlement']" value="1">checked="checked"</eq>/>票面价结算(返佣)</label>
          <label><input type="radio" name="settlement" value="2" <eq name="data['settlement']" value="2">checked="checked"</eq>/>底价结算(无返佣)</label>
      </div>
      <div class="unit">
        <label>分组属性：</label>
          <label><input type="radio" name="type" value="1" <eq name="data['type']" value="1">checked="checked"</eq> />企业</label>
          <label><input type="radio" name="type" value="2" <eq name="data['type']" value="2">checked="checked"</eq> />个人</label>
          <label><input type="radio" name="type" value="3" <eq name="data['type']" value="3">checked="checked"</eq> />政府</label>
          <label><input type="radio" name="type" value="4" <eq name="data['type']" value="4">checked="checked"</eq> />散客</label>
      </div>
      <div class="unit">
        <label>分组特权：</label>
          <label><input type="radio" name="privilege" value="1" <eq name="data['privilege']" value="1">checked="checked"</eq>/>窗口付费</label>
          <label><input type="radio" name="privilege" value="2" <eq name="data['privilege']" value="2">checked="checked"</eq>/>预留座位</label>
          <label><input type="radio" name="privilege" value="3" <eq name="data['privilege']" value="3">checked="checked"</eq>/>手工排座</label>
      </div>
      <div class="unit">
        <label>补贴：</label>
          <label><input type="text" name="weeks_rebate" value="{$data['weeks_rebate']}" size="5" />周补</label>
          <label><input type="text" name="month_rebate" value="{$data['month_rebate']}" size="5"  />月补</label>
          <label><input type="text" name="year_rebate" value="{$data['year_rebate']}" size="5"  />年补</label>
      </div>
      <div class="unit">
         <label>状态：</label>
        <select name="status" class="required combox">
          <option value="1" <if condition="$data['status'] eq 1">selected</if>>启用</option>
          <option value="0" <if condition="$data['status'] eq 0">selected</if>>不启用</option>
        </select>
      </div>
    </div>
    <input name="product_id" value="{$product_id}" type="hidden">
    <input name="id" value="{$id}" type="hidden">
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

