(function ($) {
    'use strict';
    $(document).ready(function () {
         var $table = $('#leaveTable');
         
         app.initializeKendoGrid($table, [
            {field: "LEAVE_ENAME", title: "Leave Name"},
            {field: "ALL_TOTAL_DAYS", title: "Total Days"},
            {field: "LEAVE_TAKEN", title: "Leave taken"},
            {field: "BALANCE", title: "Available Days"}
        ], "Leave Balanace List.xlsx");
        
        
        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
        
        
        app.searchTable('leaveTable',['LEAVE_ENAME']);
        
        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                    'LEAVE_ENAME': 'Leave',
                    'ALL_TOTAL_DAYS': 'Total Days',
                    'LEAVE_TAKEN':'Leave Taken',
                    'BALANCE':'Available Days'
                }, 'Leave Balanace List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                    'LEAVE_ENAME': 'Leave',
                    'ALL_TOTAL_DAYS': 'Total Days',
                    'LEAVE_TAKEN':'Leave Taken',
                    'BALANCE':'Available Days'
                }, 'LeaveBalanaceList');
        });
        

//        app.pdfExport(
//                'leaveTable',
//                {
//                    'LEAVE_ENAME': 'Leave',
//                    'ALL_TOTAL_DAYS': 'Total Days,',
//                    'LEAVE_TAKEN':'Leave Taken',
//                    'BALANCE':'Available Days'
//                
//                });
        
//        function gridDataBound(e) {
//            var grid = e.sender;
//            if (grid.dataSource.total() == 0) {
//                var colCount = grid.columns.length;
//                $(e.sender.wrapper)
//                        .find('tbody')
//                        .append('<tr class="kendo-data-row"><td colspan="' + colCount + '" class="no-data">There is no data to show in the grid.</td></tr>');
//            }
//        }
//        ;
//        $("#export").click(function (e) {
//            var grid = $("#leaveTable").data("kendoGrid");
//            grid.saveAsExcel();
//        });
    });
})(window.jQuery, window.app);
