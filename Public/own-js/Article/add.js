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
                var url = "/Home/Tag/insert";
                var sendData = { displayname: displayname, displayorder: displayorder};
                //console.log(sendData);
                $.ajax({
                    url: url,
                    data: sendData,
                    type: 'POST',
                    success: function (data) {
                        if (data == "1") {
                            $("#addArticleTag").modal('hide');
                            $('#alert-modal .alert-data-title').html('添加成功!');
                            $('#alert-modal').modal();
                        }
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
                var url = "Category/insert";
                var sendData = { displayname: displayname, parentcategoryid: parentcategoryid, displayorder: displayorder};
                //console.log(sendData);
                $.ajax({
                    url: url,
                    data: sendData,
                    type: 'POST',
                    success: function (data) {
                        if (data == "1") {
                            $("#addArticleCategory").modal('hide');
                            $('#alert-modal .alert-data-title').html('添加成功!');
                            $('#alert-modal').modal();
                        }
                    }
                });
            });
        }
    };
    $(function () {
        ArticleAddManager.init();
    });
})(window);
