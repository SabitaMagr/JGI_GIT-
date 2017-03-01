(function ($) {
    'use strict';
    $(document).ready(function () {
        console.log(document.questions);
        $("#appraisalQuestionTable").kendoGrid({
            excel: {
                fileName: "AppraisalQuestionList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.questions,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "QUESTION_CODE", title: "Question Code", width: 100},
                {field: "QUESTION_EDESC", title: "Question Edesc", width: 120},
                {field: "QUESTION_NDESC", title: "Question Ndesc", width: 120},
                {field: "ANSWER_TYPE", title: "Answer Type", width: 100},
                {field: "ORDER_NO", title: "Order No.", width: 80},
                {field: "HEADING_EDESC", title: "Heading Name", width: 100},
                {title: "Action", width: 100}
            ],
        });
        $("#export").click(function (e) {
            var rows = [{
                    cells: [
                        {value: "Question Code"},
                        {value: "Question Edesc"},
                        {value: "Question Ndesc"},
                        {value: "Answer Type"},
                        {value: "Order No."},
                        {value: "Heading Name"},
                        {value: "Appraisee Flag"},
                        {value: "Appraiser Flag"},
                        {value: "Reviewer Flag"},
                        {value: "Appraisee Rating"},
                        {value: "Appraiser Rating"},
                        {value: "Reviewer Rating"},
                        {value: "Min. Value"},
                        {value: "Max. Value"},
                        {value: "Remarks"}
                    ]
                }];
            var dataSource = $("#appraisalQuestionTable").data("kendoGrid").dataSource;
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
                        {value: dataItem.QUESTION_CODE},
                        {value: dataItem.QUESTION_EDESC},
                        {value: dataItem.QUESTION_NDESC},
                        {value: dataItem.ANSWER_TYPE},
                        {value: dataItem.ORDER_NO},
                        {value: dataItem.HEADING_EDESC},
                        {value: dataItem.APPRAISEE_FLAG},
                        {value: dataItem.APPRAISER_FLAG},
                        {value: dataItem.REVIEWER_FLAG},
                        {value: dataItem.APPRAISEE_RATING},
                        {value: dataItem.APPRAISER_RATING},
                        {value: dataItem.REVIEWER_RATING},
                        {value: dataItem.MIN_VALUE},
                        {value: dataItem.MAX_VALUE},
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
                            {autoWidth: true}
                        ],
                        title: "Appraisal Question List",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "AppraisalQuestionList.xlsx"});
        }
        window.app.UIConfirmations();
    });
})(window.jQuery);