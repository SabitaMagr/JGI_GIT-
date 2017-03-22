/**
 * Created by punam on 9/28/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        
        var inputFieldId = "form-roleName";
        var formId = "rolesetup-form";
        var tableName =  "HRIS_ROLES";
        var columnName = "ROLE_NAME";
        var checkColumnName = "ROLE_ID";
        var selfId = $("#roleId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);
    });
})(window.jQuery, window.app);
