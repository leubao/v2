<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader"> 
  <!--工具条 s-->
  
</div>
<div class="bjui-pageContent">
	<div id='calendar'></div>
</div>
<div class="bjui-pageFooter">
</div>
<script>
    $(document).ready(function() {
        $('#calendar').fullCalendar({
            //defaultDate: '2015-02-12',
            views:'basicDay　basicWeek month',
            header:{left: 'prev next today', center: 'title', right:'month,basicWeek'},
            editable: true,
            eventLimit: true, // allow "more" link when too many events
            events: [
                {   
                    title: '销售计划:5',
                    start: '2015-09-14',
                },
                {   
                    title: '人数:50000',
                    start: '2015-09-14',
                },
                {   
                    title: '收入:500.88万',
                    start: '2015-09-14',
                },
                {   
                    title: '净收入:300.77万',
                    start: '2015-09-14',
                },
                {  
                    title: '销售计划:5',
                    start: '2015-09-15',
                },
                {   
                    title: '人数:50000',
                    start: '2015-09-15',
                },
                {   
                    title: '收入:500.88万',
                    start: '2015-09-15',
                },
                {   
                    title: '净收入:300.77万',
                    start: '2015-09-15',
                },
            ],
            //单击日历中的一天
            dayClick: function(date, event, jsEvent, view) {
                var select_date = date.format();
                $(this).dialog({id:'day_info'+select_date, url:'{:U('Report/Sales/day_info');}', title:select_date+'销售详情',mask:true});

			      },
      			//日程（事件）对象
      			eventClick: function(date, allDay, jsEvent, view) {
      				//var select_date = allDay.format();
              //$(this).dialog({id:'day_info'+select_date, url:'{:U('Report/Sales/day_info');}', title:select_date+'销售详情',mask:true});

      			}
        });

        
    });

</script>