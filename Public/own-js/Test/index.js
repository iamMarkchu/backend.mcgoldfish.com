(function () {
    var TestindexManager = {
        init: function () {
            var that = this;
            that.currentTable = MarkBase.MarkDT('#tbTestList', {
                "ajax": {
                    "url": '/Test/QueryData/',
                    "type": 'POST'
                },
                "columns": [
                    { "data": "id"}
                ]
            });
            that.bindEvent();
        },
        currentTable: null,
        bindEvent: function () {
            var that = this;    
            // 基于准备好的dom，初始化echarts实例
             var myChart = echarts.init(document.getElementById('main'));
            // // 指定图表的配置项和数据
            // myChart.setOption({
            //     title: {
            //         text: 'ECharts 入门示例',
            //         link: '/',
            //         left: 'right'
            //     },
            //     legend:{
            //         data:["asd"]
            //     },
            //     series : [
            //         {
            //             name: '访问来源',
            //             roseType: 'angle',
            //             type: 'pie',
            //             radius: '55%',
            //             data:[
            //                 {value:400, name:'搜索引擎'},
            //                 {value:335, name:'直接访问'},
            //                 {value:310, name:'邮件营销'},
            //                 {value:274, name:'联盟广告'},
            //                 {value:235, name:'视频广告'}
            //             ]
            //         }
            //     ]
            // });
            // var myChart = echarts.init(document.getElementById('main2'));
            // var option = {
            //     title: {
            //         text: 'ECharts 入门示例'
            //     },
            //     tooltip: {},
            //     legend: {
            //         data:['销量']
            //     },
            //     xAxis: {
            //         data: ["衬衫","羊毛衫","雪纺衫","裤子","高跟鞋","袜子"],
            //     },
            //     yAxis: {},
            //     series: [{
            //         name: '销量',
            //         type: 'bar',
            //         data: [5, 20, 36, 10, 10, 20]
            //     }]
            // };
            // option = {
            //     title: {
            //         text: '某楼盘销售情况',
            //         subtext: '纯属虚构'
            //     },
            //     tooltip: {
            //         trigger: 'axis'
            //     },
            //     legend: {
            //         data:['意向','预购','成交']
            //     },
            //     toolbox: {
            //         show: true,
            //         feature: {
            //             magicType: {show: true, type: ['stack', 'tiled']},
            //             saveAsImage: {show: true}
            //         }
            //     },
            //     xAxis: {
            //         type: 'category',
            //         boundaryGap: false,
            //         data: ['周一','周二','周三','周四','周五','周六','周日']
            //     },
            //     yAxis: {
            //         type: 'value'
            //     },
            //     series: [{
            //         name: '成交',
            //         type: 'pie',
            //         smooth: true,
            //         data: [10, 12, 21, 54, 260, 830, 710]
            //     },
            //     {
            //         name: '预购',
            //         type: 'bar',
            //         smooth: true,
            //         data: [30, 182, 434, 791, 390, 30, 10]
            //     },
            //     {
            //         name: '意向',
            //         type: 'bar',
            //         smooth: true,
            //         data: [1320, 1132, 601, 234, 120, 90, 20]
            //     }]
            // };
            // // 使用刚指定的配置项和数据显示图表。
            // myChart.setOption(option);
            draggable.init(
                $('div[_echarts_instance_]')[0],
                myChart,
                {
                    width: 700,
                    height: 400,
                    throttle: 70
                }
            );

            myChart.hideLoading();



    option = {
        baseOption: {
            title : {
                text: '南丁格尔玫瑰图',
                subtext: '纯属虚构',
                x:'center'
            },
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                data:['rose1','rose2','rose3','rose4','rose5','rose6','rose7','rose8']
            },
            toolbox: {
                show : true,
                feature : {
                    mark : {show: true},
                    dataView : {show: true, readOnly: false},
                    magicType : {
                        show: true,
                        type: ['pie', 'funnel']
                    },
                    restore : {show: true},
                    saveAsImage : {show: true}
                }
            },
            calculable : true,
            series : [
                {
                    name:'半径模式',
                    type:'pie',
                    roseType : 'radius',
                    label: {
                        normal: {
                            show: false
                        },
                        emphasis: {
                            show: true
                        }
                    },
                    lableLine: {
                        normal: {
                            show: false
                        },
                        emphasis: {
                            show: true
                        }
                    },
                    data:[
                        {value:10, name:'rose1'},
                        {value:5, name:'rose2'},
                        {value:15, name:'rose3'},
                        {value:25, name:'rose4'},
                        {value:20, name:'rose5'},
                        {value:35, name:'rose6'},
                        {value:30, name:'rose7'},
                        {value:40, name:'rose8'}
                    ]
                },
                {
                    name:'面积模式',
                    type:'pie',
                    roseType : 'area',
                    data:[
                        {value:10, name:'rose1'},
                        {value:5, name:'rose2'},
                        {value:15, name:'rose3'},
                        {value:25, name:'rose4'},
                        {value:20, name:'rose5'},
                        {value:35, name:'rose6'},
                        {value:30, name:'rose7'},
                        {value:40, name:'rose8'}
                    ]
                }
            ]
        },
        media: [
            {
                option: {
                    legend: {
                        right: 'center',
                        bottom: 0,
                        orient: 'horizontal'
                    },
                    series: [
                        {
                            radius: [20, '50%'],
                            center: ['25%', '50%']
                        },
                        {
                            radius: [30, '50%'],
                            center: ['75%', '50%']
                        }
                    ]
                }
            },
            {
                query: {
                    minAspectRatio: 1
                },
                option: {
                    legend: {
                        right: 'center',
                        bottom: 0,
                        orient: 'horizontal'
                    },
                    series: [
                        {
                            radius: [20, '50%'],
                            center: ['25%', '50%']
                        },
                        {
                            radius: [30, '50%'],
                            center: ['75%', '50%']
                        }
                    ]
                }
            },
            {
                query: {
                    maxAspectRatio: 1
                },
                option: {
                    legend: {
                        right: 'center',
                        bottom: 0,
                        orient: 'horizontal'
                    },
                    series: [
                        {
                            radius: [20, '50%'],
                            center: ['50%', '30%']
                        },
                        {
                            radius: [30, '50%'],
                            center: ['50%', '70%']
                        }
                    ]
                }
            },
            {
                query: {
                    maxWidth: 500
                },
                option: {
                    legend: {
                        right: 10,
                        top: '15%',
                        orient: 'vertical'
                    },
                    series: [
                        {
                            radius: [20, '50%'],
                            center: ['50%', '30%']
                        },
                        {
                            radius: [30, '50%'],
                            center: ['50%', '75%']
                        }
                    ]
                }
            }
        ]
    };



    myChart.setOption(option);
        }
    };
    $(function () {
        TestindexManager.init();
    });
})(window);
