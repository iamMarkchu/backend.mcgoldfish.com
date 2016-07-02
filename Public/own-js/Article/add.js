(function () {
    var ArticleAddManager = {
        //初始化
        init: function () {
            var that = this;
            that.bindEvent();
        },
        currentTable: null,
        bindEvent: function () {
            var that = this;    
            //绑定新增文章点击事件
            $('#selOY2').select2();
            $('#tag_multi_select2').select2();
            $('#addArticleTag').modal({show:false});
            $('#addArticleCategory').modal({show:false});
            $('#alert-modal').modal({show:false});
            $('#addOneTag').click(function(){
                 $('#addArticleTag').modal('show');
            });
            $('#addOneCategory').click(function(){
                 $('#addArticleCategory').modal('show');
            });
            $('#addTagForArticle').click(function(){
                var displayname,displayorder;
                //如果select2中的值不为空,获取select2中的data中
                if( $('#addArticleTag input[name=displayname]').val() != ''){
                    displayname = $('#addArticleTag input[name=displayname]').val();
                }
                if( $('#addArticleTag input[name=displayorder]').val() != ''){
                    displayorder = $('#addArticleTag input[name=displayorder]').val();
                }
                //ajax 前端与后台沟通参数
                var url = "/Tag/insert";
                var sendData = { displayname: displayname, displayorder: displayorder};
                //console.log(sendData);
                $.ajax({
                    url: url,
                    data: sendData,
                    dataType:'json',
                    type: 'POST',
                    success: function (data) {
                        $("#addArticleTag").modal('hide');
                        $('#alert-modal .alert-data-title').html('添加成功!');
                        $('#alert-modal').modal();
                        $('#tag_multi_select2').append('<option value="'+data.id+'" selected="selected" >'+data.displayname+'</option>').trigger('change');
                    }
                });
            });
            $('#addCategoryForArticle').click(function(){
                var displayname,parentcategoryid,displayorder;
                //如果select2中的值不为空,获取select2中的data中
                if( $('#addArticleCategory input[name=displayname]').val() != ''){
                    displayname = $('#addArticleCategory input[name=displayname]').val();
                }
                if( $('#addArticleCategory input[name=parentcategoryid]').val() != ''){
                    parentcategoryid = $('#addArticleCategory input[name=parentcategoryid]').val();
                }
                if( $('#addArticleCategory input[name=displayorder]').val() != ''){
                    displayorder = $('#addArticleCategory input[name=displayorder]').val();
                }
                //ajax 前端与后台沟通参数
                var url = "/Category/insert";
                var sendData = { displayname: displayname, parentcategoryid: parentcategoryid, displayorder: displayorder};
                //console.log(sendData);
                $.ajax({
                    url: url,
                    data: sendData,
                    dataType: 'json',
                    type: 'POST',
                    success: function (data) {
                        $("#addArticleCategory").modal('hide');
                        $('#alert-modal .alert-data-title').html('添加成功!');
                        $('#alert-modal').modal();
                        if(data.parentcategoryid != ''){

                        }else{
                            $('#selOY2').append('<optgroup label="'+data.displayname+'" data-id="'+data.id+'"><option value="'+data.id+'" selected="selected" >'+data.displayname+'</option></<optgroup>').trigger('change');
                        }
                    }
                });
            });
            var ue = UE.getEditor('container',{
                initialFrameHeight:'600',
                initialFrameWidth:'90%'
            });
            //ue.execCommand( "getlocaldata" );
            ue.addListener('contentChange',function(){
                var contentHtml = ue.getContent();
                $.ajax({
                    url : "/Article/saveTmpContetntToCache/",
                    data : {contentHtml:contentHtml},
                    type : "POST"
                });
            });
            $.ajax({
                url: "/Article/getTmpContentFromCache/",
                type: "POST",
                success : function(data){
                    ue.ready(function(){
                         ue.setContent(data);
                    });
                }
            });
            function clearLocalData () {
                ue.execCommand( "clearlocaldata" );
                alert("已清空草稿箱")
            }
        }
    };
    $(function () {
        ArticleAddManager.init();
    });
})(window);
