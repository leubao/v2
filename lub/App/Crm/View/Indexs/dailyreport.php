<h2 class="contentTitle">鼎盛王朝-康熙大典日报表</h2>	
<div class="pageContent" layoutH="60">
    <div class="tabs" currentIndex="1" eventType="click">
        <div class="tabsHeader">
            <div class="tabsHeaderContent">
                <ul>
                    <li><a href="javascript:;"><span>标题1</span></a></li>
                    <li><a href="javascript:;"><span>标题2</span></a></li>
                    <li><a href="demo_page2.html" class="j-ajax"><span>标题3</span></a></li>
                </ul>
            </div>
        </div>
            <div class="tabsContent" style="height:150px;">
                        <div>
            <pre>
            currentIndex: 0-n   default:0
            eventType: click|hover  default:click
            </pre>
            </div>
            <div>内容2</div>
            <div></div>
        </div>
    </div>

	<fieldset>
		<dl>
			<dt>备注说明：</dt>
			<dd>按出票数量进行统计</dd>
		</dl>
		<dl>
			<dt>时间：</dt>
			<dd>2014年9月25日 14:30</dd>
		</dl>
	</fieldset>		
	<fieldset>
		<legend>按票型划分</legend>
		<table class="table" width="100%">
			<tr height="30">
				<td bgcolor="#ccc">票型</td>
				<td bgcolor="#ccc">出票数量</td>
				<td bgcolor="#ccc">金额</td>
			</tr>
			<tr>
				<td>A票</td>
				<td>3</td>
				<td>600</td>
			</tr>
			<tr>
				<td>B票</td>
				<td>3</td>
				<td>600</td>
			</tr>
            <tr>
                <td>C票</td>
                <td>3</td>
                <td>600</td>
            </tr>            			
		</table>
	</fieldset>
	<fieldset>
		<legend>按渠道划分</legend>
		<table class="table" width="100%">
			<tr height="30">
				<td bgcolor="#ccc">渠道名称</td>
				<td bgcolor="#ccc">出票数量</td>
				<td bgcolor="#ccc">金额</td>
			</tr>
			<tr>
				<td>旅行社</td>
				<td>3</td>
				<td>600</td>
			</tr>
			<tr>
				<td>散客票</td>
				<td>3</td>
				<td>600</td>
			</tr>			
		</table>
	</fieldset>	
	<fieldset> 
		

<script src="{$config_siteurl}statics/chart/esl.js"></script>
                    <div id="main" style="height:400px;width:"></div>
                    <script type="text/javascript">
						// 路径配置
        require.config({
            paths:{ 
                'echarts' : '{$config_siteurl}statics/chart/echarts',
                'echarts/chart/pie' : '{$config_siteurl}statics/chart/echarts'
            }
        });
        
        // 使用pie
        require(
            [
                'echarts',
                'echarts/chart/pie' // 使用柱状图就加载bar模块，按需加载
            ],
            function (ec) {
                // 基于准备好的dom，初始化echarts图表
                var myChart = ec.init(document.getElementById('main')); 
                
                var option = {
                        title : {
                            text: '日报表饼状图',
                            subtext: '纯属虚构',
                            x:'center'
                        },
                        tooltip : {
                            trigger: 'item',
                            formatter: "{a} <br/>{b} : {c} ({d}%)"
                        },
                        legend: {
                            orient : 'vertical',
                            x : 'left',
                            data:['A票','B票','C票']
                        },
                        toolbox: {
                            show : true,
                            feature : {
                                mark : {show: true},
                                dataView : {show: true, readOnly: false},
                                restore : {show: true},
                                saveAsImage : {show: true}
                            }
                        },
                        calculable : true,
                        series : [
                            {
                                name:'访问来源',
                                type:'pie',
                                radius : '55%',
                                center: ['50%', '60%'],
                                data:[
                                    {value:300, name:'A票'},
                                    {value:310, name:'B票'},
                                    {value:234, name:'C票'},

                                ]
                            }
                        ]
                    };
        
                // 为echarts对象加载数据 
                myChart.setOption(option); 
            }
        );
					</script>

	</fieldset>	
</div>
