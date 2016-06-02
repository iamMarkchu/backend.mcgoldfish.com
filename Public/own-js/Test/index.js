(function () {
    var TestindexManager = {
        init: function () {
            var that = this;
            that.currentTable = MarkBase.MarkDT('#tbTestList', {
                "ajax": {
                    "url": '/Home/Test/QueryData/',
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
            $("#example").modal(); 
            $('#addTest').click(function(e){
                window.location.href="/Home/Test/add/";
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
                var url = "/Home/Test/btn_Search";
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
        TestindexManager.init();
    });
})(window);
