(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#ruleTable").kendoGrid({
            excel: {
                fileName: "RuleList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.rules,
                pageSize: 20
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: {
                input: true,
                numeric: false
            },
            dataBound: gridDataBound,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "PRIORITY_INDEX", title: "Priority", type: "numbers"},
                {field: "PAY_EDESC", title: "Rules"},
                {field: "PAY_TYPE_FLAG", title: "Type"},
                {title: "Action"}
            ]
        });

        app.searchTable('ruleTable', ['PAY_EDESC', 'PAY_TYPE_FLAG', 'PRIORITY_INDEX']);

        app.pdfExport(
                'ruleTable',
                {
                    'PRIORITY_INDEX': 'Priority',
                    'PAY_EDESC': 'Rules',
                    'PAY_TYPE_FLAG': 'Type'
                });

        function gridDataBound(e) {
            var grid = e.sender;
            if (grid.dataSource.total() == 0) {
                var colCount = grid.columns.length;
                $(e.sender.wrapper)
                        .find('tbody')
                        .append('<tr class="kendo-data-row"><td colspan="' + colCount + '" class="no-data">There is no data to show in the grid.</td></tr>');
            }
        }
        ;
        $("#export").click(function (e) {
            var grid = $("#ruleTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);
