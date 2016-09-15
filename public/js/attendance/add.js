(function ($,app) {
    'use strict';
    $(document).ready(function () {
        var format = "d-M-yyyy";
        app.addDatePicker($("#attendanceDt"));
        app.addTimePicker($('#inTime'),$('#outTime'));
    });

})(window.jQuery,window.app);