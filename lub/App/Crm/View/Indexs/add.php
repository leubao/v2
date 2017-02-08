<div class="pageContent">
  <if condition="$type eq '1'">
  <form method="post" action="{:U('Crm/Index/add',array('navTabId'=>$navTabId));}" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
    <div class="pageFormContent" layoutH="58">
      <div class="unit">
        <label>商户名称：</label>
        <input type="text" name="name" size="30" maxlength="20" class="required"/>
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
        <input type="text" name="address" size="30" maxlength="20" class="required"/>
      </div>
      <div class="unit">
        <label>银行账号：</label>
        <input type="text" name="bank_account" size="30" maxlength="20" class="number"/>
      </div>
      <div class="unit">
        <label>开户银行：</label>
        <input type="text" name="bank" size="30" maxlength="20"/>
      </div>
      <div class="unit">
        <label>联系人：</label>
        <input type="text" name="contacts" size="30" maxlength="20" class="required"/>
      </div>
      <div class="unit">
        <label>联系人电话：</label>
        <input type="text" name="phone" size="30" maxlength="20" class="required phone"/>
      </div>
      <div class="unit">
        <label>销售配额：</label>
        <input type="text" name="quota" size="30" maxlength="20" class="required" value="150"/>
      </div>
      <if condition="$proconf['agent'] eq '1'">
      <div class="unit">
        <label>代理商级别：</label>
        <select name="level" class="required combox">
        	<volist name="level" id="v">
          <option value="{$v.id}" selected>{$v.name}</option>
          </volist> 
        </select>
      </div>
      <else />
      <input type="hidden" name="level" value="{$Config['level_1']}">
      </if> 
      <div class="unit">
        <label>管理员：</label>
        <input type="hidden" name="orgLookup.salesman" value=""/>
        <input type="text" class="required" name="orgLookup.salesmanname" value="" suggestFields="salesmanname" suggestUrl="" lookupGroup="orgLookup" disabled="" />
        <a class="btnLook" href="{:U('Crm/Index/lookup')}" lookupGroup="orgLookup">查找管理员</a>
               
      </div>
      <div class="unit">
         <label>状态：</label>
        <select name="status" class="required combox">
          <option value="1" selected>启用</option>
          <option value="0">不启用</option>
        </select>
      </div>
    </div>
    <else />
    <form method="post" action="{:U('Crm/Index/addusers',array('navTabId'=>$navTabId));}" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
      <div class="pageFormContent" layoutH="58">
        <div class="unit">
          <label>姓名：</label>
          <input type="text" name="nickname" size="30" maxlength="20" class="required"/>
          <span class="info">务必填写真实姓名</span>
        </div>
        <div class="unit">
          <label>用户名：</label>
          <input type="text" name="username" size="30" maxlength="20" class="required" remote="{:U('Item/Check/public_check_name',array('ta'=>17));}"/>
          <span class="info">用于填写登录用户名</span>
        </div>
        <div class="unit">
          <label>密码类型：</label>
          <input type="radio" name="pwdtype" value="1" checked onclick="javascript:document.getElementById('pwd1').setAttribute('readonly','true');document.getElementById('pwd2').setAttribute('readonly','true')"/>随机密码
          <input type="radio" name="pwdtype" value="2" onclick="javascript:document.getElementById('pwd1').removeAttribute('readonly');document.getElementById('pwd2').removeAttribute('readonly');" />自定义
          <span class="info">默认密码为123456</span>
        </div>
        <div class="unit">
          <label>自定义密码：</label>
          <input type="password" id="pwd1" name="password1" size="30" maxlength="20" readonly='true' minlength="6" />
        </div>
        <div class="unit">
          <label>确认密码：</label>
          <input type="password" id="pwd2" name="password2" size="30" maxlength="20" readonly='true' minlength="6" equalto="#pwd1"/>
        </div>                
        <div class="unit">
          <label>性别：</label>
          <select name="sex" class="required combox">
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
          <input type="text" name="phone" size="30" maxlength="20" class="required phone"/>
        </div>         
        <div class="unit">
          <label>银行账号：</label>
          <input type="text" name="bank_account" size="30" maxlength="20" class="number"/>
        </div>
        <div class="unit">
          <label>开户银行：</label>
          <input type="text" name="bank" size="30" maxlength="20"/>
        </div>
        <!--
        <div class="unit">
          <label>微信：</label>
          <input type="text" name="wechat" size="30" maxlength="20" />
        </div>
        <div class="unit">
          <label>微博：</label>
          <input type="text" name="weibo" size="30" maxlength="20" />
        </div>-->
        <div class="unit">
          <label>邮箱：</label>
          <input type="text" name="email" size="30" maxlength="20" class="email"/>
        </div>
        <div class="unit">
          <label>导游证号：</label>
          <input type="text" name="cardid" size="30" maxlength="20"/>(选填内容，不是导游可不填)
        </div>
        <div class="unit">
        <label>相关销售：</label>
          <input type="hidden" name="orgLookup.salesman" value=""/>
          <input type="text" class="required" name="orgLookup.salesmanname" value="" suggestFields="salesmanname" suggestUrl="" lookupGroup="orgLookup" disabled="" />
          <a class="btnLook" href="{:U('Crm/Index/lookup')}" lookupGroup="orgLookup">查找管理员</a>      
        </div>
                       
        <div class="unit">
           <label>状态：</label>
          <select name="status" class="required combox">
            <option value="1" selected>启用</option>
            <option value="0">不启用</option>
          </select>
        </div>
      </div>
    </if>
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