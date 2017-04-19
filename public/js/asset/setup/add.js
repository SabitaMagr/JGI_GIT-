(function ($, app) {
    'use strict';
    
    
     $(document).ready(function () {
         
    app.addDatePicker($('#purchaseDate'));
    app.addDatePicker($('#expiaryDate'));
        $('select').select2();
        
        var inputFieldId = "assetCode";
        var formId = "assetSetup-form";
        var tableName =  "HRIS_ASSET_SETUP";
        var columnName = "ASSET_CODE";
        var checkColumnName = "ASSET_ID";
        var selfId = $("#assetSetupId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId, function () {
            App.blockUI({target: "#hris-page-content"});
        });       
        window.app.checkUniqueConstraints("assetEdesc",formId,tableName,"ASSET_EDESC",checkColumnName,selfId);
        window.app.checkUniqueConstraints("assetNdesc",formId,tableName,"ASSET_NDESC",checkColumnName,selfId);
    });
    

    
    
    
    })(window.jQuery, window.app);