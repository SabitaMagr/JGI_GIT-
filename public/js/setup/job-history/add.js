(function ($, app) {
    'use strict';
    $(document).ready(function () {
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

        var $serviceEventTypeId = $("#serviceEventTypeId");
        console.log($serviceEventTypeId.val());

        var disableEmployeeInfo = function () {
            $fromBranchId.prop("disabled", true);
            $fromServiceTypeId.prop("disabled", true);
            $fromDepartmentId.prop("disabled", true);
            $fromDesignationId.prop("disabled", true);
            $fromPositionId.prop("disabled", true);
        };

        var disableEmployee = function () {
            $employeeId.prop("disabled", true);
        };

        var updateView = function (employee) {
            $fromServiceTypeId.val(employee.SERVICE_TYPE_ID);
            $fromBranchId.val(employee.BRANCH_ID);
            $fromDepartmentId.val(employee.DEPARTMENT_ID);
            $fromDesignationId.val(employee.DESIGNATION_ID);
            $fromPositionId.val(employee.POSITION_ID);
        };
        var pullEmployeeDetail = function (employeeId) {
            app.pullDataById(document.restfulUrl, {
                action: 'pullEmployeeById',
                data: {employeeId: employeeId}
            }).then(function (success) {
                console.log("pullEmployeeById response", success);
                updateView(success.data);
                var employeeDtl = success.data;
                if (employeeDtl.APP_BRANCH_ID == null && employeeDtl.APP_DEPARTMENT_ID == null && employeeDtl.APP_DESIGNATION_ID == null && employeeDtl.APP_POSITION_ID == null && employeeDtl.APP_SERVICE_EVENT_TYPE_ID == null && employeeDtl.APP_SERVICE_TYPE_ID == null) {
                    var selectobject = document.getElementById("serviceEventTypeId")
                    var app = false;    
                    for (var i = 0; i < selectobject.length; i++) {
                            if (selectobject.options[i].value != 2)
                               app = true;
                    }
                    if(app){
                        $serviceEventTypeId.append('<option value="2">Appoinment</option>');
                    }
                } else {
                    var selectobject = document.getElementById("serviceEventTypeId")
                    for (var i = 0; i < selectobject.length; i++) {
                        if (selectobject.options[i].value == 2)
                            selectobject.remove(i);
                    }
                }
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
//        disableEmployeeInfo();
        if (editMode) {
            $employeeId.val(document.employeeId);
            disableEmployee();
        } else {
            pullEmployeeDetail($employeeId.val());
        }




    });
})(window.jQuery, window.app);


