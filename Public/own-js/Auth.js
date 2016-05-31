(function () {
    var AuthManager = {
        //初始化
        init: function () {
            var that = this;
            that.currentTable = kxxBase.kxxDT('#tbList', {
                "ajax": {
                    "url": '/Home/Auth/QueryData/',
                    "type": 'POST'
                },
                "columns": [
                    { "data": "id",
                      "render": function (data, type, row) {
                            return '<div class="btn-group"><a class="btn purple" href="#" data-toggle="dropdown">'+data+'</a><ul class="dropdown-menu"><li><a href="/Home/Auth/edit/id/'+data+'"><i class="icon-trash"></i> Edit</a></li><li><a href="/Home/Auth/delete/id/'+data+'"><i class="icon-remove"></i> Delete</a></li><li><a href="/Home/User/index/role_id/'+data+'"><i class="icon-add"></i> userList</a></li><li><a href="/Home/Auth/giveAuth/role_id/'+data+'"><i class="icon-add"></i> giveAuth</a></li></ul></div>';
                            //return '<a href="/Home/Article/edit/id/'+data+'" class="edit" articleid="' + data + '">编辑</a><a href="/Home/Article/delete/id/'+data+'" class="delete" articleid="' + data + '">删除</a>'; 
                        }
                    },
                    { "data": "name" },
                    { "data": "status" },
                    { "data": "pid" },
                    { "data": "addtime",
                     "render": function(data, type,row){
                        return data;
                     }
                    },
                    { "data": "lastupdatetime" },
                    { "data": "remark"}
                ]
            });
            that.bindEvent();
        },
        currentTable: null,
        bindEvent: function () {
            var that = this;    
            //绑定新增文章点击事件
            $('#add').click(function(e){
                window.location.href="/Home/Auth/add/";
            });
            $('#selOY2').select2();
            //绑定搜索按钮事件
            $('#searchbutton').click(function (e) {
                var titleOrId,selectarticlesource,selectstatus,selectorderby;
                //如果select2中的值不为空,获取select2中的data中
                if($('#search_id_or_title').val() != ''){
                    titleOrId = $('#search_id_or_title').val();
                }
                if ($('#selectarticlesource').val() != '-1') {
                    selectarticlesource = $('#selectarticlesource').val();
                }
                if ($('#selectstatus').val() != '-1') {
                    selectstatus = $('#selectstatus').val();
                }
                if ($('#selectorderby').val() != '-1') {
                    selectorderby = $('#selectorderby').val();
                }
                //ajax 前端与后台沟通参数
                var url = "/Home/Article/btn_Search";
                var sendData = { titleOrId: titleOrId, selectarticlesource: selectarticlesource, selectstatus: selectstatus, selectorderby: selectorderby };
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
        AuthManager.init();
    });
})(window);
