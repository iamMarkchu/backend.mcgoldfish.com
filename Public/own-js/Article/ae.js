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
            $('select').select2();
            $('input[name=title]').blur(function(){
                var title = $(this).val();
                if (title != '') {
                    url = "/article/checkTitleDuplicated.html";
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
                url: '/article/uploadImage.html?name=image',
                dataType: 'json',
                done: function (e, data) {
                    $('#imagePreview').empty().append('<img src="'+data.result.file_path+'" />');
                    $('input[name=image]').val(data.result.file_path);
                }
            });
            var imageCount = 0
            $('#fileupload').fileupload({
                url: '/article/uploadImage.html?name=markdown',
                dataType: 'json',
                done: function (e, data) {
                    var text = '';
                    if(imageCount != 0)
                    {
                        text += "\n";
                    }
                    text +='![image]('+data.result.file_path+')';
                    $('#article-content').append(text);
                    imageCount++;
                }
            });
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
