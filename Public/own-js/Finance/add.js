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
            $('select[name="merchant"]').select2();
            $('select[name="belong"]').select2();
            $('#addFinanceMerchant').modal({show:false});
            $('#addOneMerchant').click(function(){
                 $('#addFinanceMerchant').modal('show');
            });
            $('#addMerchantForFinance').click(function(){
                var name
                //如果select2中的值不为空,获取select2中的data中
                if( $('#addFinanceMerchant input[name=name]').val() != ''){
                    name = $('#addFinanceMerchant input[name=name]').val();
                }
                //ajax 前端与后台沟通参数
                var url = "/Merchant/insert";
                var sendData = {name:name};
                //console.log(sendData);
                $.ajax({
                    url: url,
                    data: sendData,
                    dataType:'json',
                    type: 'POST',
                    success: function (data) {
                        console.log(data);
                        $("#addFinanceMerchant").modal('hide');
                        $('#alert-modal .alert-data-title').html('添加成功!');
                        $('#alert-modal').modal();
                        $('select[name="merchant"]').append('<option value="'+data.name+'" selected="selected" >'+data.name+'</option>').trigger('change');
                    }
                });
            });
        }
    };
    $(function () {
        FinanceaddManager.init();
    });
})(window);
