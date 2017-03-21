/**
 * Created by punam on 9/28/16.
 */
(function ($,app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        
        var inputFieldId = "form-departmentName";
        var formId = "department-form";
        var tableName =  "HRIS_DEPARTMENTS";
        var columnName = "DEPARTMENT_NAME";
        var checkColumnName = "DEPARTMENT_ID";
        var selfId = $("#departmentId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);
        window.app.checkUniqueConstraints("form-departmentCode",formId,tableName,"DEPARTMENT_CODE",checkColumnName,selfId);
    });
})(window.jQuery,window.app);
