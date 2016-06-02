(function () {
    var Manager = {
        //初始化
        init: function () {
            var that = this;
            that.currentTable = kxxBase.kxxDT('#tblist', {
                "ajax": {
                    "url": '/Home/Category/QueryData/',
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
                            return '<a href="/Home/Category/edit/id/'+data+'" class="edit">编辑</a>|<a href="/Home/Category/delete/id/'+data+'" class="delete">删除</a>'; 
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
                window.location.href="/Home/Category/add/";
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
