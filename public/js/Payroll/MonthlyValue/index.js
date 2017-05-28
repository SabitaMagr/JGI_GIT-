(function ($) {
    'use strict';
    $(document).ready(function () {    
       
        $("#monthlyValueTable").kendoGrid({
            excel: {
                fileName: "MonthlyValueList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.monthlyValues,
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
                {field: "MTH_EDESC", title: "EDesc"},
                {field: "MTH_LDESC", title: "NDesc"},
                {field: "SHOW_AT_RULE", title: "Show At Rule"},
                {field: "SH_INDEX_NO", title: "Index No"},
                {title: "Action"}
            ]
        });  
        
        app.searchTable('monthlyValueTable',['MTH_EDESC','MTH_LDESC','SHOW_AT_RULE','SH_INDEX_NO']);
        
        function gridDataBound(e) {
            var grid = e.sender;
            if (grid.dataSource.total() == 0) {
                var colCount = grid.columns.length;
                $(e.sender.wrapper)
                        .find('tbody')
                        .append('<tr class="kendo-data-row"><td colspan="' + colCount + '" class="no-data">There is no data to show in the grid.</td></tr>');
            }
        };
        $("#export").click(function (e) {
            var grid = $("#monthlyValueTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();
    });   
})(window.jQuery, window.app);
