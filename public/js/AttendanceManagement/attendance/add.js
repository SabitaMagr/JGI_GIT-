/**
 * Created by punam on 9/28/16.
 */
(function ($,app) {
    'use strict';
    $(document).ready(function () {

        $('select').select2();
        $('#inTime').combodate({
            minuteStep: 1
        });
        $('#outTime').combodate({
            minuteStep: 1
        });

        app.addDatePicker(
            $("#attendanceDt")
        );
        var totalHour = function () {
            var inTime = $('#inTime').val();

            var tim_i = new Date("01/01/2007 " + $('#inTime').val());
            var tim_o = new Date("01/01/2007 " + $('#outTime').val());

            var diff1 = ( tim_i-tim_o) / 60000; //dividing by seconds and milliseconds
            var diff = Math.abs(diff1);
            var minutes = diff % 60;
            var hours = (diff - minutes) / 60;

            var total_tim = hours + '.' + minutes;
            $("#totalHour").val(total_tim);
        };
        $("#inTime").on("change",totalHour);
        $("#outTime").on("change",totalHour);
    });
})(window.jQuery,window.app);


