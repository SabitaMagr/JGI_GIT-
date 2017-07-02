(function ($) {
    'use strict';
    $(document).ready(function () {
        console.log(document.overtimeRequest);
        $("#overtimeApproveTable").kendoGrid({
            excel: {
                fileName: "OvertimeRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.overtimeRequest,
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
                {field: "REQUESTED_DATE", title: "Requested Date", width: 120},
                {field: "OVERTIME_DATE", title: "Overtime Date", width: 100},
                {field: "DETAILS", title: "Time (From-To)", width: 150},
                {field: "TOTAL_HOUR", title: "Total Hour", width: 100},
                {field: "YOUR_ROLE", title: "Your Role", width: 120},
                {title: "Action", width: 70}
            ]
        });
        
        app.searchTable('overtimeApproveTable',['FULL_NAME','REQUESTED_DATE','OVERTIME_DATE','TOTAL_HOUR','YOUR_ROLE']);
        
        app.pdfExport(
                'overtimeApproveTable',
                {
                    'FULL_NAME': 'Name',
                    'REQUESTED_DATE': 'Request Date',
                    'OVERTIME_DATE': 'Overtime Date',
                    'TOTAL_HOUR': 'Total Hour',
                    'DESCRIPTION': 'Description',
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
                        {value: "Requested Date"},
                        {value: "Overtime Date"},
                        {value: "Time (From-To)"},
                        {value: "Total Hour"},
                        {value: "Description"},
                        {value: "Your Role"},
                        {value: "Status"},
                        {value: "Remarks"},
                        {value: "Remarks By Recommender"},
                        {value: "Recommended Date"},
                        {value: "Remarks By Approver"},
                        {value: "Approved Date"}
                    ]
                }];
            var dataSource = $("#overtimeApproveTable").data("kendoGrid").dataSource;
            var filteredDataSource = new kendo.data.DataSource({
                data: dataSource.data(),
                filter: dataSource.filter()
            });

            filteredDataSource.read();
            var data = filteredDataSource.view();

            for (var i = 0; i < data.length; i++) {
                var dataItem = data[i];
                var details = [];
                for (var j = 0; j < dataItem.DETAILS.length; j++) {
                    details.push(dataItem.DETAILS[j].START_TIME+"-"+dataItem.DETAILS[j].END_TIME);
                }
                var details1 = details.toString();
                rows.push({
                    cells: [
                        {value: dataItem.FULL_NAME},
                        {value: dataItem.REQUESTED_DATE},
                        {value: dataItem.OVERTIME_DATE},
                        {value: details1},
                        {value: dataItem.TOTAL_HOUR},
                        {value: dataItem.DESCRIPTION},
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
                        title: "Overtime Request List",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "OvertimeRequestList.xlsx"});
        }
    });
})(window.jQuery, window.app);
