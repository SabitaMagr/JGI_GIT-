/**
 * Created by punam on 9/28/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        
        var inputFieldId = "shiftEname";
        var formId = "shiftSetup-form";
        var tableName =  "HR_SHIFTS";
        var columnName = "SHIFT_ENAME";
        var checkColumnName = "SHIFT_ID";
        var selfId = $("#shiftId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);
        window.app.checkUniqueConstraints("shiftLname",formId,tableName,"SHIFT_LNAME",checkColumnName,selfId);
        window.app.checkUniqueConstraints("shiftCode",formId,tableName,"SHIFT_CODE",checkColumnName,selfId);
    });
})(window.jQuery, window.app);
