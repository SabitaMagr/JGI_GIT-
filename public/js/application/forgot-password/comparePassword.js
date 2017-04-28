(function ($, app) {
    'use strict';
    $("#rePassword").on("blur", function () {
        var rePassword = $(this).val();
        var password = $("#password").val();
        if(rePassword!==password){
            $(".errorMsg").html("* Password doesn't match!!!.");
        }else{
            $(".errorMsg").html("");
        }
    });
    $('#usernameForm').submit(function (e) {
        var err = [];
        $(".errorMsg").each(function () {
            var erroMsg = $.trim($(this).html());
            if (erroMsg !== "") {
                err.push("error");
            }
        });
        if (err.length > 0)
        {
            return false;
        }
    });
})(window.jQuery, window.app);

