(function ($,app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        
        var inputFieldId = "form-instituteName";
        var formId = "institute-form";
        var tableName =  "HR_INSTITUTE_MASTER_SETUP";
        var columnName = "INSTITUTE_NAME";
        var checkColumnName = "INSTITUTE_ID";
        var selfId = $("#instituteID").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);
        window.app.checkUniqueConstraints("form-instituteCode",formId,tableName,"INSTITUTE_CODE",checkColumnName,selfId);
    });
})(window.jQuery,window.app);


