(function ($,app) {
    'use strict';
    $(document).ready(function () {
        
        var inputFieldId = "assetGroupCode";
        var formId = "assetGroup-form";
        var tableName =  "HR_ASSET_GROUP";
        var columnName = "ASSET_GROUP_CODE";
        var checkColumnName = "ASSET_GROUP_ID";
        var selfId = $("#assetGroupId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);       
        window.app.checkUniqueConstraints("assestGroupEdesc",formId,tableName,"ASSET_GROUP_EDESC",checkColumnName,selfId);
        window.app.checkUniqueConstraints("assetGroupNdesc",formId,tableName,"ASSET_GROUP_NDESC",checkColumnName,selfId);
    });
})(window.jQuery,window.app);

