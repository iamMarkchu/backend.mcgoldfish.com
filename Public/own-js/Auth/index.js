(function () {
    var AuthManager = {
        //初始化
        init: function () {
            var that = this;
            that.currentTable = MarkBase.MarkDT('#tbList', {
                "ajax": {
                    "url": '/Auth/QueryData/',
                    "type": 'POST'
                },
                "columns": [
                    { "data": "id"},
                    { "data": "name" },
                    { "data": "remark"},
                    { "data": "addtime",
                        "render": function(data,type,row){
                            var returnString = data+"<br/>";
                            returnString += row.lastupdatetime;
                            return returnString;
                        }
                    },
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
                    {"data": "id",
                        "render": function(data, type,row){
                            var str ='<a href="/Auth/edit/id/'+data+'" data-id="'+data+'">编辑</a>|' ;
                            str += '<a href="/Auth/delete/id/'+data+'" data-id="'+data+'" class="deleteAuth">删除组别</a>|'
                            str += '<a href="/User/index/role_id/'+data+'" data-id="'+data+'" class="deleteNode">查看用户</a>|';
                            str += '<a href="/Auth/giveAuth/role_id/'+data+'" data-id="'+data+'" class="giveAuth">给予权限</a>';
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
            //绑定新增文章点击事件
            $('#add').click(function(e){
                window.location.href="/Auth/add/";
            });
            $('#selOY2').select2();
            //绑定搜索按钮事件
            $('#searchbutton').click(function (e) {
                var titleOrId,selectarticlesource,selectstatus,selectorderby;
                //如果select2中的值不为空,获取select2中的data中
                if($('#search_id_or_title').val() != ''){
                    titleOrId = $('#search_id_or_title').val();
                }
                if ($('#selectarticlesource').val() != '-1') {
                    selectarticlesource = $('#selectarticlesource').val();
                }
                if ($('#selectstatus').val() != '-1') {
                    selectstatus = $('#selectstatus').val();
                }
                if ($('#selectorderby').val() != '-1') {
                    selectorderby = $('#selectorderby').val();
                }
                //ajax 前端与后台沟通参数
                var url = "/Article/btn_Search";
                var sendData = { titleOrId: titleOrId, selectarticlesource: selectarticlesource, selectstatus: selectstatus, selectorderby: selectorderby };
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

        }
    };
    $(function () {
        AuthManager.init();
    });
})(window);
