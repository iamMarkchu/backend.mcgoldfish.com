(function () {
    var UserindexManager = {
        init: function () {
            var that = this;
            that.currentTable = MarkBase.MarkDT('#tbUserList', {
                "ajax": {
                    "url": '/User/QueryData/',
                    "type": 'POST'
                },
                "columns": [
                    { "data": "id",
                      "render": function (data, type, row) {
                            return '<div class="btn-group"><a class="btn purple" href="#" data-toggle="dropdown">'+data+'</a><ul class="dropdown-menu"><li><a href="/User/edit/id/'+data+'"><i class="icon-trash"></i> Edit</a></li><li><a href="/User/delete/id/'+data+'"><i class="icon-remove"></i> Delete</a></li></ul></div>';
                            //return '<a href="/Home/Article/edit/id/'+data+'" class="edit" articleid="' + data + '">编辑</a><a href="/Home/Article/delete/id/'+data+'" class="delete" articleid="' + data + '">删除</a>'; 
                        }
                    },
                    { "data": "account" },
                    { "data": "nickname" },
                    { "data": "last_login_time" },
                    { "data": "last_login_ip",
                     "render": function(data, type,row){
                        return data;
                     }
                    },
                    { "data": "login_count" },
                    { "data": "email"},
                    { "data": "remark" },
                    { "data": "status" }
                ]
            });
            that.bindEvent();
        },
        currentTable: null,
        bindEvent: function () {
            var that = this;    
            $('#addUser').click(function(e){
                window.location.href="/User/add/";
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
                var url = "/User/btn_Search";
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
        UserindexManager.init();
    });
})(window);
