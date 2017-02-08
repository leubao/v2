  <div class="pageContent">
    <form method="post" action="{:U('Crm/Index/edit_gov',array('navTabId'=>$navTabId,'groupid'=>$groupid));}" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
      <div class="pageFormContent" layoutH="58">
        <div class="unit">
          <label>姓名：</label>
          <input type="text" name="nickname" size="30" maxlength="20" class="required" value="{$data.nickname}"/>
        </div>
        <!-- <div class="unit">
                          <label>密码类型：</label>
                          <input type="radio" name="pwdtype" value="1" checked onclick="javascript:document.getElementById('pwd1').setAttribute('readonly','true');document.getElementById('pwd2').setAttribute('readonly','true')"/>随机密码
                          <input type="radio" name="pwdtype" value="2" onclick="javascript:document.getElementById('pwd1').removeAttribute('readonly');document.getElementById('pwd2').removeAttribute('readonly');" />自定义
                        </div>
                        <div class="unit">
                          <label>自定义密码：</label>
                          <input type="password" id="pwd1" name="password1" size="30" maxlength="20" readonly='true'/>
                        </div>
                        <div class="unit">
                          <label>确认密码：</label>
                          <input type="password" id="pwd2" name="password2" size="30" maxlength="20" readonly='true' equalto="#pwd1"/>
                        </div> -->                
        <div class="unit">
          <label>性别：</label>
          <select name="sex" class="required combox">
            <option value="1" <if condition="$data['sex'] eq 1">selected</if>>男</option>
            <option value="0" <if condition="$data['sex'] eq 0">selected</if>>女</option>
          </select>      
        </div>
        <div class="unit">
          <label>相关销售：</label>
          <input type="hidden" name="orgLookup.salesman" value="{$data.salesmanname}"/>
          <input type="text" class="required" name="orgLookup.salesmanname" value="{$data.salesmanname}" suggestFields="salesmanname" suggestUrl="" lookupGroup="orgLookup" />
          <a class="btnLook" href="{:U('Crm/Index/lookup')}" lookupGroup="orgLookup">查找带回</a>      
        </div>         
        <!-- <div class="unit">
          <label>身份证号：</label>
          <input type="text" name="idnumber" size="30" maxlength="20" class="required"/>
        </div> -->
        <div class="unit">
          <label>手机号：</label>
          <input type="text" name="phone" size="30" maxlength="20" class="required phone" value="{$data.phone}"/>
        </div>
        <div class="unit">
          <label>所属角色：</label>
          <select name="role_id" class="required combox">
            <option value="" selected>请选择</option>
            <volist name="role" id="ro">
              <option value="{$ro.id}" <if condition="$data['role_id'] eq $ro['id']">selected</if>>{$ro.name}</option>
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
                <input type="hidden" value="{$id}" name="id" />
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