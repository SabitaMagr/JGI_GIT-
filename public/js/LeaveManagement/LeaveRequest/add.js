(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();

        var $employee = $('#employeeId');
        var $leave = $('#leaveId');
        var $halfDay = $("#halfDay");
        var $availableDays = $('#availableDays');
        var $noOfDays = $('#noOfDays');
        var $request = $("#request");
        var $errorMsg = $("#errorMsg");

        var dateDiff = "";

        app.floatingProfile.setDataFromRemote($employee.val());

        var leaveList = [];
        var availableDays = null;


        var calculateAvailableDays = function (startDateStr, endDateStr, employeeId) {
            App.blockUI({target: "#hris-page-content", message: "Calculating Days"});
            app.pullDataById(document.wsFetchAvailableDays, {startDate: startDateStr, endDate: endDateStr, employeeId: employeeId}).then(function (response) {
                App.unblockUI("#hris-page-content");
                if (!response.success) {
                    app.showMessage(response.error, 'error');
                    return;
                }

                var dateDiff = response.data['AVAILABLE_DAYS'];
                var availableDays = parseInt($availableDays.val());

                $noOfDays.val(dateDiff);

                if (dateDiff > availableDays) {
                    $errorMsg.html("* Applied days can't be more than available days");
                    $request.prop("disabled", true);
                } else {
                    $errorMsg.html("");
                    $request.prop("disabled", false);
                }

            }, function (error) {
                App.unblockUI("#hris-page-content");
                app.showMessage(error, 'error');
            });
        };
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate', function (startDate, endDate, startDateStr, endDateStr) {
            var employeeId = $employee.val();
            if (typeof employeeId === 'undefined' || employeeId === null || employeeId === '' || employeeId === -1) {
                return;
            }
            calculateAvailableDays(startDateStr, endDateStr, employeeId);
        });


        app.setLoadingOnSubmit("leaveApply");



        var toggleSubstituteEmployeeReq = function ($flag) {
            if ($flag) {
                $('#substituteEmployeeCol').find('span[class="required"]').show();
                $('#leaveSubstitute').find('option[value=""]').prop('disabled', true);
                $('#leaveSubstitute').prop('required', true);
            } else {
                $('#substituteEmployeeCol').find('span[class="required"]').hide();
                $('#leaveSubstitute').find('option[value=""]').prop('disabled', false);
                $('#leaveSubstitute').prop('required', false);
            }
        };
        var toggleGracePeriod = function ($flag) {
            if ($flag) {
                $('#gracePeriodCol').show();
                $('#gracePeriod').prop('disabled', false);
            } else {
                $('#gracePeriodCol').hide();
                $('#gracePeriod').prop('disabled', true);
            }
        };
        var toggleHalfDay = function ($flag) {
            if ($flag) {
                $('#halfDayCol').show();
                $('#halfDay').prop('disabled', false);
            } else {
                $('#halfDayCol').hide();
                $('#halfDay').prop('disabled', true);
            }
        };

        toggleHalfDay(false);
        toggleGracePeriod(false);
        toggleSubstituteEmployeeReq(false);



        var leaveChange = function (obj) {
            var $this = $(obj);
            if ($this.val() === null || $this.val() === '' || $this.val() === '-1') {
                return;
            }
            App.blockUI({target: "#hris-page-content", message: "Calculating Leave Days"});
            app.pullDataById(document.wsPullLeaveDetail, {
                'leaveId': $this.val(),
                'employeeId': $employee.val()
            }).then(function (success) {
                App.unblockUI("#hris-page-content");
                var leaveDetail = success.data;
                availableDays = parseInt(leaveDetail.BALANCE);
                $availableDays.val(availableDays);

                var noOfDays = parseInt($noOfDays.val());

                if ((availableDays != "" && noOfDays != "") && noOfDays > availableDays) {
                    $("#errorMsg").html("* Applied days can't be more than available days");
                    $("#request").attr("disabled", "disabled");
                } else if ((availableDays != "" && noOfDays != "") && (noOfDays <= availableDays)) {
                    $("#errorMsg").html("");
                    $("#request").removeAttr("disabled");
                }

                toggleGracePeriod(leaveDetail.ALLOW_GRACE_LEAVE === "Y");
                toggleHalfDay(leaveDetail.ALLOW_HALFDAY === "Y");
                toggleSubstituteEmployeeReq(leaveDetail.IS_SUBSTITUTE_MANDATORY === 'Y');
            }, function (failure) {
                App.unblockUI("#hris-page-content");
                console.log(failure);
            });
        };

        $leave.on('change', function () {
            leaveChange(this);
        });



        var employeeChange = function (obj) {
            var $this = $(obj);
            app.floatingProfile.setDataFromRemote($this.val());
            App.blockUI({target: "#hris-page-content", message: "Fetching Employee Leaves"});
            app.pullDataById(document.wsPullLeaveDetailWidEmployeeId, {
                'employeeId': $this.val()
            }).then(function (success) {
                App.unblockUI("#hris-page-content");
                leaveList = success.data;
                app.populateSelect($leave, leaveList, 'id', 'name', 'Select a Leave', null, null, false);

                var $startDate = $('#startDate'), $endDate = $('#endDate');
                if ($startDate.val() != '' && $endDate.val() != '') {
                    calculateAvailableDays($startDate.val(), $endDate.val(), $this.val());
                }

            }, function (failure) {
                App.unblockUI("#hris-page-content");
                console.log(failure);
            });




        };

        $employee.on('change', function () {
            employeeChange(this);
        });
    });
})(window.jQuery, window.app);


