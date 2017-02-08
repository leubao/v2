<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title" id="myModalLabel">渠道销售详情</h4>
</div>
<div class="modal-body">
  <table class="table table-hover table-bordered">
        <colgroup>
        <col>
        <col>
        </colgroup>
        <thead>
          <tr>
            <td align="center">渠道商名称</td>
            <td align="center">已售数</td>
          </tr>
        </thead>
        <tbody>
        <volist name="data" id="vo">
        	<tr>
            <td align="center">{$vo.channel_id|crmName}</td>
            <td align="center">{$vo.number}</td>
          </tr>
          </volist>
        </tbody>
      </table>
</div>
