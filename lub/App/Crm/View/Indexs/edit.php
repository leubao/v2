<div class="pageContent">
  <form method="post" action="{:U('Crm/Index/edit',array('navTabId'=>$navTabId));}" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
    <div class="pageFormContent" layoutH="58">
      <div class="unit">
        <label>客户名称：</label>
        <input type="text" name="name" size="30" maxlength="20" class="required" value="{$data.name}"/>
      </div>
      <div class="unit">
        <label>所属类型：</label>
        <input type="checkbox" name="type" value="1" <eq name="data['type']" value="1"> checked</eq>>旅行社
        <input type="checkbox" name="type" value="2" <eq name="data['type']" value="2"> checked</eq>>酒店
        <input type="checkbox" name="type" value="3" <eq name="data['type']" value="3"> checked</eq>>电商
        <input type="checkbox" name="type" value="4" <eq name="data['type']" value="4"> checked</eq>>代售点
        <input type="checkbox" name="type" value="5" <eq name="data['type']" value="5"> checked</eq>>其它
      </div>
      <div class="unit">
        <label>地址：</label>
        <input type="text" name="address" size="30" maxlength="20" class="required" value="{$data.address}"/>
      </div>
      <div class="unit">
        <label>银行账号：</label>
        <input type="text" name="bank_account" size="30" maxlength="20" class="number" value="{$data.bank_account}"/>
      </div>
      <div class="unit">
        <label>开户银行：</label>
        <input type="text" name="bank" size="30" maxlength="20" value="{$data.bank}"/>
      </div>
      <div class="unit">
        <label>联系人：</label>
        <input type="text" name="contacts" size="30" maxlength="20" class="required" value="{$data.contacts}"/>
      </div>
      <div class="unit">
        <label>联系人电话：</label>
        <input type="text" name="phone" size="30" maxlength="20" class="required phone" value="{$data.phone}" />
      </div>
      <div class="unit">
        <label>管理员：</label>
        <input type="hidden" name="orgLookup.salesman" value="{$data.salesman}"/>
        <input type="text" class="required" name="orgLookup.salesmanname" value="{$data.salesman|userName}" suggestFields="salesmanname" suggestUrl="" lookupGroup="orgLookup" disabled="" />
        <a class="btnLook" href="{:U('Crm/Index/lookup')}" lookupGroup="orgLookup">查找管理员</a>      
      </div>
      <if condition="$proconf['agent'] eq '1'">
      <div class="unit">
        <label>代理商级别：</label>
        <select name="level" class="required combox">
          <volist name="level" id="v">
          <option value="{$v.id}" <if condition="$data['level'] eq $v['id']">selected</if>>{$v.name}</option>
          </volist> 
        </select>
      </div> 
      </if>
      <div class="unit">
        <label>所属分组：</label>
        <select name="groupid" class="required combox">
        <volist name="group" id="vo">
          <if condition="$type eq $vo['type']">
          <option value="{$vo.id}" <if condition="$data['groupid'] eq $vo['id']">selected</if>>{$vo.name}</option>
          </if>
          </volist>
        </select>
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
              <input name="id" value="{$data.id}" type="hidden">	
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