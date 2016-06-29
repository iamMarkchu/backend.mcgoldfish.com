(function () {
    var FinanceindexManager = {
        init: function () {
            var that = this;
            that.currentTable = MarkBase.MarkDT('#tbFinanceList', {
                "ajax": {
                    "url": '/Finance/QueryData/',
                    "type": 'POST'
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
                    { "data": "category",
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
                    { "data": "merchant"},
                    { "data": "content"},
                    { "data": "who"},
                    { "data": "belong"},
                ]
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
        }
    };
    $(function () {
        FinanceindexManager.init();
    });
})(window);
