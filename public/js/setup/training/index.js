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
//            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
//                {field: "TRAINING_CODE", title: "Training Code",width:100},
                {field: "TRAINING_NAME", title: "Training"},
                {field: "COMPANY_NAME", title: "Company"},
                {title: "Start Date",
                columns: [
                    {field: "START_DATE",
                        title: "AD",
                        template: "<span>#: (START_DATE == null) ? '-' : START_DATE #</span>"},
                    {field: "START_DATE_N",
                        title: "BS",
                        template: "<span>#: (START_DATE_N == null) ? '-' : START_DATE_N #</span>"} ]},
            {title: "End Date",
                columns: [
                    {field: "END_DATE",
                        title: "AD",
                        template: "<span>#: (END_DATE == null) ? '-' : END_DATE #</span>"},
                    {field: "END_DATE_N",
                        title: "BS",
                        template: "<span>#: (END_DATE_N == null) ? '-' : END_DATE_N #</span>"} ]},
                {field: "DURATION", title: "Duration(in hour)"},
                {field: "INSTITUTE_NAME", title: "Institute"},
                {field: ["TRAINING_ID"], title: "Action", template: `<span>  <a class="btn-edit"
        href="` + document.editLink + `/#:TRAINING_ID#" style="height:17px;">
        <i class="fa fa-edit"></i>
        </a>
        <a class="btn-delete confirmation"
        href="` + document.deleteLink + `/#:TRAINING_ID#" id="bs_#:TRAINING_ID #" style="height:17px;"> 
        <i class="fa fa-trash-o"></i></a>
        </span>`}
            ]
        });

        app.searchTable('trainingTable', ['TRAINING_NAME', 'COMPANY_NAME', 'START_DATE', 'END_DATE', 'START_DATE_N', 'END_DATE_N', 'DURATION', 'INSTITUTE_NAME']);

        app.pdfExport(
                'trainingTable',
                {
                    'TRAINING_NAME': 'Training',
                    'COMPANY_NAME': 'Company',
                    'START_DATE': 'Start Date(AD)',
                    'START_DATE_N': 'Start Date(BS)',
                    'END_DATE': 'End Date(AD)',
                    'END_DATE_N': 'End Date(BS)',
                    'DURATION': 'Duration',
                    'INSTITUTE_NAME': 'Institute'
                }
        );

        $("#export").click(function (e) {
            var rows = [{
                    cells: [
                        {value: "Training Name"},
                        {value: "Company"},
                        {value: "Training Type"},
                        {value: "Start Date(AD)"},
                        {value: "Start Date(BS)"},
                        {value: "End Date(AD)"},
                        {value: "End Date(BS)"},
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
                        {value: dataItem.START_DATE_N},
                        {value: dataItem.END_DATE},
                        {value: dataItem.END_DATE_N},
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