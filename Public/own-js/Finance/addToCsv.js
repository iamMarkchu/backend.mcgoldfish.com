(function () {
    var FinanceaddManager = {
        //初始化
        init: function () {
            var that = this;
            that.bindEvent();
        },
        currentTable: null,
        bindEvent: function () {
            var that = this;    
            $('select[name=moban]').change(function(){
                var type = $(this).val();
                if(type == '0'){
                    $('input[name=amount]').val('');
                }else if(type == '1'){
                    $('input[name=amount]').val('5,支出,在地铁站,when,上海地铁,车票,mark,现金');
                }else if(type == '2'){
                    $('input[name=amount]').val('10500,收入,在公司,when,美戈信息技术有限公司,工资,mark,中国银行');
                }else if(type == '3'){
                    $('input[name=amount]').val('15,收入,在理财产品,when,陆金所,理财收益,mark,陆金所');
                }
            });
        }
    };
    $(function () {
        FinanceaddManager.init();
    });
})(window);
