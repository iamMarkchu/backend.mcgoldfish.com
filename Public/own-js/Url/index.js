(function () {
    var UrlindexManager = {
        init: function () {
            var that = this;
            that.currentTable = MarkBase.MarkDT('#tbUrlList', {
                "ajax": {
                    "url": '/Url/QueryData/',
                    "type": 'POST'
                },
                "columns": [
                    { "data": "id" },
                    { "data": "requestpath" },
                    { "data": "modeltype" },
                    { "data": "optdataid" },
                    { "data": "isjump",
                     "render": function(data, type,row){
                        if(data == 'NO'){
                                return '<span class="label label-success">'+data+'</span>';
                                //return '<span style="color:green;">'+data+'</span>';
                            }else{
                                return '<span class="label label-danger">'+data+'</span>';
                                //return data;
                            }
                     }
                    },
                    { "data": "jumprewriteurlid" },
                    { "data": "status",
                        "render": function(data,type,row){
                            if(data == 'yes'){
                                return '<span class="label label-success">'+data+'</span>';
                                //return '<span style="color:green;">'+data+'</span>';
                            }else{
                                return '<span class="label label-danger">'+data+'</span>';
                                //return data;
                            }
                        }
                    },
                    { "data": "lastupdatetime" },
                    { "data": "id",
                      "render": function (data, type, row) {
                            return '<a href="/Url/edit/id/'+data+'" class="edit">编辑</a>|<a href="/Url/delete/id/'+data+'" class="delete">删除</a>'; 
                        }
                    }
                ]
            });
            that.bindEvent();
        },
        currentTable: null,
        bindEvent: function () {
            var that = this;    
            $('#addUrl').click(function(e){
                window.location.href="/Url/add/";
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
                var url = "/Url/btn_Search";
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
        UrlindexManager.init();
    });
})(window);
