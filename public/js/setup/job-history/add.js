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
        var from = null;

        $fromServiceTypeId.on("change", function () {
            $toServiceTypeId.val($(this).val()).trigger("change");
        });
        $fromCompanyId.on("change", function () {
            $toCompanyId.val($(this).val()).trigger("change");
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

//            $serviceEventTypeId.prop("disabled", flag);

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

        var pullEmployeeDetail = function (employeeId) {
            if (typeof prevAndNextHistory['prev'] !== 'undefined') {
                return;
            }
            app.pullDataById(document.wsPullEmployeeDetailWithOptions, {
                employeeId: employeeId
            }).then(function (success) {
                var employeeDetail = success.data.employeeDetail;

                companyList = success.data.companyList;
                branchList = success.data.branchList;
                departmentList = success.data.departmentList;
                designationList = success.data.designationList;
                positionList = success.data.positionList;


                populateList($fromCompanyId, companyList, "COMPANY_ID", "COMPANY_NAME", "----");
                populateList($toCompanyId, companyList, "COMPANY_ID", "COMPANY_NAME", "----");

                populateList($fromBranchId, branchList, "BRANCH_ID", "BRANCH_NAME", "----");
                populateList($toBranchId, branchList, "BRANCH_ID", "BRANCH_NAME", "----");

                populateList($fromDepartmentId, departmentList, "DEPARTMENT_ID", "DEPARTMENT_NAME", "----");
                populateList($toDepartmentId, departmentList, "DEPARTMENT_ID", "DEPARTMENT_NAME", "----");

                populateList($fromDesignationId, designationList, "DESIGNATION_ID", "DESIGNATION_TITLE", "----");
                populateList($toDesignationId, designationList, "DESIGNATION_ID", "DESIGNATION_TITLE", "----");

                populateList($fromPositionId, positionList, "POSITION_ID", "POSITION_NAME", "----");
                populateList($toPositionId, positionList, "POSITION_ID", "POSITION_NAME", "----");


                from = employeeDetail;
                serviceEventChangeAction();
//                updateView(from);
//                checkAppointmentOption(employeeDetail);
//                toggleEmployeeInfo(false);

                if (typeof employeeDetail['LAST_EVENT_DATE'] !== 'undefined') {
                    $('#startDate').datepicker('setStartDate', nepaliDatePickerExt.getDate(employeeDetail['LAST_EVENT_DATE']));
                }

            }, function (failure) {
                console.log("pullEmployeeById failure", failure);
            });
        };


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
        var serviceEventChangeAction = function () {
            var selectedServiceEventType = $serviceEventTypeId.val();
            if (selectedServiceEventType == 2) {
                toggleEmployeeInfo(true);
            } else {
                toggleEmployeeInfo(false);
            }
            if (from != null) {
                updateView(from);
            }
        }
        $serviceEventTypeId.on('change', function () {
            serviceEventChangeAction();
        });
        serviceEventChangeAction();




        if (typeof prevAndNextHistory['prev'] !== 'undefined') {
            $employeeId.prop("disabled", true);

            var prevHistory = prevAndNextHistory['prev'];
            from = {
                SERVICE_TYPE_ID: prevHistory['TO_SERVICE_TYPE_ID'],
                COMPANY_ID: prevHistory['TO_COMPANY_ID'],
                BRANCH_ID: prevHistory['TO_BRANCH_ID'],
                DEPARTMENT_ID: prevHistory['TO_DEPARTMENT_ID'],
                DESIGNATION_ID: prevHistory['TO_DESIGNATION_ID'],
                POSITION_ID: prevHistory['TO_POSITION_ID']
            };
            updateView(from);
            $('#startDate').datepicker('setStartDate', nepaliDatePickerExt.getDate(prevHistory['START_DATE']));

            var nextHistory = prevAndNextHistory['next'];
            if (typeof nextHistory !== "undefined") {
                $('#startDate').datepicker('setEndDate', nepaliDatePickerExt.getDate(nextHistory['START_DATE']));
            }


        }


        $employeeId.on("change", function () {
            var $this = $(this);
            app.floatingProfile.setDataFromRemote($this.val());
            pullEmployeeDetail($this.val())
        });

        app.floatingProfile.setDataFromRemote($employeeId.val());
        pullEmployeeDetail($employeeId.val());





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


