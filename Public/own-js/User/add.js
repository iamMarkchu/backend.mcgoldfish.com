(function () {
    var UseraddManager = {
        //初始化
        init: function () {
            var that = this;
            that.bindEvent();
        },
        currentTable: null,
        bindEvent: function () {
            var that = this;    
            $('#selectrole').select2();
        }
    };
    $(function () {
        UseraddManager.init();
    });
})(window);
