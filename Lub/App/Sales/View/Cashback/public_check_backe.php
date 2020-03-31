<?php if (!defined('LUB_VERSION')) exit(); dump($data);?>
<div class="bjui-pageContent">
  <table class="table table-striped table-bordered table-hover">
  <tbody>
    <tr>
      <td width="100px">单号:</td>
      <td>{$data.mch_billno}</td>
      <td width="100px">红包单号:</td>
      <td>{$data.detail_id}</td>
    </tr>
    <tr>
      <td>金额</td>
      <td><?php echo $data['total_amount']/100 ?></td>
      <td>红包数量</td>
      <td>{$data.total_num}</td>
    </tr>
    <tr>
      <td>状态</td>
      <td>{$data.status}</td>
      <td>红包类型</td>
      <td>{$data.hb_type}</td>
    </tr>
    <tr>
      <td>说明</td>
      <td colspan="3">{$data.reason}</td>
    </tr>
  </tbody>
</table>
</div>
<div class="bjui-pageFooter">
  <ul>
    <li>
      <button type="button" class="btn-close" data-icon="close">取消</button>
    </li>
  </ul>
</div>