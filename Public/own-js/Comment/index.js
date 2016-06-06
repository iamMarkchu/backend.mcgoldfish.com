(function () {
    var CommentindexManager = {
        init: function () {
            var that = this;
            that.currentTable = MarkBase.MarkDT('#tbCommentList', {
                "ajax": {
                    "url": '/Comment/QueryData/',
                    "type": 'POST'
                },
                "columns": [
                    { "data": "id"},
                    { "data": "username"},
                    { "data": "parentname",
                        "render": function(data,type,row){
                            var returnString = '';
                            if(data != null){
                                returnString += "回复给:" + data +"<br>"+"内容: ";
                            }
                            returnString += row.content;
                            return returnString;
                         }
                    },
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
                    { "data": "title",
                        "render":function(data,type,row){
                            return '<a href="http://mcgoldfish.com'+row.requestpath+'" title="查看文章">'+data+'</a>';
                        }
                    },
                    { "data": "addtime"},
                    {"data": "id",
                        "render": function (data, type, row) {
                            var str ='<a href="/Article/edit/id/'+data+'">编辑</a>|' ;
                            if(row.status == 'republish'){
                                str += '<a href="/Article/publish/id/'+data+'">发布</a>|';
                            }
                            if(row.status != 'deleted'){
                                str += '<a href="#" class="deleteArticle" data-id="'+data+'">移动到回收站</a>';
                            }else{
                                str += '<a href="#" class="resumeArticle" data-id="'+data+'">恢复</a>';
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
            $('#addComment').click(function(e){
                window.location.href="/Comment/add/";
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
                var url = "/Comment/btn_Search";
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
        CommentindexManager.init();
    });
})(window);
