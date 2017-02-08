<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageContent">
  <ul class="list-group">
    <li class="list-group-item"><label class="col-sm-4">应用名称:</label>{$data.name}</li>
    <li class="list-group-item"><label class="col-sm-4">appID:</label>{$data.appid}</li>
    <li class="list-group-item"><label class="col-sm-4">appKey:</label><?php $str = $data['appid'].$data['id'].$data['appkey'];  echo md5($str);?><a href="{:U('Manage/App/appkey',array('id'=>$data['id'],'menuid'=>$menuid));}" data-toggle="doajax" data-confirm-msg="确定要更新该应用的密钥吗？" title="更新"><i class="fa fa-refresh"></i></a></li>
    <li class="list-group-item"><label class="col-sm-4">所属商户:</label>{$data.crm_id|crmName}</li>
    <li class="list-group-item"><label class="col-sm-4">权限产品:</label><volist name="product" id="pro">{$pro|productName}</volist></li>
    <li class="list-group-item"><label class="col-sm-4">URL:</label>{$data.url}</li>
    <li class="list-group-item"><label class="col-sm-4">操作员:</label>{$data.userid|userName}</li>
    <li class="list-group-item"><label class="col-sm-4">状态:</label>{$data.status|status}</li>
    <li class="list-group-item"><label class="col-sm-4">创建时间:</label>{$data.createtime|datetime}</li>
  </ul>
</div>
