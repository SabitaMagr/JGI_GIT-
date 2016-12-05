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

        var inputFieldId = "holidayEname";
        var formId = "holiday-Form";
        var tableName =  "HR_HOLIDAY_MASTER_SETUP";
        var columnName = "HOLIDAY_ENAME";
        var checkColumnName = "HOLIDAY_ID";
        var selfId = $("#holidayId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);
        window.app.checkUniqueConstraints("holidayLname",formId,tableName,"HOLIDAY_LNAME",checkColumnName,selfId);
        window.app.checkUniqueConstraints("holidayCode",formId,tableName,"HOLIDAY_CODE",checkColumnName,selfId);
    });
})(window.jQuery, window.app);




