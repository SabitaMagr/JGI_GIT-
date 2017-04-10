(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePicker("form-startDate", "form-endDate", function (fromDate, toDate) {
            if (fromDate <= toDate) {
                var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
                var diffDays = Math.abs((fromDate.getTime() - toDate.getTime()) / (oneDay));
                var newValue = diffDays + 1;
                //dateDiff = newValue;
                $("#form-duration").val(newValue);
            }
        });
        
        var $trainingId = $("#form-trainingId");
        var $title = $("#form-title");
        var $startDate = $("#form-startDate");
        var $endDate = $("#form-endDate");
        var $duration = $("#form-duration");
        var $trainingType = $("#form-trainingType");
        
        const TRAINING_NAME = "TRAINING_NAME";
        const START_DATE = "START_DATE";
        const END_DATE = "END_DATE";
        const DURATION = "DURATION";
        const TRAINING_TYPE = "TRAINING_TYPE";
        
        console.log(document.trainingList);
        var trainingChange = function ($this) {
            var title = document.trainingList[$this.val()][TRAINING_NAME];
            var startDate = app.getSystemDate(document.trainingList[$this.val()][START_DATE]);
            var endDate = app.getSystemDate(document.trainingList[$this.val()][END_DATE]);
            var duration = document.trainingList[$this.val()][DURATION];
            var trainingType = document.trainingList[$this.val()][TRAINING_TYPE];
            $title.val(title);
            
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

        trainingChange($trainingId);
    });
})(window.jQuery, window.app);

