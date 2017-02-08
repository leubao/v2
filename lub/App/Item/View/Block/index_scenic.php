<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader"> 
  <!--工具条 s-->
  <Managetemplate file="Common/Nav"/>
  <!--帮助 说明-->

</div>
<div class="bjui-pageContent tableContent">
  <table id="block_scenic" class="table table-bordered table-hover table-striped table-top" data-toggle="tabledit" data-initnum="0" data-action="#" data-single-noindex="true">
            <thead>
                <tr data-idname="plan[#index#][id]">
                    <th title="No."><input type="text" name="plan[#index#][no]" class="no" data-rule="required" value="1" size="2"></th>
                    
                    <th title="开始时间"><input type="text" name="plan[#index#][starttime]" data-pattern='HH:mm:ss' data-rule="required" class="j_custom_issuedate" data-toggle="datepicker" value="" size="10"></th>
                    <th title="结束时间"><input type="text" name="plan[#index#][endtime]" data-pattern='HH:mm:ss' data-rule="required" class="j_custom_indate"  data-toggle="datepicker" value="{$proconf.plan_end_time}" size="10"></th>
                    <th title="销售配额"><input type="text" name="plan[#index#][quotas]" data-rule="required" value="{$proconf.quotas}" size="5"></th>
                    <th title="渠道配额"><input type="text" name="plan[#index#][quota]" data-rule="required" value="{$proconf.quota}" size="5"></th>
                    <th title="工具类型"><select name="plan[#index#][tooltype]" data-toggle="selectpicker">
                      <option value="0">===请选择===</option>
                      <volist name="tooltype" id="vo">
                        <option value="{$vo.id}">{$vo.title}</option>
                      </volist>
                    </select>
                    </th>
                    
                    
                    
                    <th title="" data-addtool="true" width="100">
                        <a href="javascript:;" class="btn btn-red row-del" data-confirm-msg="确定要删除该行信息吗？">预留</a>
                        <a href="javascript:;" class="btn btn-red row-del" data-confirm-msg="确定要删除该行信息吗？">释放</a>
                    </th>
                </tr>
            </thead>
            <tbody>
              
            </tbody>
        </table>
</div>
<script type="text/javascript">

</script>