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

        var format = function (input) {
            var str = "" + input;
            var pad = "00";
            return pad.substring(0, pad.length - str.length) + str;
        };

        var onChangeTime = function () {
            try {
                var startValue = $startTime.combodate('getValue', 'YYYY-MM-DDTHH:mm:ss.sssZ');
                var endValue = $endTime.combodate('getValue', 'YYYY-MM-DDTHH:mm:ss.sssZ');

                if (startValue == '' || endValue == '') {
                    throw {message: 'Start Time or End Time not set.', type: 'internal'};
                }
                var startDate = new Date(startValue);
                var endDate = new Date(endValue);

                var timeDiff = endDate.getTime() - startDate.getTime();
                if (timeDiff <= 0) {
                    throw {message: 'End Time should be greater than Start Time.', type: 'external'};
                }

                var diffHr = Math.floor(timeDiff / (1000 * 3600));
                var diffMin = Math.floor((timeDiff % (1000 * 3600)) / (1000 * 60));
                $totalWorkingHr.combodate('setValue', format(diffHr) + ":" + format(diffMin));

            } catch (e) {
                console.log('exceptions', e);
                if (e.type == 'external') {
                    app.errorMessage(e.message);
                }
            }

        };

        $startTime.on('change', function () {
            onChangeTime();
        });

        $endTime.on('change', function () {
            onChangeTime();
        });

//        app.startEndDatePicker('startDate', 'endDate');
app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate');
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
        window.app.checkUniqueConstraints(inputFieldId, formId, tableName, columnName, checkColumnName, selfId, function () {
            App.blockUI({target: "#hris-page-content"});
        });
        window.app.checkUniqueConstraints("shiftLname", formId, tableName, "SHIFT_LNAME", checkColumnName, selfId);
        window.app.checkUniqueConstraints("shiftCode", formId, tableName, "SHIFT_CODE", checkColumnName, selfId);
    });
})(window.jQuery, window.app);
