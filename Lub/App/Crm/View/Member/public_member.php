<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageContent">
<table class="table table-striped table-bordered table-hover">
      <tbody>
        <tr>
          <td width="100px">姓名</td>
          <td width="500px">{$data.nickname}</td> 
          <td width="100px">创建日期</td>
          <td>{$data.createtime|datetime}</td>
        </tr>
        <tr>
          <td>身份证:</td>
          <td>{$data.idcard}</td>
          <td>手机号:</td>
          <td>{$data.phone}</td>
        </tr>
        <tr>
          <td>入园次数</td>
          <td>{$data.number}</td>
          <td>最后入园时间</td>
          <td>{$data.update_time|datetime}</td>
        </tr>
        <tr>
          <td>办理方式</td>
          <td><if condition="$data['source'] eq 5"> 自助办理 <else /> 窗口办理 </if></td>
          <td>状态</td>
          <td>{$data.status|status}</td> 
        </tr>
        <tr>
          <td>会员号</td>
          <td>{$data.no-number}</td> 
          <td>微信openid</td>
          <td>{$data.openid}</td> 
        </tr>
        <tr>
          <td>备注</td>
          <td colspan="3">{$data.remark}</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<div class="bjui-pageFooter">
  <ul>
    <li>
      <button type="button" class="btn-close" data-icon="close">关闭</button>
    </li>
  </ul>
</div>