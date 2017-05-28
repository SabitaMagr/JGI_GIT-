(function ($) {
    'use strict';
    $(document).ready(function () {

        $("#leaveApproveTable").kendoGrid({
            excel: {
                fileName: "LeaveRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.leaveApprove,
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
                {field: "FIRST_NAME", title: "Employee", width: 200},
                {field: "LEAVE_ENAME", title: "Leave", width: 120},
                {field: "APPLIED_DATE", title: "Requested Date", width: 140},
                {field: "START_DATE", title: "From Date", width: 100},
                {field: "END_DATE", title: "To Date", width: 90},
                {field: "NO_OF_DAYS", title: "Duration", width: 100},
                {field: "YOUR_ROLE", title: "Your Role", width: 120},
                {title: "Action", width: 70}
            ]
        });
        
        app.searchTable('leaveApproveTable',['FIRST_NAME','LEAVE_ENAME','APPLIED_DATE','START_DATE','END_DATE','NO_OF_DAYS','YOUR_ROLE']);
        
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
                        {value: "Leave Name"},
                        {value: "Requested Date"},
                        {value: "From Date"},
                        {value: "To Date"},
                        {value: "Your Role"},
                        {value: "Duration"},
                        {value: "Status"},
                        {value: "Remarks By Employee"},
                        {value: "Remarks By Recommender"},
                        {value: "Recommended Date"},
                        {value: "Remarks By Approver"},
                        {value: "Approved Date"},
                        
                    ]
                }];
            var dataSource = $("#leaveApproveTable").data("kendoGrid").dataSource;
            var filteredDataSource = new kendo.data.DataSource({
                data: dataSource.data(),
                filter: dataSource.filter()
            });

            filteredDataSource.read();
            var data = filteredDataSource.view();

            for (var i = 0; i < data.length; i++) {
                var dataItem = data[i];
                var middleName = dataItem.MIDDLE_NAME != null ? " " + dataItem.MIDDLE_NAME + " " : " ";

                rows.push({
                    cells: [
                        {value: dataItem.FIRST_NAME + middleName + dataItem.LAST_NAME},
                        {value: dataItem.LEAVE_ENAME},
                        {value: dataItem.APPLIED_DATE},
                        {value: dataItem.START_DATE},
                        {value: dataItem.END_DATE},
                        {value: dataItem.YOUR_ROLE},
                        {value: dataItem.NO_OF_DAYS},
                        {value:  dataItem.STATUS},
                        {value: dataItem.REMARKS},
                        {value: dataItem.RECOMMENDED_REMARKS},
                        {value: dataItem.RECOMMENDED_DT},
                        {value: dataItem.APPROVED_REMARKS},
                        {value: dataItem.APPROVED_DT}
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
                            {autoWidth: true}
                        ],
                        title: "Leave Request List",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "LeaveRequestList.xlsx"});
        }
    });
})(window.jQuery, window.app);
