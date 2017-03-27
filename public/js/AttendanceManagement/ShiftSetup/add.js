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
        $('#lateIn').combodate({
            minuteStep: 1
        });
        $('#earlyOut').combodate({
            minuteStep: 1
        });
        $('#actualWorkingHr').combodate({
            minuteStep: 1
        });
        $('#totalWorkingHr').combodate({
            minuteStep: 1
        });

        var $startTime = $('#startTime');
        var $endTime = $('#endTime');
        var $totalWorkingHr = $('#totalWorkingHr');

        var onChangeTime = function () {
            var st = $startTime.combodate('getValue', 'h,m');
            var et = $endTime.combodate('getValue', 'h,m');

            var stA = st.split(',');
            var etA = et.split(',');

            var diff1 = parseInt(st[0]) - parseInt(et[0]);
            var diff2 = parseInt(st[1]) - parseInt(et[1]);
            console.log(diff1 + ":" + diff2);
//            $totalWorkingHr.combodate('setValue', diff1 + ":" + diff2);
        };

        $startTime.on('change', function () {
            onChangeTime();
        });

        $endTime.on('change', function () {
            onChangeTime();
        });

        app.startEndDatePicker('startDate', 'endDate');
        /* prevent past event post || commented for now as needs discussion*/
//        $('#startDate').datepicker("setStartDate", new Date());
//        $('#endDate').datepicker("setStartDate", new Date());
        /* end of  prevent past event post */

        var inputFieldId = "shiftEname";
        var formId = "shiftSetup-form";
        var tableName = "HRIS_SHIFTS";
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
