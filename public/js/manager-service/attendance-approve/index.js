(function ($) {
    'use strict';
    $(document).ready(function () {

        $("#attendanceApproveTable").kendoGrid({
            excel: {
                fileName: "AttendanceRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.attendanceApprove,
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
                {field: "REQUESTED_DT", title: "Requested Date", width: 160},
                {field: "ATTENDANCE_DT", title: "Attendance Date", width: 180},
                {field: "IN_TIME", title: "Check In", width: 120},
                {field: "OUT_TIME", title: "Check Out", width: 140},
                {field: "YOUR_ROLE", title: "Your Role", width: 140},
                {title: "Action", width: 80}
            ]
        });
        
        app.searchTable('attendanceApproveTable',['FIRST_NAME','REQUESTED_DT','ATTENDANCE_DT','IN_TIME','OUT_TIME','YOUR_ROLE']);
        
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
                        {value: "Attendance Date"},
                        {value: "Check In Time"},
                        {value: "Check Out Time"},
                        {value: "Total Hour"},
                        {value: "Late In Reason"},
                        {value: "Early Out Reason"},
                        {value: "Your Role"},
                        {value: "Status"},
                        {value: "Approved Date"},
                        {value: "Remarks By You"}
                    ]
                }];
            var dataSource = $("#attendanceApproveTable").data("kendoGrid").dataSource;
            var filteredDataSource = new kendo.data.DataSource({
                data: dataSource.data(),
                filter: dataSource.filter()
            });

            filteredDataSource.read();
            var data = filteredDataSource.view();

            for (var i = 0; i < data.length; i++) {
                var dataItem = data[i];
                var middleName = dataItem.MIDDLE_NAME != null ? " " + dataItem.MIDDLE_NAME + " " : " ";
                var middleName1 = dataItem.MIDDLE_NAME1 != null ? " " + dataItem.MIDDLE_NAME1 + " " : " ";
                rows.push({
                    cells: [
                        {value: dataItem.FIRST_NAME + middleName + dataItem.LAST_NAME},
                        {value: dataItem.REQUESTED_DT},
                        {value: dataItem.ATTENDANCE_DT},
                        {value: dataItem.IN_TIME},
                        {value: dataItem.OUT_TIME},
                        {value: dataItem.TOTAL_HOUR},
                        {value: dataItem.IN_REMARKS},
                        {value: dataItem.OUT_REMARKS},
                        {value: "Approver"},
                        {value: dataItem.STATUS},
                        {value: dataItem.APPROVED_DT},
                        {value: dataItem.APPROVED_REMARKS}
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
                        title: "Attendance Request List",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "AttendanceRequest.xlsx"});
        }
    });
})(window.jQuery, window.app);
