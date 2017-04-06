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
        
        var inputFieldId = "form-userName";
        var formId = "usersetup-form";
        var tableName =  "HRIS_USERS";
        var columnName = "USER_NAME";
        var checkColumnName = "USER_ID";
        var selfId = $("#userId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);       
       
    });
})(window.jQuery, window.app);
