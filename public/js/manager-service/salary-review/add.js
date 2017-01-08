(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $employeeId = $('#employeeId');
        var $oldAmount = $('#oldAmount');
        var $effectiveDate = $('#effectiveDate');
        var $effectiveNepaliDate = $('#effectiveNepaliDate');
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
            console.log("currentMonth", document.currentMonth.FROM_DATE);

            app.pullDataById(document.fetchLastSalaryReviewDate, {
                'employeeId': $this.val(),
                'fromDate': document.currentMonth['FROM_DATE'],
                'toDate': document.currentMonth['TO_DATE']
            }).then(function (success) {
                console.log("fetchLastSalaryReviewDate", success);
                if (success.lastReviewDateThisMonth) {
                    $effectiveDate.datepicker('setStartDate', new Date(Date.parse(success.lastReviewDateThisMonth.EFFECTIVE_DATE)));
                } else {
                    $effectiveDate.datepicker('setStartDate', new Date(Date.parse(document.currentMonth.FROM_DATE)));
                }
            }, function (failure) {
                console.log("fetchLastSalaryReviewDate fail", failure);
            });
        };
//        app.addDatePicker($effectiveDate);
        $effectiveDate.datepicker({format: 'dd-M-yyyy', autoclose: true}).on('changeDate', function () {
            $effectiveNepaliDate.val(nepaliDatePickerExt.fromEnglishToNepali($(this).val()));
        });

        $effectiveNepaliDate.nepaliDatePicker({
            onChange: function () {
                $effectiveDate.val(nepaliDatePickerExt.fromNepaliToEnglish($effectiveNepaliDate.val()));

            }
        });
        fetchEmployeeSalary($employeeId);
        fetchServiceEvents($employeeId);
        fetchLastSalaryReviewDate($employeeId);
        $employeeId.on('change', function () {
            fetchEmployeeSalary($(this));
            fetchServiceEvents($(this));
            fetchLastSalaryReviewDate($(this));
        });
        populateJobHistory();

    });
})(window.jQuery, window.app);