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
//            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "FULL_NAME", title: "Employee"},
                {field: "LEAVE_ENAME", title: "Leave"},
                {title: "Applied Date",
                    columns: [{
                            field: "REQUESTED_DT",
                            title: "English",
                            template: "<span>#: (REQUESTED_DT == null) ? '-' : REQUESTED_DT #</span>"},
                        {field: "REQUESTED_DT_N",
                        title: "Nepali",
                        template: "<span>#: (REQUESTED_DT_N == null) ? '-' : REQUESTED_DT_N #</span>"}
                    ]},
                {title: "From Date",
                    columns: [{
                            field: "FROM_DATE",
                            title: "English",
                            template: "<span>#: (FROM_DATE == null) ? '-' : FROM_DATE #</span>"},
                        {field: "FROM_DATE_N",
                        title: "Nepali",
                        template: "<span>#: (FROM_DATE_N == null) ? '-' : FROM_DATE_N #</span>"}
                    ]},
                 {title: "To Date",
                    columns: [{
                            field: "TO_DATE",
                            title: "English",
                            template: "<span>#: (TO_DATE == null) ? '-' : TO_DATE #</span>"},
                        {field: "TO_DATE_N",
                        title: "Nepali",
                        template: "<span>#: (TO_DATE_N == null) ? '-' : TO_DATE_N #</span>"}
                    ]},
                {field: "NO_OF_DAYS", title: "Duration"},
                {field: "STATUS", title: "Status"},
                {field: "APPROVED_FLAG", title: "Approved Flag"},
                 {field: ["ID"], title: "Action", template: `<span><a class="btn-edit" href="` + document.viewLink + `/#: ID #" style="height:17px;" title="view detail">
                            <i class="fa fa-search-plus"></i>
                            </a>
                            </span>`}
            ]
        });
        
        app.searchTable('leaveNotificationTable',['FULL_NAME','LEAVE_ENAME','REQUESTED_DT','REQUESTED_DT_N','FROM_DATE','FROM_DATE_N','TO_DATE','TO_DATE_N','NO_OF_DAYS','STATUS','APPROVED_FLAG']);
        
        app.pdfExport(
                        'leaveNotificationTable',
                        {
                            'FULL_NAME': 'Name',
                            'LEAVE_ENAME': 'Leave',
                            'REQUESTED_DT': 'Requested Date(AD)',
                            'REQUESTED_DT_N': 'Requested Date(BS)',
                            'FROM_DATE': 'From Date(AD)',
                            'FROM_DATE_N': 'From Date(BS)',
                            'TO_DATE': 'To Date(AD)',
                            'TO_DATE_N': 'To Date(BS)',
                            'NO_OF_DAYS': 'No Days',
                            'STATUS': 'Status',
                            'REMARKS': 'Remarks',
                            'RECOMMENDER_NAME': 'Recommender',
                            'APPROVER_NAME': 'Approver',
                            'RECOMMENDED_REMARKS': 'Recommender Remarks',
                            'RECOMMENDED_DT': 'Recommender Date',
                            'APPROVED_REMARKS': 'Approved Remarks',
                            'APPROVED_DT': 'Approved Date',
                            'SUB_EMPLOYEE_NAME': 'Substitute Employee',
                            'SUB_APPROVED_FLAG': 'Substitute App Flag',
                            'SUB_APPROVED_DATE': 'Substitute Approved Date',
                        }
                );
        
        
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
                        {value: "Applied Date(AD)"},
                        {value: "Applied Date(BS)"},
                        {value: "Start Date(AD)"},
                        {value: "Start Date(BS)"},
                        {value: "End Date(AD)"},
                        {value: "End Date(BS)"},
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
                rows.push({
                    cells: [
                        {value: dataItem.FULL_NAME},
                        {value: dataItem.LEAVE_ENAME},
                        {value: dataItem.REQUESTED_DT},
                        {value: dataItem.REQUESTED_DT_N},
                        {value: dataItem.FROM_DATE},
                        {value: dataItem.FROM_DATE_N},
                        {value: dataItem.TO_DATE},
                        {value: dataItem.TO_DATE_N},
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
