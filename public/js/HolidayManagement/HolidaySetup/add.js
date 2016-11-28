/**
 * Created by ukesh on 9/12/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var holidays = [];
        var holidayOptionElements = document.querySelectorAll('#holidayId option');
        console.log(holidayOptionElements.length);
        for (var index = 0; index < holidayOptionElements.length; index++) {
            holidays.push({
                id: holidayOptionElements[index].value,
                text: holidayOptionElements[index].text,
                selected:false
            });
        }

        document.holidays = holidays;

        app.addDatePicker(
            $("#startDate"),
            $("#endDate")
        );

    });
})(window.jQuery, window.app);




