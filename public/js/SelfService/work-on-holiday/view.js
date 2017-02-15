(function ($, app) {
    'use strict';
    $(document).ready(function () {
        const START_DATE = "START_DATE";
        const END_DATE = "END_DATE";

        $('select').select2();
        app.startEndDatePicker("fromDate", "toDate", function (fromDate, toDate) {
            if (fromDate <= toDate) {
                var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
                var diffDays = Math.abs((fromDate.getTime() - toDate.getTime()) / (oneDay));
                var newValue = diffDays + 1;
                //dateDiff = newValue;
                $("#duration").val(newValue);
            }
        });

    });
})(window.jQuery, window.app);

