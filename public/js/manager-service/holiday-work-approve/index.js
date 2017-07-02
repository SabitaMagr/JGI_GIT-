(function ($) {
    'use strict';
    $(document).ready(function () {
        console.log(document.holidayWorkApprove);
        $("#holidayWorkApproveTable").kendoGrid({
            excel: {
                fileName: "HolidayWorkRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.holidayWorkApprove,
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
                {field: "FULL_NAME", title: "Employee", width: 150},
                {field: "HOLIDAY_ENAME", title: "Holiday", width: 120},
                {field: "REQUESTED_DATE", title: "Requested Date", width: 140},
                {field: "FROM_DATE", title: "From Date", width: 100},
                {field: "TO_DATE", title: "To Date", width: 100},
                {field: "DURATION", title: "Duration", width: 120},
                {field: "YOUR_ROLE", title: "Your Role", width: 120},
                {title: "Action", width: 70}
            ]
        });
        
        app.searchTable('holidayWorkApproveTable',['FULL_NAME','HOLIDAY_ENAME','REQUESTED_DATE','FROM_DATE','TO_DATE','DURATION','YOUR_ROLE']);
        
         app.pdfExport(
                'holidayWorkApproveTable',
                {
                    'FULL_NAME': 'Name',
                    'HOLIDAY_ENAME': 'Holiday',
                    'REQUESTED_DATE': 'Request Date',
                    'FROM_DATE': 'From Date',
                    'TO_DATE': 'To Date',
                    'DURATION': 'Duration',
                    'YOUR_ROLE': 'Role',
                    'STATUS': 'Status',
                    'REMARKS': 'Remarks',
                    'RECOMMENDED_REMARKS': 'Recommended Remarks',
                    'RECOMMENDED_DATE': 'Recommended Date',
                    'APPROVED_REMARKS': 'Approved Remarks',
                    'APPROVED_DATE': 'Approved Date'
                    
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
            var rows = [{
                    cells: [
                        {value: "Employee Name"},
                        {value: "Holiday Name"},
                        {value: "Requested Date"},
                        {value: "From Date"},
                        {value: "To Date"},
                        {value: "Duration"},
                        {value: "Your Role"},
                        {value: "Status"},
                        {value: "Remarks"},
                        {value: "Remarks By Recommender"},
                        {value: "Recommended Date"},
                        {value: "Remarks By Approver"},
                        {value: "Approved Date"}
                    ]
                }];
            var dataSource = $("#holidayWorkApproveTable").data("kendoGrid").dataSource;
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
                        {value: dataItem.FULL_NAME},
                        {value: dataItem.HOLIDAY_ENAME},
                        {value: dataItem.REQUESTED_DATE},
                        {value: dataItem.FROM_DATE},
                        {value: dataItem.TO_DATE},
                        {value: dataItem.DURATION},
                        {value: dataItem.YOUR_ROLE},
                        {value: dataItem.STATUS},
                        {value: dataItem.REMARKS},
                        {value: dataItem.RECOMMENDED_REMARKS},
                        {value: dataItem.RECOMMENDED_DATE},
                        {value: dataItem.APPROVED_REMARKS},
                        {value: dataItem.APPROVED_DATE},
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
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true}
                        ],
                        title: "Work on Holiday Request List",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "HolidayWorkRequestList.xlsx"});
        }
    });
})(window.jQuery, window.app);
