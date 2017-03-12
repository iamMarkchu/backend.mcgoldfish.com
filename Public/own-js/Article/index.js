(function () {
    var ArticleManager = {
        //初始化
        init: function () {
            var that = this;
            that.currentTable = MarkBase.MarkDT('#tbartilceList', {
                "ajax": {
                    "url": '/article/querydata.html',
                    "type": 'POST'
                },
                "columns": [
                    { "data": "id"},
                    { "data": "title",
                        "render": function(data,type,row){
                            if(row.requestpath == null){
                                return data;
                            }else{  
                                return '<a href="http://mcgoldfish.com/article/'+row.id+'" title="查看文章">'+data+'</a>';
                            }
                        }
                    },
                    { "data": "status",
                        "render": function(data,type,row){
                            if(data == 'active'){
                                return '<span class="label label-success">'+data+'</span>';
                            }else if(data == 'republish'){
                                return '<span class="label label-info">'+data+'</span>';
                            }else{
                                return '<span class="label label-danger">'+data+'</span>';
                            }
                        }
                    },
                    { "data": "click_count"},
                    { "data": "source"},
                    { "data": "display_order" },
                    { "data": "add_editor" },
                    { "data": "category_name",
                        "render": function(data,type,row){
                            var returnList = '';
                            if(data == null){
                                returnList += '<span class="label label-danger">无类别</span>';
                            }else{
                                returnList += data;
                            }
                            returnList += "<br/>";
                            for (var i = row.tag.length - 1; i >= 0; i--) {
                                if(i == 0)
                                {
                                    returnList += row.tag[i]['tag_name'];
                                }else{
                                    returnList += row.tag[i]['tag_name']+"|";
                                }

                            }
                            return returnList;
                        }
                    },
                    { "data": "created_at",
                        "render": function(data,type,row){
                            var returnString = data+"<br/>";
                            returnString += row.updated_at;
                            return returnString;
                        }
                    },
                    {"data": "id",
                        "render": function (data, type, row) {
                            var str ='<a href="/article/edit/id/'+data+'.html">编辑</a>|' ;
                            if(row.status == 'republish'){
                                str += '<a href="/article/publish/id/'+data+'.html">发布</a>|';
                            }
                            if(row.status != 'deleted'){
                                str += '<a href="#" class="delete-article" data-id="'+data+'">删除</a>';
                            }else{
                                str += '<a href="#" class="resume-article" data-id="'+data+'">恢复</a>';
                            }
                            return str;
                        }
                    }
                ]
            });
            that.bindEvent();
        },
        currentTable: null,
        bindEvent: function () {
         var that = this;
            //绑定搜索按钮事件
            $('#searchbutton').click(function (e) {
                var formData = $('form[name=article-form]').serialize();
                $.ajax({
                    url: '/article/search.html',
                    data: formData,
                    type: 'POST',
                    success: function (data) {
                        that.currentTable.fnDraw();
                    }
                });
            });
            $("#tbartilceList").on('click','.resumeArticle',function(){
                var articleid = $(this).attr('data-id');
                var sendUrl = "/Article/resume/";
                var sendData = {id:articleid};
                $.ajax({
                    url:sendUrl,
                    data:sendData,
                    type:'POST',
                    success:function(data){
                        if (data == "1") {
                            $('#alert-modal .alert-data-title').html('恢复成功!');
                            $('#alert-modal').modal('show');
                            that.currentTable.fnDraw();
                        }
                    }
                });
            });
            $('#tbartilceList').on('click', '.delete-article', function () {
               var id = $(this).attr('data-id');
               $.get('/article/delete/id/'+id, function (data) {
                   alert(data);
               })
            });
        }
    };
    $(function () {
        ArticleManager.init();
    });
})(window);
