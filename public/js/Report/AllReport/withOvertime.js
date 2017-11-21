(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
        app.searchTable('withOTReport', ['COMPANY_NAME', 'DEPARTMENT_NAME', 'EMPLOYEE_ID', 'FULL_NAME', 'PRESENT', 'ABSENT', 'DAYOFF', 'HOLIDAY', 'LEAVE', 'PAID_LEAVE', 'UNPAID_LEAVE',
            'OVERTIME_HOUR', 'TRAVEL', 'TRAINING', 'WORK_ON_HOLIDAY', 'WORK_ON_DAYOFF'], true);
        app.pdfExport(
                'withOTReport',
                {
                    'COMPANY_NAME': 'Company',
                    'DEPARTMENT_NAME': 'Department',
                    'EMPLOYEE_ID': 'ID',
                    'FULL_NAME': 'Name',
                    'PRESENT': 'Present',
                    'ABSENT': 'Absent',
                    'DAYOFF': 'Dayoff',
                    'HOLIDAY': 'Holiday',
                    'LEAVE': 'Leave',
                    'PAID_LEAVE': 'Paid Leave',
                    'UNPAID_LEAVE': 'Unpaid Leave',
                    'OVERTIME_HOUR': 'Overtime Hour',
                    'TRAVEL': 'Travel',
                    'TRAINING': 'Training',
                    'WORK_ON_HOLIDAY': 'Work on Holiday',
                    'WORK_ON_DAYOFF': 'Work on Dayoff',
                }
        );
        var $withOTReport = $('#withOTReport');
        var $fromDate = $('#fromDate');
        var $toDate = $('#toDate');
        var $search = $('#search');
        app.initializeKendoGrid($withOTReport, [
            {field: "COMPANY_NAME", title: "Company", width: 150, locked: true},
            {field: "DEPARTMENT_NAME", title: "Department", width: 150, locked: true},
            {field: "EMPLOYEE_ID", title: "Id", width: 150, locked: true},
            {field: "FULL_NAME", title: "Name", width: 150, locked: true},
            {field: "PRESENT", title: "Present", width: 150},
            {field: "ABSENT", title: "Absent", width: 150},
            {field: "DAYOFF", title: "Dayoff", width: 150},
            {field: "HOLIDAY", title: "Holiday", width: 150},
            {field: "LEAVE", title: "Leave", width: 150},
            {field: "PAID_LEAVE", title: "Paid Leave", width: 150},
            {field: "UNPAID_LEAVE", title: "Unpaid Leave", width: 150},
            {field: "OVERTIME_HOUR", title: "Overtime Hour", width: 150},
            {field: "TRAVEL", title: "Travel", width: 150},
            {field: "TRAINING", title: "Training", width: 150},
            {field: "WORK_ON_HOLIDAY", title: "Work on Holiday", width: 150},
            {field: "WORK_ON_DAYOFF", title: "Work on Dayoff", width: 150},
        ]);

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