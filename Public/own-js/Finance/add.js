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
            $('select[name="merchant"],select[name="belong"],select[name="category"]').select2();
            $('input[name=where]').select2({
                minimumInputLength: 3,
                allowClear: true,
                ajax: {
                    url: "/Finance/ajaxGetMapSuggestion/",
                    dataType: 'json',
                    quietMillis: 400,
                    allowClear: true,
                    data: function (term, page) {
                        return { key: term };
                    },
                    results: function (data, page) {
                        return { results: data.data };
                    },
                    cache: true
                }
            });
            $('#addFinanceMerchant').modal({show:false});
            $('#addOneMerchant').click(function(){
                 $('#addFinanceMerchant').modal('show');
            });
            $('#addOneCategory').click(function(){
                 $('#addFinanceCategory').modal('show');
            });
            $('#addOneBelong').click(function(){
                $('#addFinanceBelong').modal('show');
            });
            $('#addMerchantForFinance').click(function(){
                var name;
                //如果select2中的值不为空,获取select2中的data中
                if( $('#addFinanceMerchant input[name=name]').val() != ''){
                    name = $('#addFinanceMerchant input[name=name]').val();
                }
                //ajax 前端与后台沟通参数
                var url = "/Merchant/insert";
                var sendData = {name:name,order:37};
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
                        $('select[name="merchant"]').append('<option value="'+data.id+'" selected="selected" >'+data.name+'</option>').trigger('change');
                    }
                });
            });
            $('#addCategoryForFinance').click(function(){
                var displayname;
                //如果select2中的值不为空,获取select2中的data中
                if( $('#addFinanceCategory input[name=displayname]').val() != ''){
                    displayname = $('#addFinanceCategory input[name=displayname]').val();
                }
                //ajax 前端与后台沟通参数
                var url = "/Public/insertFcategory";
                var sendData = {displayname:displayname};
                //console.log(sendData);
                $.ajax({
                    url: url,
                    data: sendData,
                    dataType:'json',
                    type: 'POST',
                    success: function (data) {
                        //console.log(data);
                        $("#addFinanceCategory").modal('hide');
                        $('#alert-modal .alert-data-title').html('添加成功!');
                        $('#alert-modal').modal();
                        $('select[name="category"]').append('<option value="'+data.id+'" selected="selected" >'+data.displayname+'</option>').trigger('change');
                    }
                });
            });
            $('#addBelongForFinance').click(function(){
                var name,value;
                if( $('#addFinanceBelong input[name=name]').val() != ''){
                    name = $('#addFinanceBelong input[name=name]').val();
                }
                if( $('#addFinanceBelong input[name=value]').val() != ''){
                    value = $('#addFinanceBelong input[name=value]').val();
                }
                if(name != '' && value != ''){
                    var url = "/Public/insertAsset";
                    var sendData = {name:name,value:value};
                    //console.log(sendData);
                    $.ajax({
                        url: url,
                        data: sendData,
                        dataType:'json',
                        type: 'POST',
                        success: function (data) {
                            //console.log(data);
                            $("#addFinanceBelong").modal('hide');
                            $('#alert-modal .alert-data-title').html('添加成功!');
                            $('#alert-modal').modal();
                            $('select[name="belong"]').append('<option value="'+data.id+'" selected="selected" >'+data.name+'</option>').trigger('change');
                        }
                    });
                }
            });
            // $('input[name=where]').keyup(function(){
            //     var whereWords =$(this).val();
            //     if(whereWords.length > 3){
            //         $.ajax({
            //             url:'/Finance/ajaxGetMapSuggestion/',
            //             data:{keyword:whereWords},
            //             type: 'POST',
            //             success : function(data){
            //                 console.log(data);
            //             }
            //         });
            //     }
            // });

        }
    };
    $(function () {
        FinanceaddManager.init();
    });
})(window);
