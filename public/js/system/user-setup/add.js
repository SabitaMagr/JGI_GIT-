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
            var formGroup = $("#rePasswordDiv");
            if (password != confirmPassword) {
                console.log('password mismatch');
                 $("#form-repassword").focus();
                window.app.displayErrorMessage(formGroup, 1, "* Passwords do not match!!!");
            }else{
                console.log('password match');
              window.app.displayErrorMessage(formGroup, 0, "Passwords do not match.");
            $('#usersetup-form')[0].submit();
            }
        });
        
        var inputFieldId = "form-userName";
        var formId = "usersetup-form";
        var tableName =  "HRIS_USERS";
        var columnName = "USER_NAME";
        var checkColumnName = "USER_ID";
        var selfId = $("#userId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId, function () {
            App.blockUI({target: "#hris-page-content"});
        });
        
        $('#form-password').hidePassword(true);
        $('#form-repassword').hidePassword(true);
    });
})(window.jQuery, window.app);