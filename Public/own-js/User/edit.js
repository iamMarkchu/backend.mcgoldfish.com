(function () {
    var UsereditManager = {
        //初始化
        init: function () {
            var that = this;
            that.bindEvent();
        },
        currentTable: null,
        bindEvent: function () {
            var that = this;    
            //绑定新增文章点击事件
            $('#selectrole').select2();
        }
    };
    $(function () {
        UsereditManager.init();
    });
})(window);
