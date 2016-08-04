(function () {
    var ImageaddManager = {
        //初始化
        init: function () {
            var that = this;
            that.bindEvent();
        },
        currentTable: null,
        bindEvent: function () {
            var imageCount = 0;
            $('.addModeImage').click(function(){
                cloneImage = $('.preToCopyImages').clone();
                cloneImage.removeClass('preToCopyImages').addClass('clone_'+imageCount++).append('<div class="controls"><a class="btn yellow cancelImage">点击取消</a></div>');
                $('.preToCopyImages').after(cloneImage);
            });
            $('.addImagesDiv').on('click','.cancelImage',function(){
                alert('111');
            });
        }
    };
    $(function () {
        ImageaddManager.init();
    });
})(window);
