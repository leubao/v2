<form class="form-horizontal" action="{:U('Crm/Index/groupedit',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
    <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <td width="100px">分组名称:</td>
          <td><input type="text" name="name" size="30" maxlength="20" value="{$data.name}" class="required"/></td>
        </tr>
        <tr>
          <td width="100px">价格政策:</td>
          <td><volist name="price" id="vo">
          <input type="checkbox" name="price_group[]" value="{$vo.id}" <if condition="in_array($vo['id'],explode(',',$data['price_group']))">checked</if>> {$vo.product_id|productName}-{$vo.name}<br>
          </volist></td>
        </tr>
        <tr>
          <td width="100px">分组属性:</td>
          <td>
            <input type="radio" name="type" value="1" <eq name="data['type']" value="1">checked="checked"</eq> data-toggle="icheck" data-label="企业" />
            <input type="radio" name="type" value="3" <eq name="data['type']" value="3">checked="checked"</eq> data-toggle="icheck" data-label="政府" />
            <input type="radio" name="type" value="4" <eq name="data['type']" value="4">checked="checked"</eq> data-toggle="icheck" data-label="个人" />
          </td>
        </tr>
        <tr>
          <td width="100px">分组特权:</td>
          <td><input type="radio" name="privilege" value="1" data-toggle="icheck" <eq name="data['privilege']" value="1">checked="checked"</eq> data-label="窗口付费+预留座位+手工排座" />
        <input type="radio" name="privilege" value="2" data-toggle="icheck" <eq name="data['privilege']" value="2">checked="checked"</eq> data-label="手工排座" /></td>
        </tr>
        <tr>
          <td width="100px">结算方式:</td>
          <td><input type="radio" name="settlement" value="1" data-toggle="icheck" <eq name="data['settlement']" value="1">checked="checked"</eq> data-label="票面价结算(返佣)" />
          <input type="radio" name="settlement" value="3" data-toggle="icheck" <eq name="data['settlement']" value="3">checked="checked"</eq> data-label="结算价格(返佣)" />
          <input type="radio" name="settlement" value="2" data-toggle="icheck" <eq name="data['settlement']" value="2">checked="checked"</eq> data-label="底价结算(无返佣)" /></td>
        </tr>
        <tr>
          <td width="100px">单笔最小:</td>
          <td><input type="text" name="group_quota" value="{$data.param.quota}" size="5" />
              <span class="fun_tips">单笔订单最少预定量</span></td>
        </tr>
        <tr>
          <td width="100px">状态:</td>
          <td><select name="status" class="required combox"><option value="1" <if condition="$data['status'] eq 1">selected</if>>启用</option>
          <option value="0" <if condition="$data['status'] eq 0">selected</if>>不启用</option></select></td>
          </tr>
        </tbody>
    </table>
  </div>
  <input name="id" value="{$id}" type="hidden">
  <div class="bjui-pageFooter">
    <ul>
      <li>
        <button type="button" class="btn-close" data-icon="close">取消</button>
      </li>
      <li>
        <button type="submit" class="btn-default" data-icon="save">保存</button>
      </li>
    </ul>
  </div>
</form>