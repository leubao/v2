<form class="form-horizontal" action="{:U('Item/Product/auth',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
	<table class="table table-striped table-bordered">
	    <tbody>
	      <tr>
	        <td width="100px">当前计划</td>
	        <td><strong>{$data.id|planShow}</strong></td>
	      </tr>
	      
	      <tr>
	        <td width="100px">销售场景</td>
	        <td>
	        <input type="checkbox" name="wechat" value="1" <eq name="data['param']['sales']['wechat']" value="1"> checked</eq>> 微信/官网
		    <input type="checkbox" name="channel" value="3" <eq name="data['param']['sales']['channel']" value="3"> checked</eq>> 渠道版
		    <input type="checkbox" name="window" value="4" <eq name="data['param']['sales']['window']" value="4"> checked</eq>> 窗口
		    <input type="checkbox" name="api" value="5" <eq name="data['param']['sales']['api']" value="5"> checked</eq>> API接口
		    <input type="checkbox" name="ifhelp" value="6" <eq name="data['param']['sales']['ifhelp']" value="6"> checked</eq>> 自助机</td>
	      </tr>
	      <tr>
	      	<td width="100px">微信/官网价格组</td>
	        <td><select name="price_group" class="required" data-toggle="selectpicker">
			        <option selected value="0">===请选择===</option>
			        <volist name="price" id="vo">
			          <option value="{$vo.id}" <if condition="$data['param']['price_group'] eq $vo['id']">selected</if>>{$vo.name}</option>
			        </volist>
			      </select></td>
	      </tr>
	      <tr>
	        <td width="100px">说明</td>
	        <td style="color: red;"><strong>设置相应场景可售数量,不限定数量默认值即可</strong></td>
	      </tr>
	      <tr>
	        <td width="100px">微信/官网</td>
	        <td>
	        <volist name="data['param']['seat']" id="vo">
			{$vo|areaName}<input type="text" name="wechat_num[{$vo}]" value="{$data['param']['wechat'][$vo]}" size="5">
			</volist>
		    </td>
	      </tr>
	      <!--
	      <tr>
	        <td width="100px">API</td>
	        <td>
	        <volist name="data['param']['seat']" id="vo">
			{$vo|areaName}<input type="text" name="api_num[]" value="0" size="5">
			</volist></td>
	      </tr>
	      <tr>
	        <td width="100px">自助机</td>
	        <td>
	        <volist name="data['param']['seat']" id="vo">
			{$vo|areaName}<input type="text" name="help_num[]" value="0" size="5">
			</volist></td>
	      </tr>
	      <tr>
	        <td width="100px">说明</td>
	        <td style="color: red;"><strong>设置出票场景,在不确定正常演出时,建议关闭渠道版出票和自助机出票</strong></td>
	      </tr>
	      <tr>
	        <td width="100px">出票设置</td>
	        <td>
	        <input type="checkbox" name="window_print" value="1" checked readonly> 窗口出票
		    <input type="checkbox" name="help_print" value="1" <eq name="data['help_print']" value="1"> checked</eq>> 自助机出票
		    <input type="checkbox" name="channel_print" value="1" <eq name="data['channel_print']" value="1"> checked</eq>> 渠道版出票
		    </td>
	      </tr>-->
	      <tr>
	        <td width="100px">参加活动</td>
	        <td>
	        <volist name="activity" id="act">
		    <input type="checkbox" name="activity[]" value="{$act.id}" <if condition="in_array($act['id'],$data['param']['activity'])"> checked </if>> {$act.title}
		    </volist>
		    </td>
	      </tr>
      	</tbody>
    </table>
  </div>
  <input name="id" value="{$data.id}" type="hidden">
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