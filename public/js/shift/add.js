/**
 * Created by ukesh on 9/1/16.
 */
(function ($) {
    'use strict';
    $(document).ready(function () {
        var format = "d-M-yyyy";

        $("#startDate").datepicker({
            format: format,
            autoclose: true
        });
        $("#endDate").datepicker({
            format: format,
            autoclose: true
        });
        $('#startTime').timepicker();
        $('#endTime').timepicker();
        $('#halfDayEndTime').timepicker();
        $('#halfTime').timepicker();

    });

})(window.jQuery);