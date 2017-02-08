<script src="{$config_siteurl}statics/chart/esl.js"></script>
    <!--第一个图表-->
    <div id="main1" style="height:350px;width:500px;border:1px dashed #ccc;float:left;margin:10px 0 0 10px"></div>
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
                    var myChart = ec.init(document.getElementById('main1')); 
                    
                    var option = {
                            title : {
                                text: '日报表饼状图',
                                /*subtext: '纯属虚构',*/
                                x:'center'
                            },
                            /*tooltip : {
                                trigger: 'item',
                                formatter: "{a} <br/>{b} : {c} ({d}%)"
                            },*/
                            legend: {
                                orient : 'vertical',
                                x : 'left',
                                data:['A票','B票','C票']
                            },
                            /*toolbox: {
                                show : true,
                                feature : {
                                    mark : {show: true},
                                    dataView : {show: true, readOnly: false},
                                    restore : {show: true},
                                    saveAsImage : {show: true}
                                }
                            },*/
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
    <!--第一个图表END-->

    <!--第二个图表-->
    <div id="main2" style="height:350px;width:500px;border:1px dashed #ccc;float:left;margin:10px 0 0 10px"></div>
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
                    var myChart = ec.init(document.getElementById('main2')); 
                    
                    var option = {
                            title : {
                                text: '渠道饼状图',
                                /*subtext: '纯属虚构',*/
                                x:'center'
                            },
                            /*tooltip : {
                                trigger: 'item',
                                formatter: "{a} <br/>{b} : {c} ({d}%)"
                            },*/
                            legend: {
                                orient : 'vertical',
                                x : 'left',
                                data:['旅行社','散客渠道','酒店']
                            },
                            /*toolbox: {
                                show : true,
                                feature : {
                                    mark : {show: true},
                                    dataView : {show: true, readOnly: false},
                                    restore : {show: true},
                                    saveAsImage : {show: true}
                                }
                            },*/
                            calculable : true,
                            series : [
                                {
                                    name:'渠道饼状图',
                                    type:'pie',
                                    radius : '55%',
                                    center: ['50%', '60%'],
                                    data:[
                                        {value:1000, name:'旅行社'},
                                        {value:3100, name:'散客渠道'},
                                        {value:200, name:'酒店'},

                                    ]
                                }
                            ]
                        };
            
                    // 为echarts对象加载数据 
                    myChart.setOption(option); 
                }
            );
    </script>
    <!--第二个图表END-->    

