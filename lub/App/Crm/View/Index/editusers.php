  <div class="pageContent">
    <form method="post" action="{:U('Crm/Index/editusers',array('navTabId'=>$navTabId));}" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
      <div class="pageFormContent" layoutH="58">
        <div class="unit">
          <label>姓名：</label>
          <input type="text" name="nickname" size="30" maxlength="20" class="required" value="{$data.nickname}"/>
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