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
            $('.checkinput').click(function(){
                if(!$(this).parents('span').hasClass('checked')){
                    if($(this).hasClass('oneLevel')){
                        $(this).parents('.authdivone').siblings('.authdivtwo').find('.twoLevel').parent('span').addClass('checked');
                        $(this).parents('.authdivone').siblings('.authdivtwo').find('.twoLevel').prop('checked','checked');
                    }else if($(this).hasClass('twoLevel')){ //如果是互斥组的,则先去除所有元素的选中状态,然后在自身加上选中状态
                        $(this).parents('.authdivtwo').siblings('.authdivone').find('.oneLevel').parent('span').addClass('checked');
                        $(this).parents('.authdivtwo').siblings('.authdivone').find('.oneLevel').prop('checked','checked');
                    }
                }else{
                   if($(this).hasClass('oneLevel')){
                        $(this).parents('.authdivone').siblings('.authdivtwo').find('.twoLevel').parent('span').removeClass('checked');
                        $(this).parents('.authdivone').siblings('.authdivtwo').find('.twoLevel').prop('checked','');
                    } 
                }
            });
        }
    };
    $(function () {
        GiveAuthManager.init();
    });
})(window);
