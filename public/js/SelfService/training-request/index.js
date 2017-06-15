(function ($, app) {
    'use strict';
    $(document).ready(function () {
        console.log(document.trainingRequestList);
        $("#trainingRequestTable").kendoGrid({
            excel: {
                fileName: "TrainingRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.trainingRequestList,
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
                {field: "TITLE", title: "Training Name"},
                {field: "REQUESTED_DATE", title: "Applied Date"},
                {field: "START_DATE", title: "Start Date"},
                {field: "END_DATE", title: "End Date"},
                {field: "DURATION", title: "Duration"},
                {field: "TRAINING_TYPE", title: "Training Type"},
                {field: "STATUS", title: "Status"},
                {title: "Action"}
            ]
        });
        
        app.searchTable('trainingRequestTable',['TITLE','REQUESTED_DATE','START_DATE','END_DATE','DURATION','TRAINING_TYPE','STATUS']);
        
        app.pdfExport(
                        'trainingRequestTable',
                        {
                            'TRAINING_CODE': 'Training',
                            'TITLE': 'Title',
                            'REQUESTED_DATE': 'Req Dt',
                            'START_DATE': 'Start Dt',
                            'END_DATE': 'End Dt',
                            'DURATION': 'Duration',
                            'TRAINING_TYPE': 'Type',
                            'STATUS': 'Status',
                            'DESCRIPTION': 'Desc',
                            'REMARKS': 'Remarks',
                            'RECOMMENDER_NAME': 'Recommeder',
                            'APPROVER_NAME': 'Approver',
//                            'RECOMMENDED_REMARKS': 'Rec Remarks',
                            'RECOMMENDED_DATE': 'Rec Date',
//                            'APPROVED_REMARKS': 'App Remarks',
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
                        {value: "Training Name"},
                        {value: "Applied Date"},
                        {value: "Start Date"},
                        {value: "End Date"},
                        {value: "Duration"},
                        {value: "Training Type"},
                        {value: "Status"},
                        {value: "Description"},
                        {value: "Remarks"},
                        {value: "Recommender"},
                        {value: "Approver"},
                        {value: "Remarks By Recommender"},
                        {value: "Recommended Date"},
                        {value: "Remarks By Approver"},
                        {value: "Approved Date"}
                    ]
                }];
            var dataSource = $("#trainingRequestTable").data("kendoGrid").dataSource;
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
                        {value: dataItem.TITLE},
                        {value: dataItem.REQUESTED_DATE},
                        {value: dataItem.START_DATE},
                        {value: dataItem.END_DATE},
                        {value: dataItem.DURATION},
                        {value: dataItem.TRAINING_TYPE},
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
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true}
                        ],
                        title: "Training Request",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "TrainingRequestList.xlsx"});
        }
        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);