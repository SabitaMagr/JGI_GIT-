(function ($) {
    'use strict';
    $(document).ready(function () {
        console.log(document.trainingApprove);
        $("#trainingApproveTable").kendoGrid({
            excel: {
                fileName: "TrainingRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.trainingApprove,
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
                {field: "FIRST_NAME", title: "Employee", width: 150},
                {field: "TITLE", title: "Training", width: 130},
                {field: "REQUESTED_DATE", title: "Requested Date", width: 140},
                {field: "START_DATE", title: "Start Date", width: 100},
                {field: "END_DATE", title: "End Date", width: 100},
                {field: "DURATION", title: "Duration", width: 100},
                {field: "TRAINING_TYPE", title: "Training Type", width: 120},
                {field: "YOUR_ROLE", title: "Your Role", width: 120},
                {title: "Action", width: 70}
            ]
        });
        
        app.searchTable('trainingApproveTable',['FIRST_NAME','TITLE','REQUESTED_DATE','START_DATE','END_DATE','DURATION','TRAINING_TYPE','YOUR_ROLE']);
        
        app.pdfExport(
                'trainingApproveTable',
                {
                    'FIRST_NAME': 'Name',
                    'MIDDLE_NAME': 'MiddleName',
                    'LAST_NAME': 'LastName',
                    'TRAINING_NAME': 'Training',
                    'REQUESTED_DATE': 'Req.Date',
                    'START_DATE': 'StartDate',
                    'END_DATE': 'EndDate',
                    'DURATION': 'Duration',
                    'TRAINING_TYPE': 'Type',
                    'YOUR_ROLE': 'Role',
                    'STATUS': 'Status',
                    'DESCRIPTION': 'Description',
                    'REMARKS': 'Remarks',
                    'RECOMMENDED_REMARKS': 'R.Remarks',
                    'RECOMMENDED_DATE': 'R.Date',
                    'APPROVED_REMARKS': 'A.Remarks',
                    'APPROVED_DATE': 'A.Date'
                    
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
                        {value: "Training Name"},
                        {value: "Requested Date"},
                        {value: "Start Date"},
                        {value: "End Date"},
                        {value: "Duration"},
                        {value: "Training Type"},
                        {value: "Your Role"},
                        {value: "Status"},
                        {value: "Description"},
                        {value: "Remarks"},
                        {value: "Remarks By Recommender"},
                        {value: "Recommended Date"},
                        {value: "Remarks By Approver"},
                        {value: "Approved Date"}
                    ]
                }];
            var dataSource = $("#trainingApproveTable").data("kendoGrid").dataSource;
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
                        {value: dataItem.TRAINING_NAME},
                        {value: dataItem.REQUESTED_DATE},
                        {value: dataItem.START_DATE},
                        {value: dataItem.END_DATE},
                        {value: dataItem.DURATION},
                        {value: dataItem.TRAINING_TYPE},
                        {value: dataItem.YOUR_ROLE},
                        {value: dataItem.STATUS},
                        {value: dataItem.DESCRIPTION},
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
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true}
                        ],
                        title: "Training Request List",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "TrainingRequestList.xlsx"});
        }
    });
})(window.jQuery, window.app);
