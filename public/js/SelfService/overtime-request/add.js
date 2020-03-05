/**
 * Created by punam on 9/28/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var $overtimeDate = $("#overtimeDate");
        var $employeeId = $('#employeeId');

        if (!($overtimeDate.is('[readonly]'))) {
            app.datePickerWithNepali("overtimeDate", "nepaliDate");
            app.getServerDate().then(function (response) {
                $overtimeDate.datepicker('setEndDate', app.getSystemDate(response.data.serverDate));
            }, function (error) {
                console.log("error=>getServerDate", error);
            });
        } else {
            app.datePickerWithNepali("overtimeDate", "nepaliDate");
        }

        var $nepaliDate = $("#nepaliDate");
        var $englishDate = $("#overtimeDate");
        $("#nepaliDate").nepaliDatePicker({
            onChange: function(){
                var temp = nepaliDatePickerExt.fromNepaliToEnglish($nepaliDate.val());
                var englishStartDate = $englishDate.datepicker('getStartDate');
                var englishEndDate = $englishDate.datepicker('getEndDate');
                $englishDate.val(temp);
                let employeeId = $employeeId.val();
                let date = $overtimeDate.val();
                if(date != null || date != ''){
                    validateAttendance(employeeId, date);
                }
            }
        });

        function validateAttendance(employeeId, date){
            app.serverRequest(document.showAttendanceDetail, {
                employeeId: employeeId,
                date: date
            }).then(function (response) {
                const div = document.getElementById('attdDetail');
                div.innerHTML = `
                        <div class="col-sm-3">
                        <label><strong>In Time:</strong></label>
                        <div>`+response.data[0]['IN_TIME']+`</div></div>
                        <div class="col-sm-3">
                        <label><strong>Out Time:</strong></label>
                        <div>`+response.data[0]['OUT_TIME']+`</div></div>
                        <div class="col-sm-3">
                        <label><strong>Working Minutes:</strong></label>
                        <div>`+response.data[0]['TOTAL_HOUR']+`</div></div>
                        <div class="col-sm-3">
                        <label><strong>OT Minutes:</strong></label>
                        <div>`+response.data[0]['OT_MINUTES']+`</div></div>` ;
            });

            app.serverRequest(document.validateAttendanceLink, {
                employeeId: employeeId,
                date: date
            }).then(function(response){
                if(response.validation === 'F' || response.validation === null){
                    app.showMessage("Overtime not more than 2 hours", "error");
                    $("#submit").attr('disabled', 'disabled'); 
                }
                else{ $("#submit").removeAttr('disabled'); }
            });
        }

        $('#employeeId, #overtimeDate').on('change input select', function(){
            let employeeId = $employeeId.val();
            let date = $overtimeDate.val();
            if(date != null || date != ''){
                validateAttendance(employeeId, date);
            }
        });

        app.floatingProfile.setDataFromRemote($employeeId.val());

        $employeeId.on("change", function (e) {
            app.floatingProfile.setDataFromRemote($(e.target).val());
        });
        app.setLoadingOnSubmit("overtimeRequest-form", function ($form) {
            var formData = new FormData($form[0]);
            if (formData.getAll('startTime[]').length == 0) {
                app.showMessage("Minimum One Start time and End time is required.", 'error');
                return false;
            } else {
                return true;
            }
        });
    });
})(window.jQuery, window.app);


