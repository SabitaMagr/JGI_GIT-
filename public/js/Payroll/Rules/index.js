(function ($) {
    'use strict';
    $(document).ready(function () {    
        $("#ruleTable").kendoGrid({
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
            dataBound:gridDataBound,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "PAY_CODE", title: "Pay Code"},
                {field: "PAY_EDESC", title: "EDesc"},
                {field: "PAY_TYPE_FLAG", title: "Type"},
                {field: "PRIORITY_INDEX", title: "Priority Index"},
                {title: "Action"}
            ]
        });  
        function gridDataBound(e) {
            var grid = e.sender;
            if (grid.dataSource.total() == 0) {
                var colCount = grid.columns.length;
                $(e.sender.wrapper)
                        .find('tbody')
                        .append('<tr class="kendo-data-row"><td colspan="' + colCount + '" class="no-data">There is no data to show in the grid.</td></tr>');
            }
        };
    });   
})(window.jQuery, window.app);
