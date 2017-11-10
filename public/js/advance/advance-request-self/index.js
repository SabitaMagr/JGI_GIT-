(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
//        var actiontemplateConfig = {
//            update: {
//                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
//                'params': ["ADVANCE_ID"],
//                'url': document.editLink
//            },
//            delete: {
//                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
//                'params': ["ADVANCE_ID"],
//                'url': document.deleteLink
//            }
//        };
        var columns = [
            {field: "ADVANCE_ENAME", title: "Advance", width: 150},
//            {field: ["ADVANCE_ID"], title: "Action", width: 120, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ];
//        var map = {
//            'ADVANCE_CODE': 'AdvanceCode',
//            'ADVANCE_ENAME': 'Name'
//        }
        app.initializeKendoGrid($table, columns, "Advance Request List.xlsx");
//
//        app.searchTable($table, ['ADVANCE_ENAME']);
//
//        $('#excelExport').on('click', function () {
//            app.excelExport($table, map, 'Advance List.xlsx');
//        });
//        
//        $('#pdfExport').on('click', function () {
//            app.exportToPDF($table, map, 'Advance List.pdf');
//        });

//        app.pullDataById("", {}).then(function (response) {
//            console.log(response.data);
//            app.renderKendoGrid($table, response.data);
//        }, function (error) {
//
//        });
    });
})(window.jQuery);

