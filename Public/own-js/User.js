(function () {
    var UserManager = {
        //初始化
        init: function () {
            var that = this;
            that.currentTable = kxxBase.kxxDT('#tbList', {
                "ajax": {
                    "url": '/Home/User/QueryData/',
                    "type": 'POST'
                },
                "columns": [
                    { "data": "id",
                      "render": function (data, type, row) {
                            return '<div class="btn-group"><a class="btn purple" href="#" data-toggle="dropdown">'+data+'</a><ul class="dropdown-menu"><li><a href="/Home/User/edit/id/'+data+'"><i class="icon-trash"></i> Edit</a></li><li><a href="/Home/User/delete/id/'+data+'"><i class="icon-remove"></i> Delete</a></li></ul></div>';
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
            //绑定新增文章点击事件
            $('#add').click(function(e){
                window.location.href="/Home/User/add/";
            });
            $('#selectrole').select2();
            //绑定搜索按钮事件
            $('#searchbutton').click(function (e) {
                var selectrole;
                //如果select2中的值不为空,获取select2中的data中
                if ($('#selectrole').val() != '-1') {
                    selectrole = $('#selectrole').val();
                }
                //ajax 前端与后台沟通参数
                var url = "/Home/User/btn_Search";
                var sendData = { selectrole: selectrole};
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
        UserManager.init();
    });
})(window);
