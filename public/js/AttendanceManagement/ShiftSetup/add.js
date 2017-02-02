(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('#startTime').combodate({
            minuteStep: 1
        });
        $('#endTime').combodate({
            minuteStep: 1
        });
        $('#halfDayEndTime').combodate({
            minuteStep: 1
        });
        $('#halfTime').combodate({
            minuteStep: 1
        });
        app.startEndDatePicker('startDate', 'endDate');
        /* prevent past event post || commented for now as needs discussion*/
//        $('#startDate').datepicker("setStartDate", new Date());
//        $('#endDate').datepicker("setStartDate", new Date());
        /* end of  prevent past event post */

        var inputFieldId = "shiftEname";
        var formId = "shiftSetup-form";
        var tableName = "HR_SHIFTS";
        var columnName = "SHIFT_ENAME";
        var checkColumnName = "SHIFT_ID";
        var selfId = $("#shiftId").val();
        if (typeof selfId === "undefined") {
            selfId = 0;
        }
        window.app.checkUniqueConstraints(inputFieldId, formId, tableName, columnName, checkColumnName, selfId);
        window.app.checkUniqueConstraints("shiftLname", formId, tableName, "SHIFT_LNAME", checkColumnName, selfId);
        window.app.checkUniqueConstraints("shiftCode", formId, tableName, "SHIFT_CODE", checkColumnName, selfId);
    });
})(window.jQuery, window.app);
