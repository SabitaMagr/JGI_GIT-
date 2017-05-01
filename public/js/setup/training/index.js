(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("#trainingTable").kendoGrid({
            dataSource: {
                data: document.trainings,
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
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
//                {field: "TRAINING_CODE", title: "Training Code",width:100},
                {field: "TRAINING_NAME", title: "Training Name",width:130},
                {field: "COMPANY_NAME", title: "Company",width:110},
                {field: "START_DATE", title: "Start Date",width:110},
                {field: "END_DATE", title: "End Date",width:110},
                {field: "DURATION", title: "Duration(in hour)",width:120},
                {field: "INSTITUTE_NAME", title: "Institute Name",width:130},
                {title: "Action",width:110}
            ]
        });
        
        $("#export").click(function (e) {
            var rows = [{
                    cells: [
                        {value: "Training Name"},
                        {value: "Company"},
                        {value: "Training Type"},
                        {value: "Start Date"},
                        {value: "End Date"},
                        {value: "Duration(in hour)"},
                        {value: "Institute Name"},
                        {value: "Instructor Name"},
                        {value: "Remarks"}
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
//                        {value: dataItem.TRAINING_CODE},
                        {value: dataItem.TRAINING_NAME},
                        {value: dataItem.COMPANY_NAME},
                        {value: dataItem.TRAINING_TYPE},
                        {value: dataItem.START_DATE},
                        {value: dataItem.END_DATE},
                        {value: dataItem.DURATION},
                        {value: dataItem.INSTITUTE_NAME},
                        {value: dataItem.INSTRUCTOR_NAME},
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
                            {autoWidth: true}
                        ],
                        title: "Training",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "TrainingList.xlsx"});
        }
        
        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);