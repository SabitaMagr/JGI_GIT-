(function ($) {
    'use strict';
    $(document).ready(function () {    
       console.log(document.flatValues);
        $("#flatValueTable").kendoGrid({
            excel: {
                fileName: "FlatValueList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.flatValues,
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
                {field: "FLAT_EDESC", title: "EDesc"},
                {field: "FLAT_LDESC", title: "LDesc"},
                {field: "SHOW_AT_RULE", title: "Show At Rule"},
                {title: "Action"}
            ]
        }); 
        
        app.searchTable('flatValueTable',['FLAT_EDESC','FLAT_LDESC','SHOW_AT_RULE']);
        
        app.pdfExport(
                'flatValueTable',
                {
                    'FLAT_EDESC': 'Flat Desc in English',
                    'FLAT_LDESC': 'Flat Desc in Nepali',
                    'SHOW_AT_RULE': 'Show At Rule'
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
        $("#export").click(function (e) {
            var grid = $("#flatValueTable").data("kendoGrid");
            grid.saveAsExcel();
        });
    });   
})(window.jQuery, window.app);
