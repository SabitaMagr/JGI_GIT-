(function ($, app) {
    'use strict';
    $(document).ready(function () {
        console.log(document.list);
        $("#leaveNotificationTable").kendoGrid({
            excel: {
                fileName: "LeaveNotification.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.list,
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
                {field: "FIRST_NAME", title: "Employee Name", width: 150},
                {field: "LEAVE_CODE", title: "Leave Code", width: 120},
                {field: "LEAVE_ENAME", title: "Leave Name", width: 120},
                {field: "APPLIED_DATE", title: "Requested Date", width: 140},
                {field: "FROM_DATE", title: "From Date", width: 100},
                {field: "TO_DATE", title: "To Date", width: 90},
                {field: "NO_OF_DAYS", title: "Duration", width: 100},
                {field: "STATUS", title: "Status",width:80},
                {field: "APPROVED_FLAG", title: "Approved Flag",width:120},
                {title: "Action",width:80}
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
            var rows = [{
                    cells: [
                        {value: "Employee Name"},
                        {value: "Leave Code"},
                        {value: "Leave Name"},
                        {value: "Applied Date"},
                        {value: "Start Date"},
                        {value: "End Date"},
                        {value: "Duration"},
                        {value: "Status"},
                        {value: "Remarks"},
                        {value: "Recommender"},
                        {value: "Approver"},
                        {value: "Remarks By Recommender"},
                        {value: "Recommended Date"},
                        {value: "Remarks By Approver"},
                        {value: "Approved Date"},
                        {value: "Leave Substitute"},
                        {value: "Approved Flag"},
                        {value: "Approved Date"},
                    ]
                }];
            var dataSource = $("#leaveNotificationTable").data("kendoGrid").dataSource;
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
                        {value: dataItem.LEAVE_CODE},
                        {value: dataItem.LEAVE_ENAME},
                        {value: dataItem.REQUESTED_DT},
                        {value: dataItem.FROM_DATE},
                        {value: dataItem.TO_DATE},
                        {value: dataItem.NO_OF_DAYS},
                        {value: dataItem.STATUS},
                        {value: dataItem.REMARKS},
                        {value: dataItem.RECOMMENDER_NAME},
                        {value: dataItem.APPROVER_NAME},
                        {value: dataItem.RECOMMENDED_REMARKS},
                        {value: dataItem.RECOMMENDED_DT},
                        {value: dataItem.APPROVED_REMARKS},
                        {value: dataItem.APPROVED_DT},
                        {value: dataItem.SUB_EMPLOYEE_NAME},
                        {value: dataItem.SUB_APPROVED_FLAG},
                        {value: dataItem.SUB_APPROVED_DATE}
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
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true}
                            
                        ],
                        title: "Leave Notification",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "LeaveNotification.xlsx"});
        }
        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);
