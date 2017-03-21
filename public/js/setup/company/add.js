(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        
        var formId = "company-form";
        var inputFieldId = "form-companyName";    
        var tableName =  "HRIS_COMPANY";
        var columnName = "COMPANY_NAME";
        var checkColumnName = "COMPANY_ID";
        var selfId = $("#companyId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);
        window.app.checkUniqueConstraints("form-companyCode",formId,tableName,"COMPANY_CODE",checkColumnName,selfId);
    });
})(window.jQuery, window.app);
