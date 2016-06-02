(function () {
    var Manager = {
        //初始化
        init: function () {
            var that = this;
            that.currentTable = kxxBase.kxxDT('#tblist', {
                "ajax": {
                    "url": '/Home/Url/QueryData/',
                    "type": 'POST'
                },
                "columns": [
                    { "data": "id" },
                    { "data": "requestpath" },
                    { "data": "modeltype" },
                    { "data": "optdataid" },
                    { "data": "isjump",
                     "render": function(data, type,row){
                        return data;
                     }
                    },
                    { "data": "jumprewriteurlid" },
                    { "data": "status"},
                    { "data": "pagemetaid" },
                    { "data": "lastupdatetime" },
                    { "data": "id",
                      "render": function (data, type, row) {
                            return '<a href="/Home/Url/edit/id/'+data+'" class="edit">编辑</a>|<a href="/Home/Url/delete/id/'+data+'" class="delete">删除</a>'; 
                        }
                    }
                ]
            });
            that.bindEvent();
        },
        currentTable: null,
        bindEvent: function () {
            //绑定新增文章点击事件
            $('#addbutton').click(function(e){
                window.location.href="/Home/Url/add/";
            });
            //绑定搜索按钮事件
            $('#searchbutton').click(function (e) {        
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
        Manager.init();
    });
})(window);
