/**
 * Created by punam on 9/28/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {

        $('select').select2();
        $('#startTime').combodate({
            minuteStep: 1
        });
        $('#endTime').combodate({
            minuteStep: 1
        });

        var $overtimeDate = $("#overtimeDate");
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

        var totalHour = function () {
            var tim_i = new Date("01/01/2007 " + $('#startTime').val());
            var tim_o = new Date("01/01/2007 " + $('#endTime').val());

            var diff1 = (tim_i - tim_o) / 60000; //dividing by seconds and milliseconds
            var diff = Math.abs(diff1);
            var minutes = diff % 60;
            var hours = (diff - minutes) / 60;

            var total_tim = hours + '.' + minutes;
            $("#totalHour").val(total_tim);
        };
        $("#startTime").on("change", totalHour);
        $("#endTime").on("change", totalHour);

        var $employeeId = $('#employeeId');
        app.floatingProfile.setDataFromRemote($employeeId.val());

        $employeeId.on("change", function (e) {
            app.floatingProfile.setDataFromRemote($(e.target).val());
        });
        app.setLoadingOnSubmit("overtimeRequest-form");
    });
})(window.jQuery, window.app);


