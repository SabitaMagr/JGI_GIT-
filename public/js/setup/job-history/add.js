(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $employeeId = $("#employeeID");
        var $fromServiceTypeId = $('#fromServiceTypeId');
        var $fromBranchId = $('#fromBranchId');
        var $fromDepartmentId = $('#fromDepartmentId');
        var $fromDesignationId = $('#fromDesignationId');
        var $fromPositionId = $('#fromPositionId');

//        if (typeof document.employeeId !== "undefined") {
//            $employeeId.val(document.employeeId);
//        } else {
//        }

//            $employeeId.select2();

        app.addDatePicker(
                $("#startDate"),
                $("#endDate"));


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
        pullEmployeeDetail($employeeId.val());


        app.floatingProfile.setDataFromRemote($employeeId.val());
        $employeeId.on("change", function () {
            var employeeId = $(this).val();
            app.floatingProfile.setDataFromRemote($employeeId.val());
            pullEmployeeDetail($employeeId.val())
        });





    });
})(window.jQuery, window.app);


