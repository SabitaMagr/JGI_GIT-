(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.addDatePicker(
                $("#startDate"),
                $("#endDate"));
        var editMode = typeof document.employeeId !== "undefined";

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

        var disableEmployeeInfo = function () {
            $fromBranchId.prop("disabled", true);
            $fromServiceTypeId.prop("disabled", true);
            $fromDepartmentId.prop("disabled", true);
            $fromDesignationId.prop("disabled", true);
            $fromPositionId.prop("disabled", true);

            $serviceEventTypeId.prop("disabled", false);
            $toBranchId.prop("disabled", false);
            $toServiceTypeId.prop("disabled", false);
            $toDepartmentId.prop("disabled", false);
            $toDesignationId.prop("disabled", false);
            $toPositionId.prop("disabled", false);
        };

        var enableEmployeeInfo = function () {
            $fromBranchId.prop("disabled", false);
            $fromServiceTypeId.prop("disabled", false);
            $fromDepartmentId.prop("disabled", false);
            $fromDesignationId.prop("disabled", false);
            $fromPositionId.prop("disabled", false);

            $serviceEventTypeId.prop("disabled", true);
            $toBranchId.prop("disabled", true);
            $toServiceTypeId.prop("disabled", true);
            $toDepartmentId.prop("disabled", true);
            $toDesignationId.prop("disabled", true);
            $toPositionId.prop("disabled", true);

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
                console.log("pullEmployeeById response", success);
                if (!editMode) {
                    updateView(success.data);
                }
                checkAppointmentOption(success.data);

            }, function (failure) {
                console.log("pullEmployeeById failure", failure);
            });
        };

        $employeeId.on("change", function () {
            var employeeId = $(this).val();
            app.floatingProfile.setDataFromRemote($employeeId.val());
            if (!editMode) {
                pullEmployeeDetail($employeeId.val())
            }
        });

        app.floatingProfile.setDataFromRemote($employeeId.val());

        if (editMode) {
            $employeeId.val(document.employeeId);
            pullEmployeeDetail($employeeId.val());
            disableEmployee();
        } else {
            pullEmployeeDetail($employeeId.val());
        }
        var checkAppointmentOption = function (employeeDtl) {
            if (employeeDtl.APP_BRANCH_ID == null && employeeDtl.APP_DEPARTMENT_ID == null && employeeDtl.APP_DESIGNATION_ID == null && employeeDtl.APP_POSITION_ID == null && employeeDtl.APP_SERVICE_EVENT_TYPE_ID == null && employeeDtl.APP_SERVICE_TYPE_ID == null) {
                var selectobject = document.getElementById("serviceEventTypeId")
                var app = false;
                for (var i = 0; i < selectobject.length; i++) {
                    if (selectobject.options[i].value != 2)
                        app = true;
                }
                if (app) {
                    $serviceEventTypeId.append('<option value="2">Appoinment</option>');
                }
                $serviceEventTypeId.val(2).trigger("change");
                enableEmployeeInfo();

            } else {
                if (employeeDtl.APP_SERVICE_EVENT_TYPE_ID != 2) {
                    var selectobject = document.getElementById("serviceEventTypeId")
                    for (var i = 0; i < selectobject.length; i++) {
                        if (selectobject.options[i].value == 2)
                            selectobject.remove(i);
                    }
                }
                disableEmployeeInfo();
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

        $('form').bind('submit', function () {
            $(this).find(':disabled').removeAttr('disabled');
        });

    });
})(window.jQuery, window.app);


