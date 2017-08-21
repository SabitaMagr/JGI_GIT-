(function ($) {
    'use strict';
    $(document).ready(function () {

        $("#trainingTable").kendoGrid({
            excel: {
                fileName: "TrainingList.xlsx",
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
                {field: "TRAINING_NAME", title: "Training",width:90},
                {field: "START_DATE", title: "Start Date",width:80},
                {field: "END_DATE", title: "End Date",width:80},
                {field: "DURATION", title: "Duration(in hour)",width:100},
                {field: "INSTITUTE_NAME", title: "Institute Name",width:100},
                {field: "LOCATION", title: "Location",width:100},
                {title:"Action",width:60}
            ]
        });
        
        app.searchTable('trainingTable',['TRAINING_CODE','START_DATE','END_DATE','DURATION','INSTITUTE_NAME','LOCATION']);
        
        app.pdfExport(
                'trainingTable',
                {
                    'TRAINING_NAME': 'Training',
                    'START_DATE': 'Start Date',
                    'END_DATE':'End Date',
                    'DURATION':'Duration',
                    'INSTITUTE_NAME':'Institute',
                    'LOCATION':'Location'
                
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
                        {value: "Training Name"},
                        {value: "Start Date"},
                        {value: "End Date"},
                        {value: "Duration(in hour)"},
                        {value: "Training Type"},
                        {value: "Instructor Name"},
                        {value: "Institute Name"},
                        {value: "Location"},
                        {value: "Telephone"},
                        {value: "Email"},
                        {value: "Remarks for Training"}
                    ]
                }];
            var dataSource = $("#trainingTable").data("kendoGrid").dataSource;
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
                        {value: dataItem.TRAINING_NAME},
                        {value: dataItem.START_DATE},
                        {value: dataItem.END_DATE},
                        {value: dataItem.DURATION},
                        {value: dataItem.TRAINING_TYPE},
                        {value: dataItem.INSTRUCTOR_NAME},
                        {value: dataItem.INSTITUTE_NAME},
                        {value: dataItem.LOCATION},
                        {value: dataItem.TELEPHONE},
                        {value: dataItem.EMAIL},
                        {value: dataItem.REMARKS}
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
                            {autoWidth: true}
                        ],
                        title: "Training List",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "TrainingList.xlsx"});
        }
    });
})(window.jQuery, window.app);
