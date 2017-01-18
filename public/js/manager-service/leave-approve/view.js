(function ($) {
    'use strict';
    $(document).ready(function (e) {
        $("#approve").on("click", function () {
            var id = $(this);
            var parentId = id.parent(".form-group");
            console.log("hellow");
            var availableBalance = $("#availableDays").val();
            var noOfDays = $("#noOfDays").val();
            if (noOfDays > availableBalance) {
                var errorMsgSpan = $('<span />', {
                    "class": 'errorMsgNoLeft',
                    text: "There is not enough available days...!!!"
                });
                parentId.append(errorMsgSpan);
                $("#availableDaysText").css("font-weight","bold");
                this.disabled = true;
            } else {
                this.disabled = false;
            }
            console.log(availableBalance + noOfDays);

            //this.value='Sending…';
            //e.preventDefault();
        });
    });
})(window.jQuery, window.app);
