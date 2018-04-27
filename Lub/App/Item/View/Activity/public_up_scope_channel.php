<form class="form-horizontal" action="{:U('Item/Activity/public_up_scope_channel',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
    <table id="up_scope_channel" class="table table-bordered table-hover table-striped table-top" data-toggle="tabledit" data-initnum="0" data-action="#" data-single-noindex="true">
        <thead>
            <tr data-idname="channel[#index#][id]">
                
                
                <th title="渠道商" align="center">
                  
                  <input type="text" name="channel[#index#][name].name" value="" value="{$channel_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_channel',array('ifadd'=>1));}" data-group="channel[#index#][name]" data-width="600" data-height="445" data-title="渠道商" placeholder="渠道商">
                </th>
                <th title="ID"><input type="text" name="channel[#index#][name].id" data-rule="required" value="" size="5"></th>
                <th title="是否参与">
                    <input type="radio" name="channel[#index#][scope]" data-toggle="icheck" value="1" checked data-label="是">
                    <input type="radio" name="channel[#index#][scope]" data-toggle="icheck" value="0" data-label="否">
                </th>
                <th title="" data-addtool="true" width="100">
                    <a href="javascript:;" class="btn btn-red row-del" data-confirm-msg="确定要删除该行信息吗？">删</a>
                </th>
            </tr>
        </thead>
        <tbody>
          <volist name="data.param.info.scope.ginseng" id="vo">
            <tr>
              <td data-val="{$vo|crmName}"></td>
              <td data-val="{$vo}"></td>
              <td data-val="1"></td>
              <td data-original="true">--</td>
            </tr>
          </volist>

          <volist name="data.param.info.scope.dont" id="vos">
            <tr>
              <td data-val="{$vos|crmName}"></td>
              <td data-val="{$vos}"></td>
              <td data-val="0"></td>
              <td data-original="true">--</td>
            </tr>
          </volist>

        </tbody>
    </table>
  </div>
  <input name="id" value="{$data.id}" type="hidden">
  <input name="type" value="{$data.type}" type="hidden">
  <input type="hidden" name="number" value="{$data.param.info.number}">
  <input type="hidden" name="ticket_id" value="{$data.param.info.ticket}">
  <div class="bjui-pageFooter">
    <ul>
      <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
      <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
  </div>
</form>