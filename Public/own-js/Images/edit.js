(function () {
    var ImageeditManager = {
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
        }
    };
    $(function () {
        ImageeditManager.init();
    });
})(window);
