<if condition="$type eq 1">
  <div class="pageContent">
    <form method="post" action="{:U('Crm/Index/edit',array('navTabId'=>$navTabId));}" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
      <div class="pageFormContent" layoutH="58">
        <div class="unit">
          <label>客户名称：</label>
          <input type="text" name="name" size="30" maxlength="20" class="required" value="{$data.name}"/>
        </div>
        <div class=""></div>
        <!-- <div class="unit">
          <label>地址：</label>
          <input type="text" name="address" size="30" maxlength="20" class="required"/>
        </div> 
        <div class="unit">
          <label>税号：</label>
          <input type="text" name="tariff" size="30" maxlength="20" class="required number"/>
        </div>
        <div class="unit">
          <label>银行账号：</label>
          <input type="text" name="bank_account" size="30" maxlength="20" class="required number"/>
        </div>
        <div class="unit">
          <label>开户银行：</label>
          <input type="text" name="bank" size="30" maxlength="20"/>
        </div>-->
        <div class="unit">
          <label>联系人：</label>
          <input type="text" name="contacts" size="30" maxlength="20" class="required" value="{$data.contacts}"/>
        </div>
        <div class="unit">
          <label>联系人电话：</label>
          <input type="text" name="phone" size="30" maxlength="20" class="required phone" value="{$data.phone}"/>
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
<else/> 
  <div class="pageContent">
    <form method="post" action="{:U('Crm/Index/editusers',array('navTabId'=>$navTabId));}" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
      <div class="pageFormContent" layoutH="58">
        <div class="unit">
          <label>姓名：</label>
          <input type="text" name="name" size="30" maxlength="20" class="required" value="{$data.nickname}"/>
        </div>
        <div class="unit">
          <label>性别：</label>
          <select name="sex" class="required combox">
            <option value="1" <if condition="$data['sex'] eq 1">selected</if>>男</option>
            <option value="0" <if condition="$data['sex'] eq 0">selected</if>>女</option>
          </select>      
        </div>
        <div class="unit">
          <label>身份证号：</label>
          <input type="text" name="idnumber" size="30" maxlength="20" class="required" value="{$data.idnumber}"/>
        </div>
        <div class="unit">
          <label>手机号：</label>
          <input type="text" name="phone" size="30" maxlength="20" class="required phone" value="{$data.phone}"/>
        </div>
        <div class="unit">
          <label>导游类型：</label>
          <select name="type" class="combox">
            <option value=""  <if condition="$data['type'] eq ''">selected</if>>请选择</option>
            <option value="1" <if condition="$data['type'] eq 1">selected</if>>类型1</option>
            <option value="2" <if condition="$data['type'] eq 2">selected</if>>类型2</option>
            <option value="3" <if condition="$data['type'] eq 3">selected</if>>类型3</option>
          </select>(选填内容,不是导游可不填)        
        </div>
        <!-- <div class="unit">
          <label>识别ID：</label>
          <input type="text" name="identifyid" size="30" maxlength="20" class="required" value="{$data.identifyid}"/>        
        </div>            
        <div class="unit">
          <label>银行账号：</label>
          <input type="text" name="bank_account" size="30" maxlength="20" class="required number" value="{$data.bank_account}"/>
        </div>
        <div class="unit">
          <label>开户银行：</label>
          <input type="text" name="bank" size="30" maxlength="20" value="{$data.bank}"/>
        </div> -->
        <div class="unit">
          <label>微信：</label>
          <input type="text" name="wechat" size="30" maxlength="20" value="{$data.wechat}"/>
        </div>
        <div class="unit">
          <label>微博：</label>
          <input type="text" name="weibo" size="30" maxlength="20" value="{$data.weibo}"/>
        </div>
        <div class="unit">
          <label>邮箱：</label>
          <input type="text" name="email" size="30" maxlength="20" class="email" value="{$data.email}"/>
        </div>
        <div class="unit">
          <label>导游证号：</label>
          <input type="text" name="cardid" size="30" maxlength="20" value="{$data.cardid}"/>(选填内容，不是导游可不填)
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
</if>


