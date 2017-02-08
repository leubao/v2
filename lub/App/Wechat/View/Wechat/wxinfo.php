<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageContent">
  <ul class="list-group">
    <li class="list-group-item"><label class="col-sm-4">微信名称:</label>{$data.name}</li>
    <li class="list-group-item"><label class="col-sm-4">微信号:</label>{$data.wxid}</li>
    <li class="list-group-item"><label class="col-sm-4">appID:</label>{$data.appid}</li>
    <li class="list-group-item"><label class="col-sm-4">appsecret:</label>{$data.name}</li>
    <li class="list-group-item"><label class="col-sm-4">Token:</label>{$data.appsecret}</li>
    <li class="list-group-item"><label class="col-sm-4">encodingASEKey:</label>{$data.encodingaeskey}</li>
    <li class="list-group-item"><label class="col-sm-4">公众账号:</label>{$data.account}</li>
    <li class="list-group-item"><label class="col-sm-4">URL:</label>{$data.wxurl}</li>
    <li class="list-group-item"><label class="col-sm-4">类型:</label>{$data['type']|wechat_type}</li>
    <li class="list-group-item"><label class="col-sm-4">状态:</label>{$data.status|status}</li>
  </ul>
</div>
