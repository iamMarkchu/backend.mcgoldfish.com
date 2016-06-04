(function () {
    var CategoryindexManager = {
        init: function () {
            var that = this;
            that.currentTable = MarkBase.MarkDT('#tbCategoryList', {
                "ajax": {
                    "url": '/Category/QueryData/',
                    "type": 'POST'
                },
                "columns": [
                    { "data": "id" },
                    { "data": "displayname" },
                    { "data": "parentcategoryid" },
                    { "data": "addtime" },
                    { "data": "lastchangetime",
                     "render": function(data, type,row){
                        return data;
                     }
                    },
                    { "data": "displayorder" },
                    { "data": "id",
                      "render": function (data, type, row) {
                            return '<a href="/Category/edit/id/'+data+'" class="edit">编辑</a>|<a href="/Category/delete/id/'+data+'" class="delete">删除</a>'; 
                        }
                    }
                ]
            });
            that.bindEvent();
        },
        currentTable: null,
        bindEvent: function () {
            var that = this;    
            $('#addCategory').click(function(e){
                window.location.href="/Category/add/";
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
                var url = "/Category/btn_Search";
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
        CategoryindexManager.init();
    });
})(window);
