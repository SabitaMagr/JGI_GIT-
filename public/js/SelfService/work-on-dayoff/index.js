(function ($, app) {
    'use strict';
    $(document).ready(function () {

        console.log(document.workOnDayoffRequestList);
        $("#workOnDayoffTbl").kendoGrid({
            excel: {
                fileName: "WorkOnDayoffRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.workOnDayoffRequestList,
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
                {title: "Applied Date",
                    columns: [{
                            field: "REQUESTED_DATE",
                            title: "English",
                            template: "<span>#: (REQUESTED_DATE == null) ? '-' : REQUESTED_DATE #</span>"},
                        {field: "REQUESTED_DATE_N",
                            title: "Nepali",
                            template: "<span>#: (REQUESTED_DATE_N == null) ? '-' : REQUESTED_DATE_N #</span>"
                        }]},
                        {title: "From Date",
                            columns: [{
                                    field: "FROM_DATE",
                                    title: "English",
                                    template: "<span>#: (FROM_DATE == null) ? '-' : FROM_DATE #</span>"},
                                {field: "FROM_DATE_N",
                                    title: "Nepali",
                                    template: "<span>#: (FROM_DATE_N == null) ? '-' : FROM_DATE_N #</span>"
                                }]},
                {title: "To Date",
                columns: [{
                  field: "TO_DATE",
                  title: "English",
                  template: "<span>#: (TO_DATE == null) ? '-' : TO_DATE #</span>"},
                  {field: "TO_DATE_N",
                   title: "Nepali",
                   template: "<span>#: (TO_DATE_N == null) ? '-' : TO_DATE_N #</span>"     
                }]},
                {field: "DURATION", title: "Duration"},
                {field: "STATUS", title: "Status"},
                {field: ["ID", "ALLOW_TO_EDIT"], title: "Action", template: `<span><a class="btn-edit" href="` + document.viewLink + `/#: ID #" style="height:17px;" title="view detail">
                            <i class="fa fa-search-plus"></i>
                            </a>
                            #if(ALLOW_TO_EDIT == 1){#       
                            <a class="confirmation btn-delete" href="` + document.deleteLink + `/#: ID #" id="bs_#:ID #" style="height:17px;">
                            <i class="fa fa-trash-o"></i>
                            </a> #}#
                            </span>`}
            ]
        });

        app.searchTable('workOnDayoffTbl', ['REQUESTED_DATE', 'REQUESTED_DATE_N', 'FROM_DATE', 'FROM_DATE_N', 'TO_DATE', 'TO_DATE_N', 'DURATION', 'STATUS']);

        app.pdfExport(
                'workOnDayoffTbl',
                {
                    'REQUESTED_DATE': 'Requested Date(AD)',
                    'REQUESTED_DATE_N': 'Requested Date(BS)',
                    'FROM_DATE': 'From Date(AD)',
                    'FROM_DATE_N': 'From Date(BS)',
                    'TO_DATE': 'To Date(AD)',
                    'TO_DATE_N': 'To Date(BS)',
                    'DURATION': 'Duration',
                    'STATUS': 'Status',
                    'REMARKS': 'Remarks',
                    'RECOMMENDER_NAME': 'Recommender',
                    'APPROVER_NAME': 'Approver',
                    'RECOMMENDED_REMARKS': 'Recommended Remarks',
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
                        {value: "Applied Date(AD)"},
                        {value: "Applied Date(BS)"},
                        {value: "From Date(AD)"},
                        {value: "From Date(BS)"},
                        {value: "To Date(AD)"},
                        {value: "To Date(BS)"},
                        {value: "Duration"},
                        {value: "Status"},
                        {value: "Remarks"},
                        {value: "Recommender"},
                        {value: "Approver"},
                        {value: "Remarks By Recommender"},
                        {value: "Recommended Date"},
                        {value: "Remarks By Approver"},
                        {value: "Approved Date"}
                    ]
                }];
            var dataSource = $("#workOnDayoffTbl").data("kendoGrid").dataSource;
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
                        {value: dataItem.REQUESTED_DATE},
                        {value: dataItem.REQUESTED_DATE_N},
                        {value: dataItem.FROM_DATE},
                        {value: dataItem.FROM_DATE_N},
                        {value: dataItem.TO_DATE},
                        {value: dataItem.TO_DATE_N},
                        {value: dataItem.DURATION},
                        {value: dataItem.STATUS},
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
                        title: "Work on Dayoff Request",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "WorkOnDayoffRequestList.xlsx"});
        }
        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);