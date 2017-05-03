(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#serviceQuestionTable").kendoGrid({
            dataSource: {
                data: document.serviceQuestion,
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
                {field: "QUESTION_EDESC", title: "Question Name", width: 150},
                {field: "PARENT_QUESTION_EDESC", title: "Prent Question", width: 130},
                {field: "SERVICE_EVENT_TYPE_NAME", title: "Service Event Type", width: 150},
                {title: "Action", width: 80}
            ]
        });

        $("#export").click(function (e) {
            var rows = [{
                    cells: [
                        {value: "Question Name (in Eng.)"},
                        {value: "Question Name (in Nep.)"},
                        {value: "Parent Question Name"},
                        {value: "Service Event Type"},
                        {value: "Remarks"}
                    ]
                }];
            var dataSource = $("#serviceQuestionTable").data("kendoGrid").dataSource;
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
                        {value: dataItem.QUESTION_EDESC},
                        {value: dataItem.QUESTION_NDESC},
                        {value: dataItem.PARENT_QUESTION_EDESC},
                        {value: dataItem.SERVICE_EVENT_TYPE_NAME},
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
                            {autoWidth: true}
                        ],
                        title: "Service Question List",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "ServiceQuestionList.xlsx"});
        }

        window.app.UIConfirmations();

    });
})(window.jQuery);