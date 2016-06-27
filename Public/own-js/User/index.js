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
                    { "data": "id"},
                    { "data": "account" },
                    { "data": "nickname" },
                    { "data": "name"},
                    { "data": "status",
                        "render": function(data,type,row){
                            if(data == 'active'){
                                return '<span class="label label-success">'+data+'</span>';
                                //return '<span style="color:green;">'+data+'</span>';
                            }else if(data == 'republish'){
                                return '<span class="label label-info">'+data+'</span>';
                                //return '<span style="color:red;">'+data+'</span>';
                            }else{
                                return '<span class="label label-danger">'+data+'</span>';
                                //return data;
                            }
                        }
                    },
                    { "data": "last_login_time",
                        render:function(data,type,row){
                            var str = data;
                            str += "<br/>"+row.last_login_ip;
                            return str;
                        }
                    },
                    { "data": "login_count" },
                    { "data": "email"},
                    { "data": "remark" },
                    { "data": "id",
                        render:function(data,type,row){
                            return '<a href="/User/edit/id/'+data+'">编辑</a>|<a href="/User/delete/id/'+data+'">删除</a>';
                        }
                    }
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
