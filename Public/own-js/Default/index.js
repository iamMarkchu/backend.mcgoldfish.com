(function () {
    var [controllerName][actionName]Manager = {
        init: function () {
            var that = this;
            that.currentTable = MarkBase.MarkDT('#tb[controllerName]List', {
                "ajax": {
                    "url": '/Home/[controllerName]/QueryData/',
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
            $('#add[controllerName]').click(function(e){
                window.location.href="/Home/[controllerName]/add/";
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
                var url = "/Home/[controllerName]/btn_Search";
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
        [controllerName][actionName]Manager.init();
    });
})(window);
