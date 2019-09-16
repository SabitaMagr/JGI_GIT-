(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        
        var $table = $("#leaveAdditionReportTable");
        var $search = $('#search');
        var columns = [
            {field: "EMPLOYEE_CODE", title: "Code", width: 90, locked: true},
            {field: "FULL_NAME", title: "Employee", width: 150, locked: true},
            {field: "LEAVE_ENAME", title: "Leave ", width: 150},
            {field: "REMARKS", title: "Remarks", width: 100},
            {field: "NO_OF_DAYS", title: "Days", width: 100},
            {field: "LEAVE_DATE", title: "Leave Date", width: 150}
        ];
//        var map = {
//            'EMPLOYEE_CODE': 'Code',
//            'EMPLOYEE_ID': 'Id',
//            'FULL_NAME': 'Name',
//            'DEPARTMENT_NAME': 'Department',
//            'FUNCTIONAL_TYPE_EDESC': 'Functional Type'
//        };

        var $leave = $('#leaveId');
        var leaveList = document.leaves;
        app.populateSelect($leave, leaveList, 'LEAVE_ID', 'LEAVE_ENAME');

        app.searchTable($table, ['EMPLOYEE_ID', 'EMPLOYEE_CODE', 'FULL_NAME']);

        app.initializeKendoGrid($table, columns, null, null, null, 'Leave Addition Report.xlsx');

        $search.on('click', function () {
            var data = document.searchManager.getSearchValues();
            data['leave_id'] = $leave.val();

            app.serverRequest(document.pullLeaveAdditionReport, data).then(function (response) {
                if (response.success) {
                    console.log(response.data);
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

        $('#excelExport').on("click", function () {
            app.excelExport($table, map, "Employee Leave Balance Report.xlsx");
        });
        $('#pdfExport').on("click", function () {
            app.exportToPDF($table, map, "Employee Leave Balance Report.pdf", 'A2');
        });

//        $("#reset").on("click", function () {
//            $(".form-control").val("");
//            document.searchManager.reset();
//        });

    });
})(window.jQuery, window.app);
