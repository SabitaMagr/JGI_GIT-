(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.datePickerWithNepali('fromDate', 'nepalifromDate');

        var $employeeId = $('#employeeId');
        var $submitBtn = $('#btn-reAttendnace');

        app.populateSelect($employeeId, document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', '---', '');
        $submitBtn.on('click', function () {
            var selectedDate = $('#fromDate').val();
            if (!selectedDate) {
                app.showMessage("Please select a date First");
                return;
            }
            var employeeList = [];
            var selectedEmployees = [];
            $.each($("#employeeId option:selected"), function () {
                var employeeid = $(this).val();
                var employeeData = {
                    EMPLOYEE_ID: employeeid,
                    ATTENDANCE_DATE: selectedDate
                }
                selectedEmployees.push(employeeData);
            });

            if (selectedEmployees.length > 0) {
                employeeList = selectedEmployees;
            } else {
                var employeeListWithDate = [];
                $.each(document.employeeList, function (index, value) {
                    var employeeData = {
                        EMPLOYEE_ID: value.EMPLOYEE_ID,
                        FROM_DATE: selectedDate,
                        TO_DATE: selectedDate
                    }
                    employeeListWithDate.push(employeeData);
                });
                employeeList = employeeListWithDate;
            }
            app.bulkServerRequest(document.regenAttendanceLink, employeeList, function () {
                app.showMessage("Attendance Report Regeneration Successful.");
            }, function (data, error) {
                app.showMessage(error, 'error');
            });
        });
    });
})(window.jQuery, window.app);