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
//            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "TRAINING_NAME", title: "Training"},
                {title: "Start Date",
                    columns: [{
                            field: "START_DATE",
                            title: "English",
                            template: "<span>#: (START_DATE == null) ? '-' : START_DATE #</span>"},
                        {field: "START_DATE_N",
                            title: "Nepali",
                            template: "<span>#: (START_DATE_N == null) ? '-' : START_DATE_N #</span>"}]},
                {title: "End Date",
                    columns: [{
                            field: "END_DATE",
                            title: "English",
                            template: "<span>#: (END_DATE == null) ? '-' : END_DATE #</span>"},
                        {field: "END_DATE_N",
                            title: "Nepali",
                            template: "<span>#: (END_DATE_N == null) ? '-' : END_DATE_N #</span>"}]},
                {field: "DURATION", title: "Duration(in hour)"},
                {field: "INSTITUTE_NAME", title: "Institute Name",
                    template: "<span>#: (INSTITUTE_NAME == null) ? '-' : INSTITUTE_NAME #</span>"
                },
                {field: "LOCATION", title: "Location"},
                {field: ["EMPLOYEE_ID", "TRAINING_ID"], title: "Action", template: `<span><a class="btn-edit" href="` + document.editLink + `/#:EMPLOYEE_ID #/#:TRAINING_ID #" style="height:17px;" title="view detail">'
<i class="fa fa-search-plus"></i>
</a>

</span>`
                }
            ]
        });

        app.searchTable('trainingTable', ['TRAINING_CODE', 'START_DATE','START_DATE_N', 'END_DATE', 'END_DATE_N', 'DURATION', 'INSTITUTE_NAME', 'LOCATION']);

        app.pdfExport(
                'trainingTable',
                {
                    'TRAINING_NAME': 'Training',
                    'START_DATE': 'Start Date(AD)',
                    'START_DATE_N': 'Start Date(BS)',
                    'END_DATE': 'End Date(AD)',
                    'END_DATE_N': 'End Date(BS)',
                    'DURATION': 'Duration',
                    'INSTITUTE_NAME': 'Institute',
                    'LOCATION': 'Location'

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
                        {value: "Start Date(AD)"},
                        {value: "Start Date(BS)"},
                        {value: "End Date(AD)"},
                        {value: "End Date(BS)"},
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
                        {value: dataItem.START_DATE_N},
                        {value: dataItem.END_DATE},
                        {value: dataItem.END_DATE_N},
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
