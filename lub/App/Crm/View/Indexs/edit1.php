<div class="pageContent">
  <form method="post" action="{:U('Crm/Index/edit',array('navTabId'=>$navTabId));}" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
    <div class="pageFormContent" layoutH="58">
      <div class="unit">
        <label>客户名称：</label>
        <input type="text" name="name" size="30" maxlength="20" class="required" value="{$data.name}"/>
      </div>
      <div class=""></div>
      <div class="unit">
        <label>地址：</label>
        <input type="text" name="address" size="30" maxlength="20" class="required" value="{$data.address}"/>
      </div>
      <div class="unit">
        <label>税号：</label>
        <input type="text" name="tariff" size="30" maxlength="20" class="required number" value="{$data.tariff}"/>
      </div>
      <!-- <div class="unit">
        <label>银行账号：</label>
        <input type="text" name="bank_account" size="30" maxlength="20" class="required number" value="{$data.bank_account}"/>
      </div>
      <div class="unit">
        <label>开户银行：</label>
        <input type="text" name="bank" size="30" maxlength="20" value="{$data.bank}"/>
      </div> -->
      <div class="unit">
        <label>联系人：</label>
        <input type="text" name="contacts" size="30" maxlength="20" class="required" value="{$data.contacts}"/>
      </div>
      <div class="unit">
        <label>联系人电话：</label>
        <input type="text" name="phone" size="30" maxlength="20" class="required" value="{$data.phone}"/>
      </div> 
      <div class="unit">
        <label>销售配额：</label>
        <input type="text" name="quota" size="30" maxlength="20" class="required" value="{$data.quota}"/>
      </div>     
      <div class="unit">
        <label>相关销售：</label>
        <input type="hidden" name="orgLookup.salesman" value="{$data.salesman}"/>
        <input type="text" class="required" name="orgLookup.salesmanname" value="{$data.salesmanname}" suggestFields="salesmanname" suggestUrl="" lookupGroup="orgLookup" />
        <a class="btnLook" href="{:U('Crm/Index/lookup')}" lookupGroup="orgLookup">查找带回</a>      
      </div>      
      <div class="unit">
         <label>状态：</label>
        <select name="status" class="required combox">
          <option value="1" <if condition="$data['status'] eq 1">selected</if>>启用</option>
          <option value="0" <if condition="$data['status'] eq 0">selected</if>>不启用</option>
        </select>
      </div>
    </div>
    <div class="formBar">
      <ul>
        <li>
          <div class="buttonActive">
            <div class="buttonContent">
              <input name="id" value="{$id}" type="hidden"> 
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

