(function () {
    var ArticleManager = {
        //初始化
        init: function () {
            var that = this;
            that.bindEvent();
        },
        currentTable: null,
        bindEvent: function () {
            var that = this;    
            //绑定新增文章点击事件
            //var ue = UE.getEditor('container');
            $('select').select2();
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
            $('input[name=imgFile]').change(function(){
                previewImage(this);
                $('#imghead').removeClass('hidden').show();
            });
            $('input[name=title]').blur(function(){
                var title = $(this).val();
                if (title != '') {
                    url = "/Article/checkTitleDuplicated/";
                    sendData = {title:title};
                    $.ajax({
                        url : url,
                        data : sendData,
                        type : 'POST',
                        dateType : 'string',
                        success : function(data){
                            if(data == '0'){
                                alert('标题与现有标题重复,请修改!');
                            }
                        }
                    });
                }
            });
            $('input[name=imageFile]').fileupload({
                url: '/article/uploadImage.html',
                dataType: 'json',
                done: function (e, data) {
                    $('#imagePreview').empty().append('<img src="'+data.result.file_path+'" />');
                    $('input[name=image]').val(data.result.file_path);
                }
            }).prop('disabled', !$.support.fileInput)
                .parent().addClass($.support.fileInput ? undefined : 'disabled');
            $('#fileupload').fileupload({
                url: '/article/uploadImage.html',
                dataType: 'json',
                done: function (e, data) {
                    $('#files').append('<p>'+data.result.file_path+ '</p>');
                }
            }).prop('disabled', !$.support.fileInput)
                .parent().addClass($.support.fileInput ? undefined : 'disabled');
            $('.view-content').click(function () {
                var content = $('#article-content').val();
                if(content != '' || content != undefined)
                {
                    $.ajax({
                        url: '/article/viewMarkdown.html',
                        data: {content: content},
                        type: 'POST',
                        success: function (data) {
                            $('#markhtml').html(data['htmlContent']);
                        }
                    });
                }
            });
        }
    };
    $(function () {
        ArticleManager.init();
    });
})(window);
