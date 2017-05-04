(function ($) {
    'use strict';

    $(document).ready(function () {
        console.log(document.setup);
        $("#assetSetupTable").kendoGrid({
            excel: {
                fileName: "AssetSetupList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.setup,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "ASSET_EDESC", title: "Asset", width: 120},
                {field: "ASSET_GROUP_EDESC", title: "Asset Group", width: 140},
                {field: "BRAND_NAME", title: "Brand", width: 130},
                {field: "MODEL_NO", title: "Model No ", width: 140},
                {field: "QUANTITY", title: "Quantity ", width: 140},
                {field: "QUANTITY_BALANCE", title: " Stock Qty ", width: 140},
                {title: "Action", width: 120}
            ],
        });
        $("#export").click(function (e) {
            var grid = $("#assetSetupTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();
    });

})(window.jQuery);