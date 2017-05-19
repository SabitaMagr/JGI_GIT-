(function ($, app) {
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate');

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


        var branchList = [];
        var departmentList = [];
        var designationList = [];
        var positionList = [];


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
            if (employee.SERVICE_TYPE_ID !== null) {
                $fromServiceTypeId.val(employee.SERVICE_TYPE_ID).trigger("change");
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

        var pullEmployeeDetail = function (employeeId) {
            app.pullDataById(document.wsPullEmployeeDetailWithOptions, {
                employeeId: employeeId
            }).then(function (success) {
                var employeeDetail = success.data.employeeDetail;

                branchList = success.data.branchList;
                departmentList = success.data.departmentList;
                designationList = success.data.designationList;
                positionList = success.data.positionList;

                populateList($fromBranchId, branchList, "BRANCH_ID", "BRANCH_NAME", "----");
                populateList($toBranchId, branchList, "BRANCH_ID", "BRANCH_NAME", "----");

                populateList($fromDepartmentId, departmentList, "DEPARTMENT_ID", "DEPARTMENT_NAME", "----");
                populateList($toDepartmentId, departmentList, "DEPARTMENT_ID", "DEPARTMENT_NAME", "----");

                populateList($fromDesignationId, designationList, "DESIGNATION_ID", "DESIGNATION_TITLE", "----");
                populateList($toDesignationId, designationList, "DESIGNATION_ID", "DESIGNATION_TITLE", "----");

                populateList($fromPositionId, positionList, "POSITION_ID", "POSITION_NAME", "----");
                populateList($toPositionId, positionList, "POSITION_ID", "POSITION_NAME", "----");

                updateView(employeeDetail);
                checkAppointmentOption(employeeDetail);

            }, function (failure) {
                console.log("pullEmployeeById failure", failure);
            });
        };

        $employeeId.on("change", function () {
            var $this = $(this);
            app.floatingProfile.setDataFromRemote($this.val());
            pullEmployeeDetail($this.val())
        });

        app.floatingProfile.setDataFromRemote($employeeId.val());
        pullEmployeeDetail($employeeId.val());

        var checkAppointmentOption = function (employeeDtl) {
            if (employeeDtl.APP_BRANCH_ID == null && employeeDtl.APP_DEPARTMENT_ID == null && employeeDtl.APP_DESIGNATION_ID == null && employeeDtl.APP_POSITION_ID == null && employeeDtl.APP_SERVICE_TYPE_ID == null) {
                if ($serviceEventTypeId.has('option[value="2"]').length == 0) {
                    $serviceEventTypeId.append('<option value="2">Appointment</option>');
                }

                $serviceEventTypeId.val(2).trigger("change");
                toggleEmployeeInfo(true);
            } else {
                if ($serviceEventTypeId.has('option[value="2"]').length != 0) {
                    $serviceEventTypeId.find('option[value="2"]').remove();
                }
                toggleEmployeeInfo(false);
            }
        };
        $fromServiceTypeId.on("change", function () {
            $toServiceTypeId.val($(this).val()).trigger("change");
        });
        $fromBranchId.on("change", function () {
//            populateList($fromDepartmentId, search(departmentList, {'BRANCH_ID': $(this).val()}), "DEPARTMENT_ID", "DEPARTMENT_NAME", "----");
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

//        $toBranchId.on('change', function () {
//            $this = $(this);
//            populateList($toDepartmentId, search(departmentList, {'BRANCH_ID': $(this).val()}), "DEPARTMENT_ID", "DEPARTMENT_NAME", "----");
//        });

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
            pullEmployeeDetail(lastEmpId);
        }



        var populateList = function ($element, list, id, value, defaultMessage, selectedId) {
            $element.html('');
            $element.append($("<option></option>").val(null).text(defaultMessage));
            var concatArray = function (keyList, list, concatWith) {
                var temp = '';
                if (typeof concatWith === 'undefined') {
                    concatWith = ' ';
                }
                for (var i in keyList) {
                    var listValue = list[keyList[i]];
                    if (i == (keyList.length - 1)) {
                        temp = temp + ((listValue === null) ? '' : listValue);
                        continue;
                    }
                    temp = temp + ((listValue === null) ? '' : listValue) + concatWith;
                }

                return temp;
            };
            for (var i in list) {
                var text = null;
                if (typeof value === 'object') {
                    text = concatArray(value, list[i], ' ');
                } else {
                    text = list[i][value];
                }
                if (typeof selectedId !== 'undefined' && selectedId != null && selectedId == list[i][id]) {
                    $element.append($("<option selected='selected'></option>").val(list[i][id]).text(text));
                } else {
                    $element.append($("<option></option>").val(list[i][id]).text(text));
                }
            }
        };

        var search = function (list, where) {
            return list.filter(function (item) {
                for (var i in where) {
                    if (!(item[i] === where[i] || where[i] == -1)) {
                        return false;
                    }
                }
                return true;
            });
        };

    });
})(window.jQuery, window.app);


