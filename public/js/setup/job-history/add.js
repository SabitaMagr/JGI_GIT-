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
            }, function (failure) {
                console.log("pullEmployeeById failure", failure);
            });
        };


        $employeeId.on("change", function () {
            var employeeId = $(this).val();
            app.floatingProfile.setDataFromRemote($employeeId.val());
            // console.log($serviceEventTypeId.val());
            var selectobject=document.getElementById("serviceEventTypeId")
            for (var i=0; i<selectobject.length; i++){
            if (selectobject.options[i].value == 'Appointment' )
               selectobject.remove(i);
            }
      
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


