<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title" id="myModalLabel">信息维护</h4>
</div>
<div class="modal-body">
  <div class="panel panel-default cler_mag_20"> 
      <div class="panel-body">
        <dl class="dl-horizontal">
          <dt>用户名:</dt><dd> {$data.username}</dd>
          <dt>姓名:</dt><dd> {$data.nickname}</dd>
          <dt>电话:</dt><dd> {$data.phone}</dd>
          <dt>Email:</dt><dd> {$data.email}</dd>
          <dt>所属角色:</dt><dd> <?php echo D('Home/Role')->getRoleIdName($data['role_id'])?></dd>
          <dt>商户:</dt><dd> {$data.item_id|itemName}</dd>
          <dt>产品:</dt><dd> {$data.product|product_name}</dd>
          <dt>默认产品:</dt><dd> {$data.defaultpro|product_name}</dd>
          <dt>支付方式:</dt><dd> <if condition="$data['is_pay'] eq 1">授信额
                              <elseif condition="$data['is_pay'] eq 2"/>网银支付
                              <else />授信+网银</if></dd>
          <dt>状态:</dt><dd><if condition="$data['status'] eq 1"><span class="label label-success">启用</span>
                              <else />
                              <span class="label label-danger">停用</span></if></dd>
          <dt>备注:</dt><dd> {$data.info}</dd>
        </dl>
      </div>
      
  </div>
</div>