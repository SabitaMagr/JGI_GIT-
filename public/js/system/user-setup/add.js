/**
 * Created by root on 10/18/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {

        var employeeId = $("#employeeID").val();
        window.app.floatingProfile.setDataFromRemote(employeeId);

        $("#employeeID").on("change", function () {
            var employeeId = $(this).val();
            window.app.floatingProfile.setDataFromRemote(employeeId);
        });

        $('select').select2();

        $("#btnSubmit").click(function () {            
            var password = $("#form-password").val();
            var confirmPassword = $("#form-repassword").val();
            var formGroup = $("#form-repassword").parent(".form-group");
            if (password != confirmPassword) {
                 $("#form-repassword").focus();
                window.app.displayErrorMessage(formGroup, 1, "* Passwords do not match!!!");
                return false;
            }else{
              window.app.displayErrorMessage(formGroup, 0, "Passwords do not match.");
              return true;  
            }
        });
    });
})(window.jQuery, window.app);
