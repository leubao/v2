<div class="bjui-pageHeader">
<!--工具条 s-->
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="" method="post">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <input type="text" value="" name="sn" class="form-control" size="20" placeholder="封包标号">&nbsp;
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>
<div class="bjui-pageContent tableContent ">
<form class="form-horizontal" action="" method="post" data-toggle="validate">
  <table class="table table-bordered table-hover mb25 w900">
  <thead>
    <tr>
      <th>序号</th>
      <th>机构号 </th>
      <th>机构名称  </th>
      <th>客户名称  </th>
      <th>借据编号  </th>
      <th>贷款金额  </th>
      <th>贷款发放日期  </th>
      <th>原档案编号 </th>
      <th>档案状态</th>
    </tr>
  </thead>
  <tbody>
  <!-- foreach s-->
      <tr>
        <td>{$i}</td>
        <td>{$vo.priceid|ticketName}</td>
        <td>{$vo.price}</td>
        <td>{$vo.discount}</td>
        <td>{$vo.areaId|areaName}</td>
        <td>{$vo.seatid|seatShow}</td>
        <td>{$vo.seatid|seatOrder=$data['plan_id']}</td>
        <td>{$vo.seatid|seatOrder=$data['plan_id']}</td>
        <td>{$vo.seatid|seatOrder=$data['plan_id']}</td>
      </tr>
<!-- foreach e-->
      <tr>
        <td>反馈</td>
        <td colspan="8">
          <select name="type" data-toggle="selectpicker">
            <option value="1">同意</option>
            <option value="2">拒绝</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>拒绝理由</td>
        <td colspan="8"><textarea name="win_rem" cols="55" rows="2"></textarea></td>
      </tr>
  </tbody>
  </table>
  </div>
</div>
<input type="hidden" value="" name="id"></input>
<div class="bjui-pageFooter">
  <ul>
    <li>
      <button type="button" class="btn-close" data-icon="close">关闭</button>
    </li>
    <li>
      <button type="submit" class="btn-default" data-icon="save">提交</button>
    </li>
  </ul>
</div>
</form>