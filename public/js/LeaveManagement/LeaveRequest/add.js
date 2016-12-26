/**
 * Created by punam on 9/30/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {

        var employeeId = $('#employeeId').val();
        window.app.floatingProfile.setDataFromRemote(employeeId);

        $('select').select2();

        var dateDiff = "";
        $("#remarks").hide();

        var checkHalfDay = function () {
            var availableDays1 = parseInt($("#availableDays").val());
            var halfDay = $("input[name='halfDay']:checked").val();
            var startDate = $("#startDate").val();
            var endDate = $("#endDate").val();
            var noOfDays1 = parseInt($("#noOfDays").val());
            if (halfDay == 'F' || halfDay == "S") {
                $('#noOfDays').attr('readonly', true);
                if (startDate != endDate) {
                    $("#errorMsgDate").html("* Start date and end date must be same in the case of half day leave");
                    $("#request").attr("disabled", "disabled");
                } else if (startDate == endDate) {
                    $("#errorMsgDate").html("");
                    $("#errorMsg").html("");
                    $("#request").removeAttr("disabled");
                }
            } else if (halfDay == 'N') {
                $("#errorMsgDate").html("");
                if (noOfDays1 <= availableDays1) {
                    $("#errorMsg").html("");
                    $("#request").removeAttr("disabled");
                }
                if (startDate == endDate) {
                    $('#noOfDays').attr('readonly', true);
                } else {
                    $('#noOfDays').attr('readonly', false);
                }
            }
        }

        var dateDifference = function () {
            var id = (typeof this === "undefined") ? "selectedDate" : $(this).attr('id');
            var startDate = "";
            var endDate = "";

            switch (id) {
                case "startDate":
                    startDate = ($(this).val() === "") ? "" : new Date($(this).val());
                    endDate = ($("#endDate").val() === "") ? "" : new Date($("#endDate").val());

                    $("#startDate").datepicker({
                        format: 'd-M-yyyy',
                        todayBtn: 1,
                        autoclose: true,
                    }).on('changeDate', function (selected) {
                        var minDate = new Date(selected.date.valueOf());
                        $('#endDate').datepicker('setStartDate', minDate);
                    });
                    break;

                case "endDate":
                    startDate = ($("#startDate").val() === "") ? "" : new Date($("#startDate").val());
                    endDate = ($(this).val() === "") ? "" : new Date($(this).val());

                    $("#endDate").datepicker({
                        format: 'd-M-yyyy',
                        autoclose: true
                    }).on('changeDate', function (selected) {
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

            if ((startDate != "" && endDate != "") && (startDate <= endDate)) {
                var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
                var diffDays = Math.abs((startDate.getTime() - endDate.getTime()) / (oneDay));
                var newValue = diffDays + 1;
                var availableDays = parseInt($("#availableDays").val());
                dateDiff = newValue;
                $("#noOfDays").val(newValue);

                var halfDay1 = $("input[name='halfDay']:checked");
                var halfDay2 = ""
                if (typeof (halfDay1.val()) == "undefined") {
                    halfDay2 = "N";
                } else {
                    halfDay2 = halfDay1.val();
                }
                checkHalfDay();
                if (newValue > availableDays) {
                    $("#errorMsg").html("* Applied days can't be more than available days");
                    $("#request").attr("disabled", "disabled");
                } else if ((newValue <= availableDays) && halfDay2 == 'N') {
                    $("#errorMsg").html("");
                    $("#request").removeAttr("disabled");
                } else if ((newValue == availableDays) && (halfDay2 == 'S' || halfDay2 == 'F')) {
                    $("#errorMsg").html("");
                    $("#request").removeAttr("disabled");
                }

                if ((newValue <= availableDays) && (halfDay2 == 'S' || halfDay2 == 'F')) {
                    $("#errorMsg").html("")
                }
            }
        };
        $("#endDate").on("change", dateDifference);
        $("#startDate").on("change", dateDifference);

        dateDifference();

        $("#noOfDays").on("keyup", function () {
            var availableDays = parseInt($("#availableDays").val());
            var noOfDays = parseInt($(this).val());
            if (noOfDays > availableDays) {
                $("#errorMsg").html("* Applied days can't be more than available days");
                $("#request").attr("disabled", "disabled");
            } else {
                $("#errorMsg").html("");
                $("#request").removeAttr("disabled");
            }
            if (noOfDays != dateDiff) {
                $("#form-remarks").attr('required', 'required');
                $("#remarks").slideDown();
            } else {
                $("#form-remarks").removeAttr('required');
                $("#remarks").slideUp();
            }
        });

        $(".radioButton").each(function () {
            $(this).on("click", checkHalfDay);
        });

    });
})(window.jQuery, window.app);



