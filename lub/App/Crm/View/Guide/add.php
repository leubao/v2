<div class="pageContent">
  <form method="post" action="{:U('Crm/Guide/add',array('navTabId'=>$navTabId));}" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
    <div class="pageFormContent" layoutH="58">
      <div class="unit">
        <label>导游姓名：</label>
        <input type="text" name="name" size="30" maxlength="20" class="required"/>
      </div>
      <div class="unit">
        <label>性别：</label>
        <select name="status" class="required combox">
          <option value="1" selected>男</option>
          <option value="0">女</option>
        </select>      
      </div>
      <div class="unit">
        <label>身份证号：</label>
        <input type="text" name="idnumber" size="30" maxlength="20" class="required"/>
      </div>
      <div class="unit">
        <label>手机号：</label>
        <input type="text" name="mobile" size="30" maxlength="20" class="required phone"/>
      </div>
      <div class="unit">
        <label>导游类型：</label>
        <select name="type" class="required combox">
          <option value="1" selected>类型1</option>
          <option value="2">类型2</option>
          <option value="3">类型3</option>
        </select>        
      </div>
      <div class="unit">
        <label>识别ID：</label>
        <input type="text" name="identifyid" size="30" maxlength="20" class="required"/>        
      </div>            
      <div class="unit">
        <label>银行账号：</label>
        <input type="text" name="bank_account" size="30" maxlength="20" class="required number"/>
      </div>
      <div class="unit">
        <label>开户银行：</label>
        <input type="text" name="bank" size="30" maxlength="20"/>
      </div>
      <div class="unit">
        <label>微信：</label>
        <input type="text" name="wechat" size="30" maxlength="20" />
      </div>
      <div class="unit">
        <label>微博：</label>
        <input type="text" name="weibo" size="30" maxlength="20" />
      </div>
      <div class="unit">
        <label>邮箱：</label>
        <input type="text" name="email" size="30" maxlength="20" class="email"/>
      </div>
      <div class="unit">
        <label>导游证号：</label>
        <input type="text" name="cardid" size="30" maxlength="20" class="required"/>
      </div>                  
      <div class="unit">
         <label>状态：</label>
        <select name="status" class="required combox">
          <option value="1" selected>启用</option>
          <option value="0">不启用</option>
        </select>
      </div>
    </div>
    <div class="formBar">
      <ul>
        <li>
          <div class="buttonActive">
            <div class="buttonContent">
              <input type="hidden" value="{$groupid}" name="groupid" />
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