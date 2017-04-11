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
                selected: false
            });
        }

        document.holidays = holidays;

//        app.startEndDatePicker('startDate', 'endDate');
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate');
        /* prevent past event post */
        $('#startDate').datepicker("setStartDate", new Date());
        $('#endDate').datepicker("setStartDate", new Date());
        /* end of  prevent past event post */


        var inputFieldId = "holidayEname";
        var formId = "holiday-Form";
        var tableName = "HRIS_HOLIDAY_MASTER_SETUP";
        var columnName = "HOLIDAY_ENAME";
        var checkColumnName = "HOLIDAY_ID";
        var selfId = $("#holidayId").val();
        if (typeof (selfId) == "undefined") {
            selfId = 0;
        }
        window.app.checkUniqueConstraints(inputFieldId, formId, tableName, columnName, checkColumnName, selfId);
        window.app.checkUniqueConstraints("holidayLname", formId, tableName, "HOLIDAY_LNAME", checkColumnName, selfId);
        window.app.checkUniqueConstraints("holidayCode", formId, tableName, "HOLIDAY_CODE", checkColumnName, selfId);
    });
})(window.jQuery, window.app);




