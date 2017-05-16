(function ($) {
    'use strict';
    $(document).ready(function () {

        console.log(document.shifts);
        
        $("#shiftTable").kendoGrid({
            excel: {
                fileName: "ShiftList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.shifts,
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
//                {field: "SHIFT_CODE", title: "Shift Code",width:80},
                {field: "SHIFT_ENAME", title: "Shift",width:120},
                {field: "COMPANY_NAME", title: "COMPANY",width:130},
                {field: "START_TIME", title: "Start Time",width:120},
                {field: "END_TIME", title: "End Time",width:120},
                {title: "Action",width:110}
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
        }
        ;
        $("#export").click(function (e) {
//            var grid = $("#shiftTable").data("kendoGrid");
//            grid.saveAsExcel();

              var rows = [{
                    cells: [
                        {value: "Shift"},
                        {value: "Company"},
                        {value: "Start Time"},
                        {value: "End Time"},
                        {value: "Start Date"},
                        {value: "End Date"},
                        {value: "Half Time"},
                        {value: "Half Day End Time"},
                        {value: "Late In"},
                        {value: "Early Out"},
                        {value: "Remarks"}
                    ]
                }];
            var dataSource = $("#shiftTable").data("kendoGrid").dataSource;
            var filteredDataSource = new kendo.data.DataSource({
                data: dataSource.data(),
                filter: dataSource.filter()
            });

            filteredDataSource.read();
            var data = filteredDataSource.view();
            
            for (var i = 0; i < data.length; i++) {
                var dataItem = data[i];
                rows.push({
                    cells: [
                        {value: dataItem.SHIFT_ENAME},
                        {value: dataItem.COMPANY_NAME},
                        {value: dataItem.START_TIME},
                        {value: dataItem.END_TIME},
                        {value: dataItem.START_DATE},
                        {value: dataItem.END_DATE},
                        {value: dataItem.HALF_TIME},
                        {value: dataItem.HALF_DAY_END_TIME},
                        {value: dataItem.LATE_IN},
                        {value: dataItem.EARLY_OUT},
                        {value: dataItem.REMARKS},
                
                    ]
                });
            }
            excelExport(rows);
            e.preventDefault();


        });
        
        
          function excelExport(rows) {
            var workbook = new kendo.ooxml.Workbook({
                sheets: [
                    {
                        columns: [
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true}
                        ],
                        title: "Training",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "ShiftList.xlsx"});
        }
        
        
        
        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);
