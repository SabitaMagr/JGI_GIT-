(function ($) {
    'use strict';
    $(document).ready(function () {
        console.log(document.group);
        $("#assetGroupTable").kendoGrid({
            excel: {
                fileName: "AssetGroupList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.group,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "ASSET_GROUP_EDESC", title: "Asset Group",width:400},
                {title: "Action",width:120}
            ],
        });
        
        app.searchTable('assetGroupTable',['ASSET_GROUP_EDESC']);
        
        app.pdfExport(
                'assetGroupTable',
                {
                    'ASSET_GROUP_EDESC': 'Asset Group'
                });
        
        $("#export").click(function (e) {
            var grid = $("#assetGroupTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();
    });
})(window.jQuery);