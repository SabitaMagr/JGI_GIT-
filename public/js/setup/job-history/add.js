(function ($, app) {
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate');

        var $employeeId = $("#employeeId");
        var $serviceEventTypeId = $("#serviceEventTypeId");

        var $toCompanyId = $('#toCompanyId');
        var $toBranchId = $('#toBranchId');
        var $toDepartmentId = $('#toDepartmentId');
        var $toDesignationId = $('#toDesignationId');
        var $toPositionId = $('#toPositionId');
        var $toServiceTypeId = $('#toServiceTypeId');
        var $toSalary = $("#toSalary");
        var $startDate = $('#startDate');


        app.floatingProfile.setDataFromRemote($employeeId.val());

        var getPreviousHistory = function (startDate, employeeId) {
            if (typeof startDate === "undefined" || typeof employeeId === "undefined" || startDate == null || employeeId == null || employeeId == -1) {
                return;
            }

            app.pullDataById(document.wsGetPreviousHistory, {
                employeeId: employeeId,
                startDate: startDate
            }).then(function (response) {
                var data = response.data;
                if (typeof data === "undefined" || data == null) {
                    return;
                }
                console.log(data);
                $serviceEventTypeId.select2({value: data.SERVICE_EVENT_TYPE_ID});
                $toCompanyId.select2({value: data.TO_COMPANY_ID});
                $toBranchId.select2({value: data.TO_BRANCH_ID});
                $toDepartmentId.select2({value: data.TO_DEPARTMENT_ID});


            }, function (error) {
                console.log(error)
            });
        };

        $employeeId.on('change', function () {
            var $this = $(this);
            var value = $this.val();
            app.floatingProfile.setDataFromRemote(value);
            getPreviousHistory($startDate.val(), value);

        });
        $startDate.on('change', function () {
            var $this = $(this);
            var value = $this.val();
            getPreviousHistory(value, $employeeId.val());
        });


    });
})(window.jQuery, window.app);


