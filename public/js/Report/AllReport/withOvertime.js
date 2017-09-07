(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
        app.searchTable('withOTReport', [], true);
        app.pdfExport(
                'withOTReport',
                {

                }
        );
        var $withOTReport = $('#withOTReport');
        var $fromDate = $('#fromDate');
        var $toDate = $('#toDate');
        var $search = $('#search');
        app.initializeKendoGrid($withOTReport, [
            {field: "COMPANY_NAME", title: "Company"},
            {field: "DEPARTMENT_NAME", title: "Department"},
            {field: "EMPLOYEE_ID", title: "Id"},
            {field: "FULL_NAME", title: "Name"},
            {field: "DAYOFF", title: "Dayoff"},
            {field: "PRESENT", title: "Present"},
            {field: "HOLIDAY", title: "Holiday"},
            {field: "LEAVE", title: "Leave"},
            {field: "PAID_LEAVE", title: "Paid Leave"},
            {field: "UNPAID_LEAVE", title: "Unpaid Leave"},
            {field: "ABSENT", title: "Absent"},
            {field: "OVERTIME_HOUR", title: "Overtime Hour"},
            {field: "TRAVEL", title: "Travel"},
            {field: "TRAINING", title: "Training"},
            {field: "WORK_ON_HOLIDAY", title: "Work on Holiday"},
            {field: "WORK_ON_DAYOFF", title: "Work on Dayoff"},
        ], 'Test.xlsx');

        $search.on('click', function () {
            var data = document.searchManager.getSearchValues();
            data['fromDate'] = $fromDate.val();
            data['toDate'] = $toDate.val();
            app.pullDataById(document.withOvertimeWs, data).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($withOTReport, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

    });
})(window.jQuery, window.app);