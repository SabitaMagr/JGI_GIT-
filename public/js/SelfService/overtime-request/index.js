(function ($, app) {
    'use strict';
    $(document).ready(function () {
        console.log(document.overtimeRequestList);
        $("#overtimeTable").kendoGrid({
            excel: {
                fileName: "OvertimeRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.overtimeRequestList,
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
                {field: "OVERTIME_DATE", title: "Overtime Date"},
                {field: "DETAILS", title: "Overtime (From - To)"},
                {field: "TOTAL_HOUR", title: "Total Hour"},
                {field: "REQUESTED_DATE", title: "Requested Date"},
                {field: "STATUS", title: "Status"},
                {title: "Action"}
            ]
        });
        
        app.searchTable('overtimeTable',['OVERTIME_DATE','DETAILS','TOTAL_HOUR','REQUESTED_DATE','STATUS']);
        
        app.pdfExport(
                        'overtimeTable',
                        {
                            'OVERTIME_DATE': ' Overtime Dt',
                            'REQUESTED_DATE': 'Req Dt',
                            'STATUS': 'Status',
                            'DESCRIPTION': 'Desc',
                            'REMARKS': 'Remarks',
                            'RECOMMENDER_NAME': 'Recommender',
                            'APPROVER_NAME': 'Approver',
                            'RECOMMENDED_REMARKS': 'Rec Remarks',
                            'RECOMMENDED_DATE': 'Rec Dt',
                            'APPROVED_REMARKS': 'App Remarks',
                            'APPROVED_DATE': 'App Dt'
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
                        {value: "Overtime Date"},
                        {value: "Time (From - To)"},
                        {value: "Requested Date"},
                        {value: "Status"},
                        {value: "Description"},
                        {value: "Remarks"},
                        {value: "Recommender"},
                        {value: "Approver"},
                        {value: "Remarks By Recommender"},
                        {value: "Recommended Date"},
                        {value: "Remarks By Approver"},
                        {value: "Approved Date"},
                    ]
                }];
            var dataSource = $("#overtimeTable").data("kendoGrid").dataSource;
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
                        {value: dataItem.OVERTIME_DATE},
                        {value: details1},
                        {value: dataItem.REQUESTED_DATE},
                        {value: dataItem.STATUS},
                        {value: dataItem.DESCRIPTION},
                        {value: dataItem.REMARKS},
                        {value: dataItem.RECOMMENDER_NAME},
                        {value: dataItem.APPROVER_NAME},
                        {value: dataItem.RECOMMENDED_REMARKS},
                        {value: dataItem.RECOMMENDED_DATE},
                        {value: dataItem.APPROVED_REMARKS},
                        {value: dataItem.APPROVED_DATE}
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
                        title: "Overtime Request",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "OvertimeRequestList.xlsx"});
        }
        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);