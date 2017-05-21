(function ($, app) {
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate');

        var $employeeId = $("#employeeID");

        var $fromServiceTypeId = $('#fromServiceTypeId');
        var $fromCompanyId = $('#fromCompanyId');
        var $fromBranchId = $('#fromBranchId');
        var $fromDepartmentId = $('#fromDepartmentId');
        var $fromDesignationId = $('#fromDesignationId');
        var $fromPositionId = $('#fromPositionId');

        var $toServiceTypeId = $('#toServiceTypeId');
        var $toCompanyId = $('#toCompanyId');
        var $toBranchId = $('#toBranchId');
        var $toDepartmentId = $('#toDepartmentId');
        var $toDesignationId = $('#toDesignationId');
        var $toPositionId = $('#toPositionId');

        var $serviceEventTypeId = $("#serviceEventTypeId");


        var companyList = [];
        var branchList = [];
        var departmentList = [];
        var designationList = [];
        var positionList = [];

        var prevAndNextHistory = document.prevAndNextHistory;

        $fromServiceTypeId.on("change", function () {
            $toServiceTypeId.val($(this).val()).trigger("change");
        });
        $fromCompanyId.on("change", function () {
            $toCompanyId.val($(this).val()).trigger("change");
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


        var updateView = function (employee) {
            if (employee.SERVICE_TYPE_ID !== null) {
                $fromServiceTypeId.val(employee.SERVICE_TYPE_ID).trigger("change");
            }

            if (employee.COMPANY_ID !== null) {
                $fromCompanyId.val(employee.COMPANY_ID).trigger("change");
            }

            if (employee.BRANCH_ID !== null) {
                $fromBranchId.val(employee.BRANCH_ID).trigger("change");
            }

            if (employee.DEPARTMENT_ID !== null) {
                $fromDepartmentId.val(employee.DEPARTMENT_ID).trigger("change");
            }

            if (employee.DESIGNATION_ID !== null) {
                $fromDesignationId.val(employee.DESIGNATION_ID).trigger("change");
            }

            if (employee.POSITION_ID !== null) {
                $fromPositionId.val(employee.POSITION_ID).trigger("change");
            }
        };

        var toggleEmployeeInfo = function (flag) {
            $fromCompanyId.prop("disabled", !flag);
            $fromBranchId.prop("disabled", !flag);
            $fromServiceTypeId.prop("disabled", !flag);
            $fromDepartmentId.prop("disabled", !flag);
            $fromDesignationId.prop("disabled", !flag);
            $fromPositionId.prop("disabled", !flag);

            $serviceEventTypeId.prop("disabled", flag);

            $toCompanyId.prop("disabled", flag);
            $toBranchId.prop("disabled", flag);
            $toServiceTypeId.prop("disabled", flag);
            $toDepartmentId.prop("disabled", flag);
            $toDesignationId.prop("disabled", flag);
            $toPositionId.prop("disabled", flag);

        };

        var disableEmployee = function () {
            $employeeId.prop("disabled", true);
        };


        var checkAppointmentOption = function (employeeDtl) {
            if (employeeDtl.APP_BRANCH_ID == null && employeeDtl.APP_DEPARTMENT_ID == null && employeeDtl.APP_DESIGNATION_ID == null && employeeDtl.APP_POSITION_ID == null && employeeDtl.APP_SERVICE_TYPE_ID == null) {
                if ($serviceEventTypeId.has('option[value="2"]').length == 0) {
                    $serviceEventTypeId.append('<option value="2">Appointment</option>');
                }

                $serviceEventTypeId.val(2).trigger("change");
                toggleEmployeeInfo(true);
            } else {
                if (2 == $serviceEventTypeId.val()) {
                    toggleEmployeeInfo(true);
                    return;
                }

                if ($serviceEventTypeId.has('option[value="2"]').length != 0) {
                    $serviceEventTypeId.find('option[value="2"]').remove();
                }
                toggleEmployeeInfo(false);

            }
        };

        var serviceEventChangeAction = function () {
            var selectedServiceEventType = $serviceEventTypeId.val();
            if (selectedServiceEventType == 2) {
                toggleEmployeeInfo(true);
            } else {
                toggleEmployeeInfo(false);
            }
        }
        $serviceEventTypeId.on('change', function () {
            serviceEventChangeAction();
        });
        serviceEventChangeAction();


        if (typeof prevAndNextHistory['prev'] !== 'undefined' || typeof prevAndNextHistory['next'] !== 'undefined') {
            var prevHistory = prevAndNextHistory['prev'];
            if (typeof prevHistory !== "undefined") {
                $('#startDate').datepicker('setStartDate', nepaliDatePickerExt.getDate(prevHistory['START_DATE']));
            }
            var nextHistory = prevAndNextHistory['next'];
            if (typeof nextHistory !== "undefined") {
                $('#startDate').datepicker('setEndDate', nepaliDatePickerExt.getDate(nextHistory['START_DATE']));
            }
        }

        app.floatingProfile.setDataFromRemote($employeeId.val());

        disableEmployee();




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


