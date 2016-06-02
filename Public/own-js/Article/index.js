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
                    { "data": "clickcount"},
                    { "data": "articlesource"},
                    { "data": "maintainorder" },
                    { "data": "addeditor" },
                    { "data": "displayname",
                        "render": function(data,type,row){
                            var returnList = '';
                            if(data == null){
                                returnList += '<span style="color:red;">无类别</span>';
                            }else{
                                returnList += data;
                            }
                            returnList += "<br/>";
                            for (var i = row.tag.length - 1; i >= 0; i--) {
                                returnList += row.tag[i]['displayname']+"|";
                            }
                            return returnList;
                        }
                    },
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
                            str += '<a href="#" class="editUrlLink" data-id="'+row.rid+'">编辑链接</a>|'
                            if(row.status != 'deleted'){
                                str += '<a href="#" class="deleteArticle" data-id="'+data+'">删除</a>';
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
            $("#editArticleUrl").modal({show:false});
            $("#delete-modal").modal({show:false});
            $('#alert-modal').modal({show:false});
            $("#tbartilceList").on('click','.editUrlLink',function(){
                var urlid = $(this).attr('data-id');
                //ajax
                var sendData = {urlid:urlid};
                var sendUrl = '/Home/Url/getUrlInfo/';
                $.ajax({
                    url:sendUrl,
                    data:sendData,
                    type:'POST',
                    dataType:'json',
                    success:function(data){
                        if(data != '0'){
                            $('#editArticleUrl input[name=id]').val(data['id']);
                            $('#editArticleUrl input[name=requestpath]').val(data['requestpath']);
                            $('#editArticleUrl select[name=modeltype]>option').each(function(i){
                                if($(this).val() == data['modeltype']){
                                    $(this).attr('selected','true');
                                }
                            });
                            $('#editArticleUrl select[name=isjump]>option').each(function(i){
                                if($(this).val() == data['isjump']){
                                    $(this).attr('selected','true');
                                }
                            });
                            $('#editArticleUrl select[name=status]>option').each(function(i){
                                if($(this).val() == data['status']){
                                    $(this).attr('selected','true');
                                }
                            });
                            $("#editArticleUrl").modal();
                        }
                    }
                });
            });
            //绑定 modal-edit url提交事件
            $('#updateUrlForArticle').click(function(){
                var id,requestpath,modeltype,isjump,status;
                //如果select2中的值不为空,获取select2中的data中
                id =  $('#editArticleUrl input[name=id]').val();
                if( $('#editArticleUrl input[name=requestpath]').val() != ''){
                    requestpath = $('#editArticleUrl input[name=requestpath]').val();
                }
                if( $('#editArticleUrl select[name=modeltype]').val() != ''){
                    modeltype = $('#editArticleUrl select[name=modeltype]').val();
                }
                if( $('#editArticleUrl select[name=isjump]').val() != ''){
                    isjump = $('#editArticleUrl select[name=isjump]').val();
                }
                if( $('#editArticleUrl select[name=status]').val() != ''){
                    status = $('#editArticleUrl select[name=status]').val();
                }
                //ajax 前端与后台沟通参数
                var url = "/Home/Url/update";
                var sendData = { id: id, requestpath: requestpath, modeltype: modeltype, isjump: isjump,status:status };
                $.ajax({
                    url: url,
                    data: sendData,
                    type: 'POST',
                    success: function (data) {
                        if (data == "1") {
                            $("#editArticleUrl").modal('hide');
                            that.currentTable.fnDraw();
                        }
                    }
                });
            });
            //绑定 modal-delete 删除事件
            $('#deleteForArticle').click(function(){
                var articleid = $('#delete-modal input[name=articleid]').val();
                var sendUrl = "/Home/Article/delete/";
                var sendData = {id:articleid};
                $.ajax({
                    url:sendUrl,
                    data:sendData,
                    type:'GET',
                    success:function(data){
                        if (data == "1") {
                            $("#delete-modal").modal('hide');
                            that.currentTable.fnDraw();
                        }
                    }
                });
            });
            $("#tbartilceList").on('click','.deleteArticle',function(){
                var articleid = $(this).attr('data-id');
                $('#delete-modal input[name=articleid]').val(articleid);
                $("#delete-modal").modal('show');
            });
            $("#tbartilceList").on('click','.resumeArticle',function(){
                var articleid = $(this).attr('data-id');
                var sendUrl = "/Home/Article/resume/";
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
        }
    };
    $(function () {
        ArticleManager.init();
    });
})(window);
