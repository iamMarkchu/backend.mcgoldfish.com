(function () {
    var userIndexManager = {
        init: function () {
            var that = this;
            that.bindEvent();
        },
        bindEvent: function () {
            var that = this;    
            $('#addUser').click(function(e){
                window.location.href="/user/add.html";
            });
        }
    };
    $(function () {
        userIndexManager.init();
    });
})(window);
