(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        var $fromDate = $('#fromDate');
        var $toDate = $('#toDate');
        var $leaveReportTable = $('#leaveReportTable');
        var $search = $('#search');

        var $dateList = $('#dateList');

        var selectedDates = [];

        var $monthsNepali = $('#monthsNepali');

        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);


        $search.on('click', function () {
            console.log(selectedDates);
            
            app.pullDataById(document.getHireFireReportWS, data).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($leaveReportTable, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
            
        });

        $monthsNepali.on('change', function () {
            var tempSelectedDate = [];
            var option = $('option:selected', this);
            option.each(function () {
                var $this = $(this);
                if ($this.length) {
                    var selText = $this.text();
                    var selVal = $this.val();
                    var splitSeltedValue = selVal.split(',');
                    var tempArr = [];
                    tempArr['name'] = selText;
                    tempArr['fromDate'] = splitSeltedValue[0];
                    tempArr['toDate'] = splitSeltedValue[1];
                    tempSelectedDate.push(tempArr);
                }
            });
            $dateList.empty();
            selectedDates = tempSelectedDate;

            if (tempSelectedDate.length !== 0) {
                $dateList.append("<tr><th>Name</th><th>FromDate</th><th>ToDate</th><tr>");
            }

            $.each(tempSelectedDate, function (index, value) {
                $dateList.append("<tr><th>"+value.name+"</th><th>"+value.fromDate+"</th><th>"+value.toDate+"</th><tr>");
            });



        })


        $("#reset").on("click", function () {
            if (typeof document.ids !== "undefined") {
                $.each(document.ids, function (key, value) {
                    $("#" + key).val(value).change();
                });
            }
        });


    });
})(window.jQuery, window.app);

