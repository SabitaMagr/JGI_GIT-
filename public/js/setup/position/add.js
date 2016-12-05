/**
 * Created by punam on 9/28/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        
        var inputFieldId = "form-positionName";
        var formId = "position-form";
        var tableName =  "HR_POSITIONS";
        var columnName = "POSITION_NAME";
        var checkColumnName = "POSITION_ID";
        var selfId = $("#positionId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);
    });
})(window.jQuery, window.app);
