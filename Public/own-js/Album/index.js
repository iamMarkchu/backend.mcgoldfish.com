(function () {
    var AlbumManager = {
        //初始化
        init: function () {
            var that = this;
            that.currentTable = MarkBase.MarkDT('#tbalbumList', {
                "ajax": {
                    "url": '/Album/QueryData/',
                    "type": 'POST'
                },
                "columns": [
                    { "data": "id"},
                    { "data": "title",
                        "render": function(data,type,row){
                            if(row.requestpath == null){
                                return data;
                            }else{  
                                return '<a href="http://mcgoldfish.com'+row.requestpath+'" title="查看文章">'+data+'</a>';
                            }
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
                    { "data": "clickcount"},
                    { "data": "maintainorder" },
                    { "data": "addeditor" },
                    { "data": "displayname",
                        "render": function(data,type,row){
                            var returnList = '';
                            if(data == null){
                                returnList += '<span class="label label-danger">无类别</span>';
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
                            var returnString = data+"<br/>";
                            var myDate = new Date();

                            returnString += row.lastupdatetime;
                            return returnString;
                        }
                    },
                    {"data": "id",
                        "render": function (data, type, row) {
                            var str ='<a href="/Album/edit/id/'+data+'">编辑</a>|' ;
                            if(row.status == 'republish'){
                                str += '<a href="/Album/publish/id/'+data+'">发布</a>|';
                            }
                            str += '<a href="#" class="editUrlLink" data-id="'+row.rid+'">编辑链接</a>|'
                            if(row.status != 'deleted'){
                                str += '<a href="#" class="deleteAlbum" data-id="'+data+'">删除</a>';
                            }else{
                                str += '<a href="#" class="resumeAlbum" data-id="'+data+'">恢复</a>';
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
            $('#addAlbum').click(function(e){
                window.location.href="/Album/add/";
            });
            //绑定编辑文章点击事件
            $('.btn-group').on('click','.purple#editAlbum',function(){
                var albumid = $('tr.selected>td').html();
                window.location.href="/Album/edit/id/"+albumid;
            });
            //绑定搜索按钮事件
            $('#searchbutton').click(function (e) {
                var titleOrId,selectalbumsource,selectstatus,selectorderby;
                //如果select2中的值不为空,获取select2中的data中
                if($('#search_id_or_title').val() != ''){
                    titleOrId = $('#search_id_or_title').val();
                }
                if ($('#selectalbumsource').val() != '-1') {
                    selectalbumsource = $('#selectalbumsource').val();
                }
                if ($('#selectstatus').val() != '-1') {
                    selectstatus = $('#selectstatus').val();
                }
                if ($('#selectorderby').val() != '-1') {
                    selectorderby = $('#selectorderby').val();
                }
                //ajax 前端与后台沟通参数
                var url = "/Album/btn_Search";
                var sendData = { titleOrId: titleOrId, selectalbumsource: selectalbumsource, selectstatus: selectstatus, selectorderby: selectorderby };
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
            $("#editAlbumUrl").modal({show:false});
            $("#delete-modal").modal({show:false});
            $('#alert-modal').modal({show:false});
            $("#tbartilceList").on('click','.editUrlLink',function(){
                var urlid = $(this).attr('data-id');
                //ajax
                var sendData = {urlid:urlid};
                var sendUrl = '/Url/getUrlInfo/';
                $.ajax({
                    url:sendUrl,
                    data:sendData,
                    type:'POST',
                    dataType:'json',
                    success:function(data){
                        if(data != '0'){
                            $('#editAlbumUrl input[name=id]').val(data['id']);
                            $('#editAlbumUrl input[name=requestpath]').val(data['requestpath']);
                            $('#editAlbumUrl select[name=modeltype]>option').each(function(i){
                                if($(this).val() == data['modeltype']){
                                    $(this).attr('selected','true');
                                }
                            });
                            $('#editAlbumUrl select[name=isjump]>option').each(function(i){
                                if($(this).val() == data['isjump']){
                                    $(this).attr('selected','true');
                                }
                            });
                            $('#editAlbumUrl select[name=status]>option').each(function(i){
                                if($(this).val() == data['status']){
                                    $(this).attr('selected','true');
                                }
                            });
                            $("#editAlbumUrl").modal();
                        }
                    }
                });
            });
            //绑定 modal-edit url提交事件
            $('#updateUrlForAlbum').click(function(){
                var id,requestpath,modeltype,isjump,status;
                //如果select2中的值不为空,获取select2中的data中
                id =  $('#editAlbumUrl input[name=id]').val();
                if( $('#editAlbumUrl input[name=requestpath]').val() != ''){
                    requestpath = $('#editAlbumUrl input[name=requestpath]').val();
                }
                if( $('#editAlbumUrl select[name=modeltype]').val() != ''){
                    modeltype = $('#editAlbumUrl select[name=modeltype]').val();
                }
                if( $('#editAlbumUrl select[name=isjump]').val() != ''){
                    isjump = $('#editAlbumUrl select[name=isjump]').val();
                }
                if( $('#editAlbumUrl select[name=status]').val() != ''){
                    status = $('#editAlbumUrl select[name=status]').val();
                }
                //ajax 前端与后台沟通参数
                var url = "/Url/update";
                var sendData = { id: id, requestpath: requestpath, modeltype: modeltype, isjump: isjump,status:status };
                $.ajax({
                    url: url,
                    data: sendData,
                    type: 'POST',
                    success: function (data) {
                        if (data == "1") {
                            $("#editAlbumUrl").modal('hide');
                            that.currentTable.fnDraw();
                        }
                    }
                });
            });
            //绑定 modal-delete 删除事件
            $('#deleteForAlbum').click(function(){
                var albumid = $('#delete-modal input[name=albumid]').val();
                var sendUrl = "/Album/delete/";
                var sendData = {id:albumid};
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
            $(".portlet-body").on('click','.deleteAlbum,.black#deleteAlbum',function(){
                var albumid = $(this).attr('data-id');
                if(albumid == undefined){
                    albumid = $('tr.selected>td').html();
                }
                $('#delete-modal input[name=albumid]').val(albumid);
                $("#delete-modal").modal('show');
            });
            $("#tbartilceList").on('click','.resumeAlbum',function(){
                var albumid = $(this).attr('data-id');
                var sendUrl = "/Album/resume/";
                var sendData = {id:albumid};
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
            $('#tbalbumList tbody').on( 'click', 'tr', function () {
                if ( $(this).hasClass('selected') ) {
                    $(this).removeClass('selected');
                }
                else {
                    that.currentTable.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                }
                if(that.currentTable.$('tr.selected').length > 0){
                    $('#editAlbum').addClass('purple');
                    if($('tr.selected .deleteAlbum').length >0){
                        $('#deleteAlbum').addClass('black');
                    }else{
                        $('#deleteAlbum').removeClass('black');
                    }
                }else{
                    $('#editAlbum').removeClass('purple');
                    $('#deleteAlbum').removeClass('black');
                }
            } );
        }
    };
    $(function () {
        AlbumManager.init();
    });
})(window);
