(function () {
    var NodeIndexManager = {
        init: function () {
            var that = this;
            that.currentTable = MarkBase.MarkDT('#tbNodeList', {
                "ajax": {
                    "url": '/Node/QueryData/',
                    "type": 'POST'
                },
                "columns": [
                    { "data": "id"},
                    { "data": "remark"},
                    { "data": "name" },
                    { "data": "status",
                        "render": function(data,type,row){
                            if(data == 'active'){
                                return '<span class="label label-success">'+data+'</span>';
                                //return '<span style="color:green;">'+data+'</span>';
                            }else{
                                return '<span class="label label-danger">'+data+'</span>';
                                //return data;
                            }
                        }
                    },
                    { "data": "pid" },
                    { "data": "addtime",
                        "render": function(data,type,row){
                            var returnString = data+"<br/>";
                            returnString += row.lastupdatetime;
                            return returnString;
                        }
                    },
                    {"data": "id",
                        "render": function (data, type, row) {
                            var str ='<a href="/Node/edit/id/'+data+'" data-id="'+data+'">编辑</a>|' ;
                            str += '<a href="/Node/index/pid/'+data+'" data-id="'+data+'" class="childList">子节点列表</a>|'
                            str += '<a href="/Auth/delete/id/'+data+'" data-id="'+data+'" class="deleteNode">删除</a>';
                            return str;
                        }
                    }
                ]
            });
            that.bindEvent();
        },
        currentTable: null,
        bindEvent: function () {
            var that = this;    
            $('#addNode').click(function(e){
                window.location.href="/Node/add/";
            });
            $('#searchbutton').click(function (e) {
                // var titleOrId,selectarticlesource,selectstatus,selectorderby;
                // if($('#search_id_or_title').val() != ''){
                //     titleOrId = $('#search_id_or_title').val();
                // }
                // if ($('#selectarticlesource').val() != '-1') {
                //     selectarticlesource = $('#selectarticlesource').val();
                // }
                // if ($('#selectstatus').val() != '-1') {
                //     selectstatus = $('#selectstatus').val();
                // }
                // if ($('#selectorderby').val() != '-1') {
                //     selectorderby = $('#selectorderby').val();
                // }
                var url = "/Node/btn_Search";
                //var sendData = { titleOrId: titleOrId, selectarticlesource: selectarticlesource, selectstatus: selectstatus, selectorderby: selectorderby };
                $.ajax({
                    url: url,
                    data: sendData,
                    type: 'POST',
                    success: function (data) {
                        if (data == "1") {
                            that.currentTable.fnDraw();
                        }
                    }
                });
            });
            $(".portlet-body").on('click','.deleteNode,.black#deleteNode',function(){
                var nodeid = $(this).attr('data-id');
                if(nodeid == undefined){
                    nodeid = $('tr.selected>td').html();
                }
                $('#delete-modal input[name=nodeid]').val(nodeid);
                $("#delete-modal").modal('show');
            });
            $(".portlet-body").on('click','.childList,.yellow#showNode',function(){
                var nodeid = $(this).attr('data-id');
                if(nodeid == undefined){
                    nodeid = $('tr.selected>td').html();
                }
                window.location.href = '/Node/index/pid/'+nodeid;
            });
            //绑定 modal-delete 删除事件
            var controller = 'Node';
            var controllerLower = controller.toLowerCase();
            $('#deleteFor'+controller).click(function(){
                eval("var " + controllerLower + "id=" + $('#delete-modal input[name='+controllerLower+'id]').val());
                var sendUrl = "/"+controller+"/delete/";
                var sendData = {id:eval(controllerLower+"id")};
                $.ajax({
                    url:sendUrl,
                    data:sendData,
                    type:'GET',
                    success:function(data){
                        if (data == "1") {
                            $("#delete-modal").modal('hide');
                            that.currentTable.fnDraw();
                        }
                    }
                });
            });
            $('#tbNodeList tbody').on( 'click', 'tr', function () {
                if ( $(this).hasClass('selected') ) {
                    $(this).removeClass('selected');
                }
                else {
                    that.currentTable.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                }
                if(that.currentTable.$('tr.selected').length > 0){
                    $('#editNode').addClass('purple');
                    if($('tr.selected .deleteNode').length >0){
                        $('#deleteNode').addClass('black');
                    }else{
                        $('#deleteNode').removeClass('black');
                    }
                    $('#showNode').addClass('yellow');
                }else{
                    $('#editNode').removeClass('purple');
                    $('#deleteNode').removeClass('black');
                    $('#showNode').removeClass('yellow');
                }
            } );
        }
    };
    $(function () {
        NodeIndexManager.init();
    });
})(window);
