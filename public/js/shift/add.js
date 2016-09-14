/**
 * Created by ukesh on 9/1/16.
 */
(function ($,app) {
    'use strict';
    $(document).ready(function () {
        var format = "d-M-yyyy";
        app.addDatePicker($("#startDate"),$("#endDate"));
        app.addTimePicker($('#startTime'),$('#endTime'), $('#halfDayEndTime'),$('#halfTime'));
    });

})(window.jQuery,window.app);