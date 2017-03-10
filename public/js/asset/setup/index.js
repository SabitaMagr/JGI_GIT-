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
                {field: "ASSET_CODE", title: "Asset Code", width: 50},
                {field: "ASSET_EDESC", title: "Asset Name (in Eng.)", width: 100},
                {field: "ASSET_GROUP_EDESC", title: "Asset Group", width: 80},
                {field: "BRAND_NAME", title: "Brand Name ", width: 80},
                {field: "MODEL_NO", title: "Model No ", width: 80},
                {field: "QUANTITY", title: "Quantity ", width: 80},
                {title: "Action", width: 100}
            ],
        });
        $("#export").click(function (e) {
            var grid = $("#assetSetupTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();
    });

})(window.jQuery);