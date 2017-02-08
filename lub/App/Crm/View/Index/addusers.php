  <div class="pageContent">
    <form method="post" action="{:U('Crm/Index/addusers',array('navTabId'=>$navTabId,'groupid'=>$groupid));}" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
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
          <label>导游类型：</label>
          <select name="type" class="combox">
            <option value="" selected>请选择</option>
            <option value="1">类型1</option>
            <option value="2">类型2</option>
            <option value="3">类型3</option>
          </select>(选填内容,不是导游可不填)        
        </div>
        <!-- <div class="unit">
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
        </div> -->
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
          <input type="text" name="cardid" size="30" maxlength="20"/>(选填内容，不是导游可不填)
        </div>
        <div class="unit">
          <label>所属角色：</label>
          <select name="role_id" class="required combox">
            <option value="" selected>请选择</option>
            <volist name="role" id="ro">
              <option value="{$ro.id}">{$ro.name}</option>
            </volist>
          </select>
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
                <input type="hidden" value="{$cid}" name="cid" />
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