(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();

        var $employeeId = $("#employeeID");
        $employeeId.on("change", function () {
            window.app.floatingProfile.setDataFromRemote($(this).val());
        });


        $("#btnSubmit").click(function () {
            var password = $("#form-password").val();
            var confirmPassword = $("#form-repassword").val();
            var formGroup = $("#rePasswordDiv");
            if (password !== confirmPassword) {
                $("#form-repassword").focus();
                app.showMessage("Confirm Password doesn't match with Password.", 'error');
                return false;
            }
        });

        $('#form-password').hidePassword(true);
        $('#form-repassword').hidePassword(true);
        window.app.floatingProfile.setDataFromRemote($employeeId.val());
    });
})(window.jQuery, window.app);