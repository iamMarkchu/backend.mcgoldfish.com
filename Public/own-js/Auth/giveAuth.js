(function () {
    var GiveAuthManager = {
        //初始化
        init: function () {
            var that = this;
            that.bindEvent();
        },
        bindEvent: function () {
            // var role_id = $('input[name=role_id]').val();
            // $.ajax({
            //     url: '/Auth/getAccess/',
            //     data: {role_id:role_id},
            //     type: 'POST',
            //     success: function (data) {
            //         $('.checkinput').each(function(){
            //             if( $.inArray(this.value,data )>-1 ){
            //                 alert(this.value);
            //                 $(this).prop('checked',true);
            //             }
            //         });
            //     }
            // });
            // $('.oneLevel,.twoLevel').click(function(){
            //     var className = $(this).attr('class');
            //     if(className == 'oneLevel'){
            //         if($(this).prop('checked') == 'checked'){
            //             $(this).parents('.authdivone').siblings('.authdivtwo').find('input').prop('checked',this.checked);
            //         }else{
            //             $(this).parents('.authdivone').siblings('.authdivtwo').find('input').prop('checked',this.checked);
            //         }
            //     }else if(className == 'twoLevel'){
            //         alert($(this).attr('checked'));
            //     }
            // });
        }
    };
    $(function () {
        GiveAuthManager.init();
    });
})(window);
