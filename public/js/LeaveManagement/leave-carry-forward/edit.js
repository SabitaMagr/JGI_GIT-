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
                    app.showMessage("Carry Forward can't be more than available days. ",'info','error');
                    return false;
                } else if (carryforward > 14) {
                    app.showMessage("Applied days can't be greater than 14",'info','error');
                    return false;
                } else {
                    $errorMsg.html("");
                    return true;
                }
        })

    });
})(window.jQuery, window.app);


