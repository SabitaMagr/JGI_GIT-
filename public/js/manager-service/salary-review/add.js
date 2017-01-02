(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $employeeId = $('#employeeId');
        var $oldAmount = $('#oldAmount');
        var $effectiveDate = $('#effectiveDate');
        var $jobHistoryId = $('#jobHistoryId');


        var populateJobHistory = function (histories) {
            $jobHistoryId.html("");
            $jobHistoryId.append($("<option />").val(null).text("Select Service Event"));
            $.each(histories, function () {
                $jobHistoryId.append($("<option />").val(this.JOB_HISTORY_ID).text(this.SERVICE_EVENT_TYPE_NAME + "(" + this.START_DATE + ")"));
            });
        };
        var fetchEmployeeSalary = function ($this) {
            app.pullDataById(document.restfulUrl, {
                action: 'pullEmployeeById',
                data: {
                    'employeeId': $this.val()
                }
            }).then(function (success) {
                console.log(success);
                var salary = parseFloat(success.data.SALARY);
                $oldAmount.val(salary);
            }, function (failure) {
                console.log(failure);
            });
        };
        var fetchServiceEvents = function ($this) {
            app.pullDataById(document.serviceHistoryList, {
                'employeeId': $this.val()
            }).then(function (success) {
                console.log("serviceHistoryList", success);
                populateJobHistory(success.jobHistoryList);
            }, function (failure) {
                console.log("serviceHistoryList fail", failure);
            });
        };

        var fetchLastSalaryReviewDate = function ($this) {
            app.pullDataById(document.fetchLastSalaryReviewDate, {
                'employeeId': $this.val()
            }).then(function (success) {
                console.log("fetchLastSalaryReviewDate", success);
            }, function (failure) {
                console.log("fetchLastSalaryReviewDate fail", failure);
            });
        };
        app.addDatePicker($effectiveDate);
        fetchEmployeeSalary($employeeId);
        $employeeId.on('change', function () {
            fetchEmployeeSalary($(this));
            fetchServiceEvents($(this));
        });
        populateJobHistory();

    });
})(window.jQuery, window.app);