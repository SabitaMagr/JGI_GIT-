(function ($, app) {
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate');

        var selectobject = document.getElementById("serviceEventTypeId")

        var $employeeId = $("#employeeID");

        var $fromServiceTypeId = $('#fromServiceTypeId');
        var $fromBranchId = $('#fromBranchId');
        var $fromDepartmentId = $('#fromDepartmentId');
        var $fromDesignationId = $('#fromDesignationId');
        var $fromPositionId = $('#fromPositionId');

        var $toServiceTypeId = $('#toServiceTypeId');
        var $toBranchId = $('#toBranchId');
        var $toDepartmentId = $('#toDepartmentId');
        var $toDesignationId = $('#toDesignationId');
        var $toPositionId = $('#toPositionId');

        var $serviceEventTypeId = $("#serviceEventTypeId");


        var toggleEmployeeInfo = function (flag) {
            $fromBranchId.prop("disabled", !flag);
            $fromServiceTypeId.prop("disabled", !flag);
            $fromDepartmentId.prop("disabled", !flag);
            $fromDesignationId.prop("disabled", !flag);
            $fromPositionId.prop("disabled", !flag);

            $serviceEventTypeId.prop("disabled", flag);
            $toBranchId.prop("disabled", flag);
            $toServiceTypeId.prop("disabled", flag);
            $toDepartmentId.prop("disabled", flag);
            $toDesignationId.prop("disabled", flag);
            $toPositionId.prop("disabled", flag);

        };

        var disableEmployee = function () {
            $employeeId.prop("disabled", true);
        };

        var updateView = function (employee) {
            $fromServiceTypeId.val(employee.SERVICE_TYPE_ID).trigger("change");
            $fromBranchId.val(employee.BRANCH_ID).trigger("change");
            $fromDepartmentId.val(employee.DEPARTMENT_ID).trigger("change");
            $fromDesignationId.val(employee.DESIGNATION_ID).trigger("change");
            $fromPositionId.val(employee.POSITION_ID).trigger("change");
        };

        var pullEmployeeDetail = function (employeeId) {
            app.pullDataById(document.restfulUrl, {
                action: 'pullEmployeeById',
                data: {employeeId: employeeId}
            }).then(function (success) {
                checkAppointmentOption(success.data);

            }, function (failure) {
                console.log("pullEmployeeById failure", failure);
            });
        };

        $employeeId.on("change", function () {
            var employeeId = $(this).val();
            app.floatingProfile.setDataFromRemote($employeeId.val());
        });

        app.floatingProfile.setDataFromRemote($employeeId.val());

        $employeeId.val(document.employeeId);
        pullEmployeeDetail($employeeId.val());
        disableEmployee();
        var checkAppointmentOption = function (employeeDtl) {
            if (employeeDtl.APP_BRANCH_ID == null && employeeDtl.APP_DEPARTMENT_ID == null && employeeDtl.APP_DESIGNATION_ID == null && employeeDtl.APP_POSITION_ID == null && employeeDtl.APP_SERVICE_TYPE_ID == null) {
                var selectobject = document.getElementById("serviceEventTypeId");
                var optionValue = [];
                for (var i = 0; i < selectobject.length; i++) {
                    optionValue.push(selectobject.options[i].value);
                }
                if (optionValue.indexOf("2") == -1) {
                    $serviceEventTypeId.append('<option value="2">Appointment</option>');
                }
                $serviceEventTypeId.val(2).trigger("change");
                toggleEmployeeInfo(true);
            } else {
                var selectobject2 = document.getElementById("serviceEventTypeId");
                var app = [];
                for (var i = 0; i < selectobject2.length; i++) {
                    app.push(selectobject2.options[i].value);
                }
                if (app.indexOf("2") == -1) {
                    $serviceEventTypeId.append('<option value="2">Appoinment</option>');
                }
                var formServiceEventTypeId = parseInt($serviceEventTypeId.val());
                if (formServiceEventTypeId == 2) {
                    toggleEmployeeInfo(true);
                } else {
                    var selectobject3 = document.getElementById("serviceEventTypeId")
                    for (var i = 0; i < selectobject3.length; i++) {
                        if (selectobject3.options[i].value == 2)
                            selectobject3.remove(i);
                    }
                    toggleEmployeeInfo(false);
                }

            }
        };
        $fromServiceTypeId.on("change", function () {
            $toServiceTypeId.val($(this).val()).trigger("change");
        });
        $fromBranchId.on("change", function () {
            $toBranchId.val($(this).val()).trigger("change");
        });
        $fromDepartmentId.on("change", function () {
            $toDepartmentId.val($(this).val()).trigger("change");
        });
        $fromDesignationId.on("change", function () {
            $toDesignationId.val($(this).val()).trigger("change");
        });
        $fromPositionId.on("change", function () {
            $toPositionId.val($(this).val()).trigger("change");
        });

        app.setLoadingOnSubmit("jobHistory-form", function () {
            localStorage.setItem("ServiceJobHistorylastEmployeeId", $employeeId.val());
            return true;
        });

        $('form').bind('submit', function () {
            $(this).find(':disabled').removeAttr('disabled');
        });

        //localstroage if set
        var lastEmpId = localStorage.getItem("ServiceJobHistorylastEmployeeId");
        if (lastEmpId != null) {
            $employeeId.val(lastEmpId).change();
            app.floatingProfile.setDataFromRemote(lastEmpId);
        }

    });
})(window.jQuery, window.app);


