(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select#form-trainingType').select2();
//        app.startEndDatePicker("form-startDate", "form-endDate", function (fromDate, toDate) {  
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'form-startDate', 'nepaliEndDate1', 'form-endDate', function (fromDate, toDate) {
            if (fromDate <= toDate) {
                var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
                var diffDays = Math.abs((fromDate.getTime() - toDate.getTime()) / (oneDay));
                var newValue = diffDays + 1;
                //dateDiff = newValue;
                $("#form-duration").val(newValue);
            }
        });

        var employeeId = $('#employeeId').val();
        window.app.floatingProfile.setDataFromRemote(employeeId);

        var $trainingId = $("#form-trainingId");
        var $title = $("#form-title");
        var $startDate = $("#form-startDate");
        var $endDate = $("#form-endDate");
        var $duration = $("#form-duration");
        var $trainingType = $("#form-trainingType");
        var $nepaliStartDate = $("#nepaliStartDate1");
        var $nepaliEndDate = $("#nepaliEndDate1");

        const TRAINING_NAME = "TRAINING_NAME";
        const START_DATE = "START_DATE";
        const END_DATE = "END_DATE";
        const DURATION = "DURATION";
        const TRAINING_TYPE = "TRAINING_TYPE";

        console.log(document.trainingList);
        var trainingChange = function ($this) {
            if (typeof document.trainingList === 'undefined' || document.trainingList === null || document.trainingList.length === 0) {
                return;
            }
            var title = document.trainingList[$this.val()][TRAINING_NAME];
            var startDate = app.getSystemDate(document.trainingList[$this.val()][START_DATE]);
            var endDate = app.getSystemDate(document.trainingList[$this.val()][END_DATE]);
            var duration = document.trainingList[$this.val()][DURATION];
            var trainingType = document.trainingList[$this.val()][TRAINING_TYPE];
//            $title.val(title);

            $startDate.datepicker('setStartDate', startDate);
            $startDate.datepicker('setEndDate', endDate);
            $endDate.datepicker('setStartDate', startDate);
            $endDate.datepicker('setEndDate', endDate);


            $startDate.datepicker('setDate', startDate);
            $endDate.datepicker('setDate', endDate);
            $duration.val(duration);
            $trainingType.val(trainingType).change();
        };

        $trainingId.on('change', function () {
            trainingChange($(this));
        });


        var companyCheckChange = function (val) {
            var checked = val.is(":checked");
            if (checked !== true) {
                $title.show();
                $title.attr("required", true);
                $trainingId.select2('destroy');
                $trainingId.hide();
                $trainingType.val('CP').change();
                $duration.val("");
                $startDate.val("");
                $endDate.val("");
                $nepaliStartDate.val("");
                $nepaliEndDate.val("");

                $trainingType.attr('disabled', false);
                $startDate.attr('disabled', false);
                $endDate.attr('disabled', false);
                $nepaliStartDate.attr('disabled', false);
                $nepaliEndDate.attr('disabled', false);

                $startDate.datepicker('setStartDate', "");
                $startDate.datepicker('setEndDate', "");
                $endDate.datepicker('setStartDate', "");
                $endDate.datepicker('setEndDate', "");

                $startDate.datepicker('setDate', "");
                $endDate.datepicker('setDate', "");

            } else if (checked !== false) {
                $title.attr("required", false);
                $title.hide();
                $trainingId.select2();
                $trainingId.show();
                trainingChange($trainingId);
                $trainingType.attr('disabled', true);
                $startDate.attr('disabled', true);
                $endDate.attr('disabled', true);
            }
        }

        $("#companyList").on("change", function () {
            companyCheckChange($(this));
        });
        companyCheckChange($("#companyList"));
        app.setLoadingOnSubmit("trainingRequest-form");
        $('form').bind('submit', function () {
            $(this).find(':disabled').removeAttr('disabled');
        });
    });
})(window.jQuery, window.app);

