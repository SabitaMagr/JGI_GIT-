(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("#advanceTable").kendoGrid({
            excel: {
                fileName: "AdvanceRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.advanceRequestList,
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
                {field: "ADVANCE_NAME", title: "Advance Name"},
                {title: "Applied Date",
                    columns: [{
                            field: "REQUESTED_DATE",
                            title: "English",
                            template: "<span>#: (REQUESTED_DATE == null) ? '-' : REQUESTED_DATE #</span>"},
                        {field: "REQUESTED_DATE_N",
                            title: "Nepali",
                            template: "<span>#: (REQUESTED_DATE_N == null) ? '-' : REQUESTED_DATE_N #</span>"}]},
                {title: "Advance Date",
                    columns: [{
                            field: "ADVANCE_DATE",
                            title: "English",
                            template: "<span>#: (ADVANCE_DATE == null) ? '-' : ADVANCE_DATE #</span>"},
                        {field: "ADVANCE_DATE_N",
                            title: "Nepali",
                            template: "<span>#: (ADVANCE_DATE_N == null) ? '-' : ADVANCE_DATE_N #</span>"}]},
                {field: "REQUESTED_AMOUNT", title: "Request Amount"},
                {field: "TERMS", title: "Terms(in month)"},
                {field: "STATUS", title: "Status"},
                {field: ["ADVANCE_REQUEST_ID", "ALLOW_TO_EDIT"], title: "Action", template: `<span><a class="btn-edit" href="` + document.viewLink + `/#: ADVANCE_REQUEST_ID #" style="height:17px;" title="view detail">
                            <i class="fa fa-search-plus"></i>
                            </a>
                            #if(ALLOW_TO_EDIT == 1){#       
                            <a class="confirmation btn-delete" href="` + document.deleteLink + `/#: ADVANCE_REQUEST_ID #" id="bs_#:ADVANCE_REQUEST_ID #" style="height:17px;">
                            <i class="fa fa-trash-o"></i>
                            </a> #}#
                            </span>`
                }
            ]
        }
        );

        app.searchTable('advanceTable', ['ADVANCE_NAME', 'REQUESTED_DATE', 'REQUESTED_DATE_N', 'ADVANCE_DATE', 'ADVANCE_DATE_N', 'REQUESTED_AMOUNT', 'TERMS', 'STATUS']);

        app.pdfExport(
                'advanceTable',
                {
                    'ADVANCE_NAME': 'Advance',
                    'REQUESTED_DATE': 'Request Dt(AD)',
                    'REQUESTED_DATE_N': 'Request Dt(BS)',
                    'ADVANCE_DATE': 'Advance Dt(AD)',
                    'ADVANCE_DATE_N': 'Advance Dt(BS)',
                    'REQUESTED_AMOUNT': 'Request Amt',
                    'TERMS': 'Terms',
                    'STATUS': 'Status',
                    'REASON': 'Reason',
                    'RECOMMENDER_NAME': 'Recommender',
                    'APPROVER_NAME': 'Approver',
                    'RECOMMENDED_REMARKS': 'Reccommended Remarks',
                    'RECOMMENDED_DATE': 'Recommended Dt',
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
                        {value: "Advance Name"},
                        {value: "Applied Date(AD)"},
                        {value: "Applied Date(BS)"},
                        {value: "Advance Date(AD)"},
                        {value: "Advance Date(BS)"},
                        {value: "Requested Amount"},
                        {value: "Terms(in month)"},
                        {value: "Status"},
                        {value: "Reason"},
                        {value: "Recommender"},
                        {value: "Approver"},
                        {value: "Remarks By Recommender"},
                        {value: "Recommended Date"},
                        {value: "Remarks By Approver"},
                        {value: "Approved Date"},
                    ]
                }];
            var dataSource = $("#advanceTable").data("kendoGrid").dataSource;
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
                        {value: dataItem.ADVANCE_NAME},
                        {value: dataItem.REQUESTED_DATE},
                        {value: dataItem.REQUESTED_DATE_N},
                        {value: dataItem.ADVANCE_DATE},
                        {value: dataItem.ADVANCE_DATE_N},
                        {value: dataItem.REQUESTED_AMOUNT},
                        {value: dataItem.TERMS},
                        {value: dataItem.STATUS},
                        {value: dataItem.REASON},
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
                        title: "Advance Request",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "AdvanceRequestList.xlsx"});
        }
        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);