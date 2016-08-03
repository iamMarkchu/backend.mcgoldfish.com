(function () {
    var FinanceindexManager = {
        init: function () {
            var that = this;
            that.currentTable = MarkBase.MarkDT('#tbFinanceList', {
                "ajax": {
                    "url": '/Finance/QueryData/',
                    "type": 'POST',
                },
                "columns": [
                    { "data": "id"},
                    { "data": "amount"},
                    { "data": "type",
                        "render": function(data,type,row){
                            if(data == '支出'){
                                return '<span class="label label-success">'+data+'</span>';
                                //return '<span style="color:green;">'+data+'</span>';
                            }else if(data == '收入'){
                                return '<span class="label label-info">'+data+'</span>';
                                //return '<span style="color:red;">'+data+'</span>';
                            }else if (data == '转账'){
                                return '<span class="label label-danger">'+data+'</span>';
                                //return data;
                            }
                        }
                    },
                    { "data": "fcdisplayname",
                        "render": function(data,type,row){
                            if(data == undefined){
                                return '暂无';
                            }else{
                                return data;
                            }
                        }
                    },
                    { "data": "where"},
                    { "data": "when"},
                    { "data": "mname",
                        "render": function(data,type,row){
                            if(data == undefined){
                                return "无商家"
                            }else{
                                return data;
                            }
                        }
                    },
                    { "data": "content"},
                    { "data": "uname"},
                    { "data": "anname"},
                ],
                "fnDrawCallback": function( oSettings ) {
                    $.ajax({
                        url: "/Finance/getSumFinance",
                        type:"POST",
                        success: function(data){
                            var sumList = data.sumList;
                            var dayList = data.dayList;
                            var budgetInfo = data.budgetInfo;
                            var budgetLeft = budgetInfo.budget - budgetInfo.realcost;
                            var budgetTotal = budgetInfo.budget;
                            var day = [];
                            var money = [];
                            $.each(dayList,function(n,value){
                                value.day = value.day.substring(5);
                                day.push(value.day);
                                money.push(value.money);
                            });
                            //console.log(sumList);
                            //console.log(day);
                            //console.log(money);
                            var myChart = echarts.init(document.getElementById('main'));
                            // 指定图表的配置项和数据
                            myChart.setOption({
                                title : {
                                    text: '本月消费比例图',
                                    subtext: day[0]+"~"+day[day.length -1],
                                    x:'left'
                                },
                                tooltip : {
                                    trigger: 'item',
                                    formatter: "{a} <br/>{b} : {c} 元 ({d}%)"
                                },
                                series : [
                                    {
                                        name: '类别',
                                        type: 'pie',
                                        radius: '50%',
                                        data: sumList
                                    }
                                ]
                            });
                            var myChart2 = echarts.init(document.getElementById('main2'));
                            // 指定图表的配置项和数据
                            myChart2.setOption({
                                title: {
                                    text: '本月日消费走势图',
                                    subtext: day[0]+"~"+day[day.length -1],
                                    x:'left'
                                },
                                tooltip: {},
                                legend: {
                                    data:['金额']
                                },
                                xAxis: {
                                    data: day,
                                },
                                yAxis: {},
                                series: [{
                                    name: '金额',
                                    type: 'line',
                                    data: money
                                }]
                            });
                            var myChart2 = echarts.init(document.getElementById('main3'));
                            myChart2.setOption({
                                title: {
                                    text: (budgetInfo.type =='1'?'情侣账户':'个人账户')+'预算图',
                                    subtext: budgetInfo.yearmonth+' 预算为: '+budgetInfo.budget+'元'+' 已用'+budgetInfo.realcost+'元',
                                    x:'left'
                                },
                                color: ['#3398DB'],
                                tooltip : {
                                    trigger: 'axis',
                                    axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                                        type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                                    }
                                },
                                xAxis : [
                                    {
                                        data : ['剩余预算']
                                    }
                                ],
                                yAxis : [
                                    {
                                        type : 'value',
                                        max : 7000
                                    }
                                ],
                                series : [
                                    {
                                        name:'剩余预算',
                                        type:'bar',
                                        itemStyle: {normal: {color:'rgba(181,195,52,0.5)', label:{show:true}}},
                                        data:[budgetLeft],
                                        markLine: {  
                                            data: [  
                                                {
                                                    name: '预算线',
                                                    yAxis: 5500
                                                } 
                                            ]  
                                        } 
                                    }
                                ],
                            });
                        }
                    });
                }
            });
            that.bindEvent();
        },
        currentTable: null,
        bindEvent: function () {
            var that = this;    
            $('#addFinance').click(function(e){
                window.location.href="/Finance/add/";
            });
            $('#searchbutton').click(function (e) {
                var type,startdate,enddate;
                if ($('#selectfinancetype').val() != '-1') {
                    type = $('#selectfinancetype').val();
                }
                if ($('#searchstartdate').val() != '') {
                    startdate = $('#searchstartdate').val();
                }
                if ($('#searchenddate').val() != '') {
                    enddate = $('#searchenddate').val();
                }
                var url = "/Finance/btn_Search";
                var sendData = { type: type, startdate: startdate, enddate: enddate};
                $.ajax({
                    url: url,
                    data: sendData,
                    type: 'POST',
                    success: function (data) {
                        if (data == "1") {
                            that.currentTable.fnDraw();
                        }
                    }
                });
            });
                        //绑定datepicker事件
            $('#searchstartdate').datepicker({
                format:'yyyy-mm-dd'
            });
            $('#searchenddate').datepicker({
                format:'yyyy-mm-dd'
            });
            //截止日期改变后 比较 开始日期和截止日期
            // $('#searchenddate').datepicker().change(function (e) {
            //     var enddate = $('#searchenddate').val().split('-');
            //     var startdate = $('#searchstartdate').val().split('-');
            //     if (startdate[0] > enddate[0]) {
            //         if (confirm('结束日期应在开始日期之后!')) {
            //             $('#searchenddate').attr('value', "");
            //         }

            //     }
            //     if (startdate[1] > enddate[1] && startdate[0] == enddate[0]) {
            //         if (confirm('结束日期应在开始日期之后!')) {
            //             $('#searchenddate').attr('value', "");
            //         }
            //     }
            //     if (startdate[2] > enddate[2] && startdate[1] && enddate[1] && startdate[0] == enddate[0]) {
            //         if (confirm('结束日期应在开始日期之后!')) {
            //             $('#searchenddate').attr('value', "");
            //         }
            //     }
            // });
            hisBudgetTable = MarkBase.MarkDT('#hisBudgetList', {
                        "ajax": {
                            "url": '/Finance/getHisBudgetList/',
                            "type": 'POST',
                        },
                        "columns": [
                            { "data": "yearmonth"},
                            { "data": "budget"},
                            { "data": "realcost"},
                            { "data": "realcost",
                                "render": function(data,type,row){
                                    if(row.budget == '0.00'){
                                        return '<span class="label label-success">未设置预算</span>';
                                    }else if(data <= row.budget){
                                        return '<span class="label label-success">否</span>';
                                    }else{
                                        return '<span class="label label-danger">是</span>';
                                    }
                                }
                            }
                        ]
                        });
            $('#setBudget').click(function(){
               $.ajax({
                    url: "/Finance/setBudget",
                    type: "POST",
                    success: function(data){
                        if(data != ''){
                            //console.log(data);
                            $("#setBudgetModal input[name=budget]").val(data.budget);
                        }
                        hisBudgetTable.fnDraw();
                        $("#setBudgetModal").modal();
                    }
               }); 
            });
            $("#setBudgetModal").modal({show:false});
            $("#setBudgetSubmit").click(function(){
                var userid,budget;
                //如果select2中的值不为空,获取select2中的data中
                userid = $('#setBudgetModal input[name=userid]').val();
                if( $('#setBudgetModal input[name=budget]').val() != ''){
                    budget = $('#setBudgetModal input[name=budget]').val();
                    sendData = {userid:userid,budget:budget};
                    $.ajax({
                        url: "/Public/insertBudget",
                        data: sendData,
                        type: "POST",
                        success: function(data){
                            $("#setBudgetModal").modal({show:false});
                            $('#alert-modal .alert-data-title').html('设置成功!');
                            $('#alert-modal').modal();
                        }
                    });
                }
            });
        }
    };
    $(function () {
        FinanceindexManager.init();
    });
})(window);
