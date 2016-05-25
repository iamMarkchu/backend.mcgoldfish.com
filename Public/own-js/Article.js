(function () {
    var ArticleManager = {
        //初始化
        init: function () {
            var that = this;
            that.currentTable = kxxBase.kxxDT('#tbartilceList', {
                "ajax": {
                    "url": '/Home/Article/QueryData/',
                    "type": 'POST'
                },
                "columns": [
                    { "data": "id" },
                    { "data": "title" },
                    { "data": "status" },
                    { "data": "pageh1" },
                    { "data": "addtime",
                     "render": function(data, type,row){
                        return data;
                     }
                    },
                    { "data": "lastupdatetime" },
                    { "data": "articlesource"},
                    { "data": "maintainorder" },
                    { "data": "addeditor" },
                    { "data": "id",
                      "render": function (data, type, row) {
                            return '<a href="/Home/Article/edit/id/'+data+'" class="edit" articleid="' + data + '">编辑</a><a href="/Home/Article/delete/id/'+data+'" class="delete" articleid="' + data + '">删除</a>'; 
                        }
                    }
                ]
            });
            that.bindEvent();
        },
        currentTable: null,
        bindEvent: function () {
            //绑定新增文章点击事件
            $('#addArticle').click(function(e){
                window.location.href="/Home/Article/add/";
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
        ArticleManager.init();
    });
})(window);
