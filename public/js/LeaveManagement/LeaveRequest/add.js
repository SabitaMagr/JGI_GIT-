/**
 * Created by punam on 9/30/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();

        var dateDifference = function () {
            var id = (typeof this === "undefined") ? "selectedDate" : $(this).attr('id');
            var startDate="";
            var endDate="";
            switch (id) {
                case "startDate":
                     startDate = new Date($(this).val());
                     endDate = new Date($("#endDate").val());

                    $("#startDate").datepicker({
                        format: 'd-M-yyyy',
                        todayBtn:  1,
                        autoclose: true,
                    }).on('changeDate', function (selected) {
                        var minDate = new Date(selected.date.valueOf());
                        $('#endDate').datepicker('setStartDate', minDate);
                    });
                    break;

                case "endDate":
                     startDate = new Date($("#endDate").val());
                     endDate = new Date($(this).val());

                    $("#endDate").datepicker({
                        format: 'd-M-yyyy',
                        autoclose: true
                    })
                        .on('changeDate', function (selected) {
                            var minDate = new Date(selected.date.valueOf());
                            $('#startDate').datepicker('setEndDate', minDate);
                        });
                    break;

                case "selectedDate":
                    $('#startDate').datepicker({
                        format: 'd-M-yyyy',
                        autoclose: true,
                    });

                    $('#endDate').datepicker({
                        format: 'd-M-yyyy',
                        autoclose: true
                    });
                    break;
            }

            if (startDate < endDate) {
                var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
                var diffDays = Math.abs((startDate.getTime() - endDate.getTime()) / (oneDay));
                var newValue = diffDays + 1;
                var availableDays = $("#availableDays").val();

                if(newValue>availableDays){
                    $("#noOfDays").val(newValue);
                    $("#errorMsg").html("* Applied days can't be more than available days");
                    $("input[type=submit]").attr("disabled", "disabled");
                }else {
                    $("#noOfDays").val(newValue);
                    $("#errorMsg").html("");
                    $("input[type=submit]").removeAttr("disabled");
                }
            }
        };
        $("#endDate").on("change", dateDifference);
        $("#startDate").on("change", dateDifference);

        dateDifference();

    });
})(window.jQuery, window.app);



