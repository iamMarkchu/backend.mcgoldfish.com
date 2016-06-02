(function () {
    var NodeaddManager = {
        //初始化
        init: function () {
            var that = this;
            that.bindEvent();
        },
        currentTable: null,
        bindEvent: function () {
            var that = this;    
        }
    };
    $(function () {
        NodeaddManager.init();
    });
})(window);
