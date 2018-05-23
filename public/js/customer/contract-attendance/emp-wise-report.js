

(function ($) {
    'use strict';
    $(document).ready(function () {

        var $table = $('#grid');
        var $monthSelect = $('#monthSelect');
        app.populateSelect($monthSelect, document.monthList, 'MONTH_ID', 'MONTH_EDESC', 'Select An Month', '');


        var columns = [
            {field: "FULL_NAME", title: "Name", width: 150},
            {field: "CONTRACT_NAME", title: "Contract", width: 130},
            {field: "LOCATION_NAME", title: "Location", width: 130},
            {field: "DESIGNATION_TITLE", title: "Designation", width: 130},
            {field: "DUTY_TYPE_NAME", title: "Duty", width: 100},
            {field: "PRESENT_DAYS", title: "Present", width: 100},
            {field: "TOTAL_NORMAL_HOUR", title: "Normal Hour", width: 100},
            {field: "TOTAL_OT_HOUR", title: "Ot Hour", width: 100},
//            {field: "ABSENT_DAYS", title: "Absent", width: 100},
//            {field: "DAY_OFF", title: "DayOff", width: 100},
//            {field: "PAID_HOLIDAY", title: "Paid Holiday", width: 100},
        ];


        app.searchTable($table, ['FULL_NAME',
            'CONTRACT_NAME',
            'LOCATION_NAME',
            'DESIGNATION_TITLE',
            'DUTY_TYPE_NAME',
            'PRESENT_DAYS',
            'TOTAL_NORMAL_HOUR',
            'TOTAL_OT_HOUR',
//            'ABSENT_DAYS',
//            'DAY_OFF',
//            'PAID_HOLIDAY'
        ]);

        app.initializeKendoGrid($table, columns);

        $('#viewBtn').on('click', function () {

            var selectedMonthVal = $monthSelect.val();
            if (selectedMonthVal == '') {
                app.errorMessage(' Month is not selected ', 'error');
                return;
            }

            app.serverRequest(document.pullEmpWiseMonthlyReport, {
                monthId: selectedMonthVal
            }
            ).then(function (response) {
                console.log(response);
                app.renderKendoGrid($table, response.data);
            });



        });
    });
}
)(window.jQuery);