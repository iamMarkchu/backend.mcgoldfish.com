(function () {
    var ArticleManager = {
        //初始化
        init: function () {
            var that = this;
            that.currentTable = MarkBase.MarkDT('#tbartilceList', {
                "ajax": {
                    "url": '/Home/Article/QueryData/',
                    "type": 'POST'
                },
                "columns": [
                    { "data": "id"},
                    { "data": "title",
                        "render": function(data,type,row){
                            return '<a href="http://mcgoldfish.com'+row.requestpath+'" title="查看文章">'+data+'</a>'  
                        }
                    },
                    { "data": "status",
                        "render": function(data,type,row){
                            if(data == 'active'){
                                return '<span style="color:green;">'+data+'</span>';
                            }else if(data == 'republish'){
                                return '<span style="color:red;">'+data+'</span>';
                            }else{
                                return data;
                            }
                        }
                    },
                    { "data": "displayname",
                        "render": function(data,type,row){
                            if(data == null){
                                return '<span style="color:red;">无类别</span>';
                            }else{
                                return data;
                            }
                        }
                    },
                    { "data": "articlesource"},
                    { "data": "maintainorder" },
                    { "data": "addeditor" },
                    { "data": "addtime",
                        "render": function(data,type,row){
                          return data+"<br/>"+row.lastupdatetime;
                        }
                    },
                    {"data": "id",
                        "render": function (data, type, row) {
                            var str ='<a href="/Home/Article/edit/id/'+data+'">编辑</a>|' ;
                            if(row.status == 'republish'){
                                str += '<a href="/Home/Article/publish/id/'+data+'">发布</a>|';
                            }
                            str += '<a href="/Home/Article/edit/id/'+data+'">编辑链接</a>|'
                            if(row.status != 'deleted'){
                                str += '<a href="/Home/Article/delete/id/'+data+'">删除</a>';
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
            //绑定新增文章点击事件
            $('#addArticle').click(function(e){
                window.location.href="/Home/Article/add/";
            });
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
        ArticleManager.init();
    });
})(window);
