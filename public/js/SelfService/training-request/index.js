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
//            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "TITLE", title: "Training Name"},
                {title: "Applied Date",
                    columns: [{
                            field: "REQUESTED_DATE",
                            title: "English",
                            template: "<span>#: (REQUESTED_DATE == null) ? '-' : REQUESTED_DATE #</span>"},
                        {field: "REQUESTED_DATE_N",
                        title: "Nepali",
                        template: "<span>#: (REQUESTED_DATE_N == null) ? '-' : REQUESTED_DATE_N #</span>"}
                    ]},
                {title: "Start Date",
                    columns: [{
                            field: "START_DATE",
                            title: "English",
                            template: "<span>#: (START_DATE == null) ? '-' : START_DATE #</span>"},
                        {field: "START_DATE_N",
                        title: "Nepali",
                        template: "<span>#: (START_DATE_N == null) ? '-' : START_DATE_N #</span>"}
                    ]},
                {title: "End Date",
                    columns: [{
                            field: "END_DATE",
                            title: "English",
                            template: "<span>#: (END_DATE == null) ? '-' : END_DATE #</span>"},
                        {field: "END_DATE_N",
                        title: "Nepali",
                        template: "<span>#: (END_DATE_N == null) ? '-' : END_DATE_N #</span>"}
                    ]},
                {field: "DURATION", title: "Duration"},
                {field: "TRAINING_TYPE", title: "Training Type"},
                {field: "STATUS", title: "Status"},
                {field: ["REQUEST_ID", "ALLOW_TO_EDIT"], title: "Action", template: `<span><a class="btn-edit" href="` + document.viewLink + `/#: REQUEST_ID #" style="height:17px;" title="view detail">
                            <i class="fa fa-search-plus"></i>
                            </a>
                            #if(ALLOW_TO_EDIT == 1){#       
                            <a class="confirmation btn-delete" href="` + document.deleteLink + `/#: REQUEST_ID #" id="bs_#:REQUEST_ID #" style="height:17px;">
                            <i class="fa fa-trash-o"></i>
                            </a> #}#
                            </span>`}
            ]
        });
        
        app.searchTable('trainingRequestTable',['TITLE','REQUESTED_DATE', 'REQUESTED_DATE_N','START_DATE', 'START_DATE_N','END_DATE', 'END_DATE_N','DURATION','TRAINING_TYPE','STATUS']);
        
        app.pdfExport(
                        'trainingRequestTable',
                        {
                            'TRAINING_CODE': 'Training',
                            'TITLE': 'Title',
                            'REQUESTED_DATE': 'Requested Date(AD',
                            'REQUESTED_DATE_N': 'Requested Date(BS)',
                            'START_DATE': 'Start Date(AD)',
                            'START_DATE_N': 'Start Date(BS)',
                            'END_DATE': 'End Date(AD)',
                            'END_DATE_N': 'End Date(BS)',
                            'DURATION': 'Duration',
                            'TRAINING_TYPE': 'Type',
                            'STATUS': 'Status',
                            'DESCRIPTION': 'Description',
                            'REMARKS': 'Remarks',
                            'RECOMMENDER_NAME': 'Recommeder',
                            'APPROVER_NAME': 'Approver',
                            'RECOMMENDED_REMARKS': 'Recommeder Remarks',
                            'RECOMMENDED_DATE': 'Recommeder Date',
                            'APPROVED_REMARKS': 'Approved Remarks',
                            'APPROVED_DATE': 'Approved Dt'
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
                        {value: "Applied Date(AD"},
                        {value: "Applied Date(BS)"},
                        {value: "Start Date(AD)"},
                        {value: "Start Date(BS)"},
                        {value: "End Date(AD)"},
                        {value: "End Date(BS)"},
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
                        {value: dataItem.REQUESTED_DATE_N},
                        {value: dataItem.START_DATE},
                        {value: dataItem.START_DATE_N},
                        {value: dataItem.END_DATE},
                        {value: dataItem.END_DATE_N},
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