(function ($, app) {
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate');

        var $employeeId = $("#employeeId");
        var $serviceEventTypeId = $("#serviceEventTypeId");

        var $toServiceTypeId = $('#toServiceTypeId');
        var $toCompanyId = $('#toCompanyId');
        var $toBranchId = $('#toBranchId');
        var $toDepartmentId = $('#toDepartmentId');
        var $toDesignationId = $('#toDesignationId');
        var $toPositionId = $('#toPositionId');



        var companyList = [];
        var branchList = [];
        var departmentList = [];
        var designationList = [];
        var positionList = [];

        var from = null;

        var disableEmployee = function () {
            $employeeId.prop("disabled", true);
        };

        var pullEmployeeDetail = function (employeeId) {
            app.pullDataById(document.wsPullEmployeeDetailWithOptions, {
                employeeId: employeeId
            }).then(function (success) {
                var employeeDetail = success.data.employeeDetail;

                companyList = success.data.companyList;
                branchList = success.data.branchList;
                departmentList = success.data.departmentList;
                designationList = success.data.designationList;
                positionList = success.data.positionList;


                populateList($toCompanyId, companyList, "COMPANY_ID", "COMPANY_NAME", "----");
                populateList($toBranchId, branchList, "BRANCH_ID", "BRANCH_NAME", "----");
                populateList($toDepartmentId, departmentList, "DEPARTMENT_ID", "DEPARTMENT_NAME", "----");
                populateList($toDesignationId, designationList, "DESIGNATION_ID", "DESIGNATION_TITLE", "----");
                populateList($toPositionId, positionList, "POSITION_ID", "POSITION_NAME", "----");


                from = employeeDetail;
                if (typeof employeeDetail['LAST_EVENT_DATE'] !== 'undefined') {
                    $('#startDate').datepicker('setStartDate', nepaliDatePickerExt.getDate(employeeDetail['LAST_EVENT_DATE']));
                }

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


