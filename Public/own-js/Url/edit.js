(function () {
    var UrleditManager = {
        //初始化
        init: function () {
            var that = this;
            that.bindEvent();
        },
        currentTable: null,
        bindEvent: function () {  
            $('#selOY2').select2();
        }
    };
    $(function () {
        UrleditManager.init();
    });
})(window);
