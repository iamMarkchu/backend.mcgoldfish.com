(function () {
    var ArticleEditManager = {
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
                var url = "/Category/insert";
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
            //submit
            $('.updateArticleBtn').click(function(){
                $('.updateArticleForm').submit();
            });
            var ue = UE.getEditor('container',{
                initialFrameHeight:'600',
                initialFrameWidth:'90%'
            });
            // ue.execCommand( "getlocaldata" );
            // ue.addListener('contentChange',function(){
            //     var contentHtml = ue.getContent();
            //     var articleid = $('input[name=id]').val();
            //     $.ajax({
            //         url : "/Article/saveTmpContetntToCache/",
            //         data : {contentHtml:contentHtml,articleid:articleid},
            //         type : "POST"
            //     });
            // });
            // $.ajax({
            //     url: "/Article/getTmpContentFromCache/",
            //     type: "POST",
            //     success : function(data){
            //         ue.ready(function(){
            //              ue.setContent(data);
            //         });
            //     }
            // });
            // function clearLocalData () {
            //     ue.execCommand( "clearlocaldata" );
            //     alert("已清空草稿箱")
            // }
            $('input[name=imgFile]').change(function(){
                previewImage(this);
                $('#imghead').removeClass('hidden').show();
                //console.log(this.files);
            });
            function previewImage(file)
            {
              var MAXWIDTH  = 260; 
              var MAXHEIGHT = 180;
              var div = document.getElementById('preview');
              if (file.files && file.files[0])
              {
                  //div.innerHTML ='<img id=imghead>';
                  var img = document.getElementById('imghead');
                  img.onload = function(){
                    //var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
                    //img.width  =  rect.width;
                    //img.height =  rect.height;
    //                 img.style.marginLeft = rect.left+'px';
                    //img.style.marginTop = rect.top+'px';
                  }
                  var reader = new FileReader();
                  reader.onload = function(evt){img.src = evt.target.result;}
                  reader.readAsDataURL(file.files[0]);
              }
              else //兼容IE
              {
                var sFilter='filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src="';
                file.select();
                var src = document.selection.createRange().text;
                //div.innerHTML = '<img id=imghead>';
                var img = document.getElementById('imghead');
                img.filters.item('DXImageTransform.Microsoft.AlphaImageLoader').src = src;
                //var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
                status =('rect:'+rect.top+','+rect.left+','+rect.width+','+rect.height);
                div.innerHTML = "<div id=divhead style='width:"+rect.width+"px;height:"+rect.height+"px;margin-top:"+rect.top+"px;"+sFilter+src+"\"'></div>";
              }
            }
            function clacImgZoomParam( maxWidth, maxHeight, width, height ){
                var param = {top:0, left:0, width:width, height:height};
                if( width>maxWidth || height>maxHeight )
                {
                    rateWidth = width / maxWidth;
                    rateHeight = height / maxHeight;
                     
                    if( rateWidth > rateHeight )
                    {
                        param.width =  maxWidth;
                        param.height = Math.round(height / rateWidth);
                    }else
                    {
                        param.width = Math.round(width / rateHeight);
                        param.height = maxHeight;
                    }
                }
                 
                param.left = Math.round((maxWidth - param.width) / 2);
                param.top = Math.round((maxHeight - param.height) / 2);
                return param;
            }
        }
    };
    $(function () {
        ArticleEditManager.init();
    });
})(window);
