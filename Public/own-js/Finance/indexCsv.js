(function () {
    var FinanceindexManager = {
        init: function () {
            var that = this;
            that.currentTable = MarkBase.MarkDT('#tbFinanceList', {
                "ajax": {
                    "url": '/Finance/QueryDataCsv/',
                    "type": 'POST'
                },
                "columns": [
                    { "data": "编号"},
                    { "data": "金额"},
                    { "data": "类型"},
                    { "data": "地点"},
                    { "data": "时间"},
                    { "data": "商家"},
                    { "data": "详情"},
                    { "data": "消费人"},
                    { "data": "来源\n"},
                    { "data": "编号"}
                ]
            });
            that.bindEvent();
        },
        currentTable: null,
        bindEvent: function () {
            var that = this;    
            $('#addFinance').click(function(e){
                window.location.href="/Finance/addToCsv/";
            });
            $('#searchbutton').click(function (e) {
                // var titleOrId,selectarticlesource,selectstatus,selectorderby;
                // if($('#search_id_or_title').val() != ''){
                //     titleOrId = $('#search_id_or_title').val();
                // }
                // if ($('#selectarticlesource').val() != '-1') {
                //     selectarticlesource = $('#selectarticlesource').val();
                // }
                // if ($('#selectstatus').val() != '-1') {
                //     selectstatus = $('#selectstatus').val();
                // }
                // if ($('#selectorderby').val() != '-1') {
                //     selectorderby = $('#selectorderby').val();
                // }
                var url = "/Finance/btn_Search";
                //var sendData = { titleOrId: titleOrId, selectarticlesource: selectarticlesource, selectstatus: selectstatus, selectorderby: selectorderby };
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

        }
    };
    $(function () {
        FinanceindexManager.init();
    });
})(window);
