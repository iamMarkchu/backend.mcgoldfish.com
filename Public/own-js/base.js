var MarkBase = function () {
    return {
        MarkDT: function (selection, args) {
            var defaultParams = {
                "ordering": false,
                "serverSide": true,
                "searching": false,
                "processing": true,
                "lengthMenu": [10, 20, 50, 100],
                "ajax": {},
                "columns": [],
                "language": {
                    "lengthMenu": "显示 _MENU_ 条",
                    "paginate": {
                        "first": "第一页",
                        "last": "最后一页",
                        "next": "下一页",
                        "previous": "上一页"
                    },
                    "info": "第 _START_ 条到第 _END_ 条，总共 _TOTAL_ 条数据",
                    "infoEmpty": "无结果"
                }
            };

            $.extend(defaultParams, args);

            return $(selection).dataTable(defaultParams);
        }
    };

} ();