/**
 * Created by punam on 9/28/16.
 */
(function ($,app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        
        var inputFieldId = "form-branchName";
        var formId = "branch-form";
        var tableName =  "HRIS_BRANCHES";
        var columnName = "BRANCH_NAME";
        var checkColumnName = "BRANCH_ID";
        var selfId = $("#branchId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId, function () {
            App.blockUI({target: "#hris-page-content"});
        });      
        window.app.checkUniqueConstraints("form-branchCode",formId,tableName,"BRANCH_CODE",checkColumnName,selfId);
    });
})(window.jQuery,window.app);


