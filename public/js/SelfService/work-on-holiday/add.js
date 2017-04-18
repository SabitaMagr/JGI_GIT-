(function ($, app) {
    'use strict';
    $(document).ready(function () {
        const START_DATE = "START_DATE";
        const END_DATE = "END_DATE";

        $('select').select2();
//        app.startEndDatePicker("fromDate", "toDate", function (fromDate, toDate) {
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'fromDate', 'nepaliEndDate1', 'toDate', function (fromDate, toDate) {
            if (fromDate <= toDate) {
                var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
                var diffDays = Math.abs((fromDate.getTime() - toDate.getTime()) / (oneDay));
                var newValue = diffDays + 1;
                //dateDiff = newValue;
                $("#duration").val(newValue);
            }
        });
        var $holidayId = $('#form-holidayId');
        var $fromDate = $("#fromDate");
        var $toDate = $("#toDate");
        var holidayChange = function ($this) {
            var startDate = app.getSystemDate(document.holidayList[$this.val()][START_DATE]);
            var endDate = app.getSystemDate(document.holidayList[$this.val()][END_DATE]);
            $fromDate.datepicker('setStartDate', startDate);
            $fromDate.datepicker('setEndDate', endDate);
            $toDate.datepicker('setStartDate', startDate);
            $toDate.datepicker('setEndDate', endDate);

            $fromDate.datepicker('setDate', startDate);
            $toDate.datepicker('setDate', endDate);

        };

        $holidayId.on('change', function () {
            holidayChange($(this));
        });

        holidayChange($holidayId);
        app.setLoadingOnSubmit("workOnHoliday-form");
    });
})(window.jQuery, window.app);

