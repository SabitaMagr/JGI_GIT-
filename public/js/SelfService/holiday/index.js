(function ($) {
    'use strict';
    $(document).ready(function () {

        $("#holidayTable").kendoGrid({
            excel: {
                fileName: "HolidayList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.holidays,
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
                {field: "HOLIDAY_ENAME", title: "Holiday Name"},
                {field: "START_DATE", title: "Start Date"},
                {field: "START_DATE", title: "Start Date Nep"},
                {field: "END_DATE", title: "End Date"},
                {field: "END_DATE", title: "End Date Nep"},
                {field: "HALF_DAY", title: "Half Day"},
            ]
        });
        
        app.searchTable('holidayTable',['HOLIDAY_ENAME','START_DATE','END_DATE','HALF_DAY']);
        
        app.pdfExport(
                'holidayTable',
                {
                    'HOLIDAY_ENAME': 'Holiday',
                    'START_DATE': 'Start Date,',
                    'END_DATE':'End Date',
                    'HALF_DAY':'HALF_DAY'
                
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
            var grid = $("#holidayTable").data("kendoGrid");
            grid.saveAsExcel();
        });
    });
})(window.jQuery, window.app);
