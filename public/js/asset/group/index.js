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
                {field: "ASSET_GROUP_CODE", title: "Asset Group Code",width:80},
                {field: "ASSET_GROUP_EDESC", title: "Asset Group Name (in Eng.)",width:120},
                {field: "ASSET_GROUP_NDESC", title: "Asset Group Name (in Nep.)",width:120},
                {title: "Action",width:100}
            ],
        });
        $("#export").click(function (e) {
            var grid = $("#assetGroupTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();
    });
})(window.jQuery);