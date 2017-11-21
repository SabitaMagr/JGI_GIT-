(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#leaveTable');

        app.initializeKendoGrid($table, [
            {field: "LEAVE_ENAME", title: "Leave Name"},
            {field: "TOTAL_DAYS", title: "Total Days"},
            {field: "LEAVE_TAKEN", title: "Leave taken"},
            {field: "BALANCE", title: "Available Days"}
        ]);


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });


        app.searchTable('leaveTable', ['LEAVE_ENAME']);

        var exportMap = {
            'LEAVE_ENAME': 'Leave',
            'TOTAL_DAYS': 'Total Days',
            'LEAVE_TAKEN': 'Leave Taken',
            'BALANCE': 'Available Days'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Leave Balanace List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'LeaveBalanaceList');
        });
    });
})(window.jQuery, window.app);
