/**
 * Created by punam on 9/28/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        
        var inputFieldId = "leaveEname";
        var formId = "leaveSetup-form";
        var tableName =  "HR_LEAVE_MASTER_SETUP";
        var columnName = "LEAVE_ENAME";
        var checkColumnName = "LEAVE_ID";
        var selfId = $("#leaveId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);
        window.app.checkUniqueConstraints("leaveCode",formId,tableName,"LEAVE_CODE",checkColumnName,selfId);
        window.app.checkUniqueConstraints("leaveLname",formId,tableName,"LEAVE_LNAME",checkColumnName,selfId);
    });
})(window.jQuery, window.app);
