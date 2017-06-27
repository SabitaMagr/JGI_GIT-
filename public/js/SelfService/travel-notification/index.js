(function ($, app) {
    'use strict';
    $(document).ready(function () {
        console.log(document.list);
        $("#travelNotificationTable").kendoGrid({
            excel: {
                fileName: "TravelNotification.xlsx",
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
                {field: "FULL_NAME", title: "Employee", width: 150},
                {field: "FROM_DATE", title: "Start Date",width:100},
                {field: "TO_DATE", title: "To Date",width:90},
                {field: "REQUESTED_DATE", title: "Applied Date",width:120},
                {field: "DESTINATION", title: "Destination",width:110},
                {field: "STATUS", title: "Status",width:80},
                {field: "APPROVED_FLAG", title: "Approved Flag",width:120},
                {title: "Action",width:80}
            ]
        });
        
        app.searchTable('travelNotificationTable',['FULL_NAME','FROM_DATE','TO_DATE','REQUESTED_DATE','DESTINATION','STATUS','APPROVED_FLAG']);
         
        app.pdfExport(
                        'travelNotificationTable',
                        {
                            'FULL_NAME': 'Name',
                            'REQUESTED_DT': 'Request Date',
                            'FROM_DATE': 'From Date',
                            'TO_DATE': 'To Date',
                            'DESTINATION': 'Destination',
                            'REQUESTED_AMOUNT': 'Request Amt',
                            'REQUESTED_TYPE': 'Request Type',
                            'PURPOSE': 'Purpose',
                            'STATUS': 'Status',
                            'REMARKS': 'Remarks',
                            'RECOMMENDER_NAME': 'Recommender',
                            'APPROVER_NAME': 'Approver',
//                            'RECOMMENDED_REMARKS': 'Rec Remarks',
                            'RECOMMENDED_DT': 'Recommended Date',
//                            'APPROVED_REMARKS': 'App Remarks',
                            'APPROVED_DT': 'Approved Date',
                            'SUB_EMPLOYEE_NAME': 'Subsitute Emp',
//                            'SUB_APPROVED_FLAG': 'Sub App Flag',
                            'SUB_APPROVED_DATE': 'Sub Approved Dt',
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
                        {value: "From Date"},
                        {value: "To Date"},
                        {value: "Applied Date"},
                        {value: "Destination"},
                        {value: "Requested Amount"},
                        {value: "Requested Type"},
                        {value: "Status"},
                        {value: "Purpose"},
                        {value: "Remarks"},
                        {value: "Recommender"},
                        {value: "Approver"},
                        {value: "Remarks By Recommender"},
                        {value: "Recommended Date"},
                        {value: "Remarks By Approver"},
                        {value: "Approved Date"},
                        {value: "Travel Substitute"},
                        {value: "Approved Flag"},
                        {value: "Approved Date"},
                    ]
                }];
            var dataSource = $("#travelNotificationTable").data("kendoGrid").dataSource;
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
                        {value: dataItem.FROM_DATE},
                        {value: dataItem.TO_DATE},
                        {value: dataItem.REQUESTED_DATE},
                        {value: dataItem.DESTINATION},
                        {value: dataItem.REQUESTED_AMOUNT},
                        {value: dataItem.REQUESTED_TYPE},
                        {value: dataItem.STATUS},
                        {value: dataItem.PURPOSE},
                        {value: dataItem.REMARKS},
                        {value: dataItem.RECOMMENDER_NAME},
                        {value: dataItem.APPROVER_NAME},
                        {value: dataItem.RECOMMENDED_REMARKS},
                        {value: dataItem.RECOMMENDED_DATE},
                        {value: dataItem.APPROVED_REMARKS},
                        {value: dataItem.APPROVED_DATE},
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
                            {autoWidth: true}
                            
                        ],
                        title: "Travel Notification",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "TravelNotificationList.xlsx"});
        }
        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);
