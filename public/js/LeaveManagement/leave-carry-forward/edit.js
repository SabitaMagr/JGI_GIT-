(function ($, app) {
//    'use strict';
    $(document).ready(function () {
        $('select').select2();

        $("#carryforward").focusout(function(){
            var ad = parseFloat($("#availableDays").val());
            var lfd = parseFloat($("#carryforward").val());
            var diff = ad - lfd;
            $("#encashment").attr('value', diff);
        });
        
        $('#request').click(function(){
                var carryforward = parseFloat($("#carryforward").val());
                var availableDays = parseFloat($("#availableDays").val());
                if (carryforward > availableDays ) {
                    alert("* Carry Forward can't be more than available days.");
                    return false;
                } else if (carryforward > 14) {
                    alert("* Applied days can't be greater than 14");
                    return false;
                } else {
                    $errorMsg.html("");
                    return true;
                }
        })

    });
})(window.jQuery, window.app);


