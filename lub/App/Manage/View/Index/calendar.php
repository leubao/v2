<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader"> 
  <!--工具条 s-->
  <div class="toolBar"> 
    <!--查询条 s-->
    <form id="pagerForm" data-toggle="ajaxsearch" action="table-fixed.html" method="post">
      <input type="hidden" name="pageSize" value="${model.pageSize}">
      <input type="hidden" name="pageCurrent" value="${model.pageCurrent}">
      <input type="hidden" name="orderField" value="${param.orderField}">
      <input type="hidden" name="orderDirection" value="${param.orderDirection}">
      <div class="bjui-searchBar">
        <label>护照号：</label>
        <input type="text" id="customNo" value="" name="code" class="form-control" size="10">
        &nbsp;
        <label>客户姓名：</label>
        <input type="text" value="" name="name" class="form-control" size="8">
        &nbsp;
        <label>所属业务:</label>
        <select name="type" data-toggle="selectpicker">
          <option value="">全部</option>
          <option value="1">联络</option>
          <option value="2">住宿</option>
          <option value="3">餐饮</option>
          <option value="4">交通</option>
        </select>
        &nbsp;
        <input type="checkbox" id="j_table_chk" value="true" data-toggle="icheck" data-label="我的客户">
        &nbsp;
        <button type="button" class="showMoreSearch" data-toggle="moresearch" data-name="custom2"><i class="fa fa-angle-double-down"></i></button>
        <button type="submit" class="btn-default" data-icon="search">查询</button>
        &nbsp; <a class="btn btn-orange" href="javascript:;" onclick="$(this).navtab('reloadForm', true);" data-icon="undo">清空查询</a>
        <div class="pull-right">
          <div class="btn-group">
            <button type="button" class="btn-default dropdown-toggle" data-toggle="dropdown" data-icon="copy">复选框-批量操作<span class="caret"></span></button>
            <ul class="dropdown-menu right" role="menu">
              <li><a href="book1.xlsx" data-toggle="doexport" data-confirm-msg="确定要导出信息吗？">导出<span style="color: green;">全部</span></a></li>
              <li><a href="book1.xlsx" data-toggle="doexportchecked" data-confirm-msg="确定要导出选中项吗？" data-idname="expids" data-group="ids">导出<span style="color: red;">选中</span></a></li>
              <li class="divider"></li>
              <li><a href="ajaxDone2.html" data-toggle="doajaxchecked" data-confirm-msg="确定要删除选中项吗？" data-idname="delids" data-group="ids">删除选中</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="bjui-moreSearch">
        <label>职业：</label>
        <input type="text" value="" name="profession" size="15" />
        <label>&nbsp;性别:</label>
        <select name="sex" data-toggle="selectpicker">
          <option value="">全部</option>
          <option value="true">男</option>
          <option value="false">女</option>
        </select>
        <label>&nbsp;手机:</label>
        <input type="text" value="" name="mobile" size="10">
        <label>&nbsp;渠道商:</label>
        <input type="hidden" name="pid" class="doc_lookup" value="">
        <input type="text" data-toggle="lookup" data-url="{:U('Manage/Index/channel')}" name="name" class="doc_lookup" size="10">
      </div>
    </form>
    <!-- 查询条 e--> 
  </div>
</div>
<div class="bjui-pageContent tableContent" id="pageContent">
  <div id="calendar" class="calendar"></div>
</div>
<script>
$('#calendar').calendar();
function afterPageLoad() {
  var now = new Date();
  var start = now.getSeconds(),
      calendars = ['success', 'danger', 'important', 'warning', 'info', 'specail', 'primary'],
      rooms = ['A003', 'A004', 'A010', 'A143', 'B008', 'B098', 'B487', 'B340', 'Z000', 'Z431', 'Z018', 'Z864'],
      peoples = ['奥特曼', '行者孙', '地卜师', '绿巨人', 'Catouse', '路人丙'],
      events = ['进食', '喝水', '交谈', '睡觉', '捶打墙壁', '自言自语', '搬动椅子', '唱歌', '上网', '梦游', '观望天花板'],
      eventsTypes = ['happy', 'sad', ':]'],
      tools = ['桌子', '椅子', '水杯', '枪', '随从'],
      descs = ['没有完成', '这次失败了', '徒劳', '很满意', '禁止再次发生', '也行', '情况不明', '发现未知征兆'];
  var calEventGenerater = function()
      {
          var start = now.clone().addDays(Math.random() * 400 - 200);
          var e =
          {
              title: (Math.random() > 0.5 ? ('和' + peoples[Math.floor(Math.random()*peoples.length)]) : '') + events[Math.floor(Math.random()*events.length)],
              desc: descs[Math.floor(Math.random()*descs.length)],
              calendar: calendars[Math.floor(Math.random()*calendars.length)],
              allDay: Math.random() > 0.9,
              start: start,
              end: start.clone().addDays(Math.random() > 0.9 ? Math.random() * 5 : 0)
          };
          return e;
      };
  var dtDataGenerater = function(rowsCount)
      {
          var data =
          {
              cols:
              [
                  {width: 100, text: '#', type: 'number', flex: false, colClass: 'text-center'},
                  {sort: 'down', width: 160, text: '时间', type: 'date', flex: false, colClass: ''},
                  {width: 80, text: '房间', type: 'string', flex: false, colClass: ''},
                  {width: 100, text: '人物', type: 'string', flex: false, colClass: ''},
                  {width: 'auto', text: '事件', type: 'string', flex: false, colClass: ''},
                  {width: 100, text: '事件类型', type: 'string', flex: true, colClass: 'text-center'},
                  {sort: false, width: 200, text: '描述', type: 'string', flex: true, colClass: ''},
                  {width: 100, text: '相关人物', type: 'string', flex: true, colClass: ''},
                  {width: 100, text: '相关物品', type: 'string', flex: true, colClass: ''},
                  {width: 60, text: '评分', type: 'number', flex: false, colClass: 'text-center text-important'},
                  {sort: false, width: 'auto', text: '操作', type: 'string', flex: false, colClass: ''},
              ],
              rows: []
          };
          for (var i = 0; i < rowsCount; i++)
          {
              var row = {checked: Math.random() > 0.9, data:
              [
                  start + i + 101000,
                  now.format('yyyy-MM-dd hh:mm:ss'),
                  rooms[Math.floor(Math.random()*rooms.length)],
                  peoples[Math.floor(Math.random()*peoples.length)],
                  events[Math.floor(Math.random()*events.length)],
                  eventsTypes[Math.floor(Math.random()*eventsTypes.length)],
                  descs[Math.floor(Math.random()*descs.length)],
                  peoples[Math.floor(Math.random()*peoples.length)],
                  tools[Math.floor(Math.random()*tools.length)],
                  Math.floor(Math.random()*100)/10,
                  "<a href='###'><i class='icon-ok-sign'></i></a> &nbsp; <a href='###' class='text-danger'><i class='icon-trash'></i></a> "
              ]};
              data.rows.push(row);
              now = new Date(now.getTime() - (Math.random()*1000*60*60));
          };
          return data;
      },
      calDataGenerater = function(count)
      {
          var data =
          {
              calendars:
              [
                  {name: "success", color: 'green'},
                  {name: "warning", color: 'yellow'},
                  {name: "danger", color: 'red'},
                  {name: "info", color: 'blue'},
                  {name: "important", color: 'brown'},
                  {name: "special", color: 'purple'},
                  {name: "primary", color: 'primary'}
              ],
              events: []
          };
          for (var i = count - 1; i >= 0; i--)
          {
              data.events.push(calEventGenerater());
          }
          return data;
      };
  $(function()
  {
    $('#pageContent .calendar').each(function()
    {
        var $this = $(this);
        var data = calDataGenerater($this.data('exampleCount') || 100);
        $this.calendar({data: data, clickEvent: function(e)
        {
            console.log(e);
            $.zui.messager.show('您点击了 <strong>' + e.event.title + '</strong>');
        }, beforeChange: function(e)
        {
            if(e.change === 'start')
            {
                $.zui.messager.show('起始时间更改为 ' + e.to.format('yyyy年MM月dd日 hh:mm'));
            }
        }});
    });
  });
}
</script> 