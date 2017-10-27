(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("#travelTable").kendoGrid({
            excel: {
                fileName: "TravelRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.travelRequestList,
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
                {title: "Start Date",
                    columns: [{
                            field: "FROM_DATE",
                            title: "English",
                            template: "<span>#: (FROM_DATE == null) ? '-' : FROM_DATE #</span>"},
                        {field: "FROM_DATE_N",
                            title: "Nepali",
                            template: "<span>#: (FROM_DATE_N == null) ? '-' : FROM_DATE_N #</span>"}]},
                {title: "To Date",
                    columns: [{
                            field: "TO_DATE",
                            title: "English",
                            template: "<span>#: (TO_DATE == null) ? '-' : TO_DATE #</span>"},
                        {field: "TO_DATE_N",
                            title: "Nepali",
                            template: "<span>#: (TO_DATE_N == null) ? '-' : TO_DATE_N #</span>"}]},
                {title: "Applied Date",
                    columns: [{
                            field: "REQUESTED_DATE",
                            title: "English",
                            template: "<span>#: (REQUESTED_DATE == null) ? '-' : REQUESTED_DATE #</span>"},
                        {field: "REQUESTED_DATE_N",
                            title: "Nepali",
                            template: "<span>#: (REQUESTED_DATE_N == null) ? '-' : REQUESTED_DATE_N #</span>"}]},
                {field: "DESTINATION", title: "Destination"},
                {field: "REQUESTED_AMOUNT", title: "Request Amt."},
                {field: "REQUESTED_TYPE", title: "Request For"},
                {field: "STATUS", title: "Status"},
                {field: ["TRAVEL_ID","REQUESTED_TYPE","ALLOW_TO_EDIT","ALLOW_TO_REQUEST_EX"], title: "Action",
                  template: `<span>
                   #if(REQUESTED_TYPE=='Expense'){ #
        <a class="btn-edit"
        href="`+document.viewExpenseLink+`"/#: TRAVEL_ID #" style="height:17px;" title="view detail">
        <i class="fa fa-search-plus"></i>
        </a> #} else{ # <a class="btn-edit"
        href="`+document.viewLink+`/#: TRAVEL_ID #" style="height:17px;" title="view detail">
        <i class="fa fa-search-plus"></i>
        </a>
        # }#
        #if(ALLOW_TO_EDIT == 1 && REQUESTED_TYPE=='Expense'){#       
        <a class="btn-edit"
        href="`+document.expenseRequestLink+`/#: TRAVEL_ID #" style="height:17px; margin-left: 6px;" title="update">
        <i class="fa fa-pencil-square-o"></i>
        </a>
        #}#
        #if(ALLOW_TO_EDIT == 1){#       
        <a class="confirmation btn-delete"
        href="`+document.deleteLink+`/#: TRAVEL_ID #" id="bs_#:TRAVEL_ID #" style="height:17px;">
        <i class="fa fa-trash-o"></i>
        </a>
        #}#
        # if(ALLOW_TO_REQUEST_EX==1){#
        <a class="btn-edit"
        href="`+document.expenseRequestLink+`/#: TRAVEL_ID #" style="height:17px; margin-left: 6px;" title="Expense Request">
        <i class="fa fa-send-o"></i>
        </a>
        #}#
</span>`}
            ]
        });

        app.searchTable('travelTable', ['FROM_DATE', 'FROM_DATE_N', 'TO_DATE', 'TO_DATE_N', 'REQUESTED_DATE', 'REQUESTED_DATE_N', 'DESTINATION', 'REQUESTED_AMOUNT', 'REQUESTED_TYPE', 'STATUS']);

        app.pdfExport(
                'travelTable',
                {
                    'FROM_DATE': 'From Date(AD)',
                    'FROM_DATE_N': 'From Date(BS)',
                    'TO_DATE': 'To Date(AD)',
                    'TO_DATE_N': 'To Date(BS)',
                    'REQUESTED_DATE': 'Request Date(AD)',
                    'REQUESTED_DATE_N': 'Request Date(BS)',
                    'DESTINATION': 'Destination',
                    'REQUESTED_AMOUNT': 'Request Amt',
                    'REQUESTED_TYPE': 'Request Type',
                    'STATUS': 'Status',
                    'PURPOSE': 'Purpose',
                    'REMARKS': 'Remarks',
                    'RECOMMENDER_NAME': 'Recommender',
                    'APPROVER_NAME': 'Approver',
                    'RECOMMENDED_REMARKS': 'Recommended Remarks',
                    'RECOMMENDED_DATE': 'Recommended Date',
                    'APPROVED_REMARKS': 'Approved Remarks',
                    'APPROVED_DATE': 'Approved Date'
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
                        {value: "From Date(AD)"},
                        {value: "From Date(BS)"},
                        {value: "To Date(AD)"},
                        {value: "To Date(BS)"},
                        {value: "Applied Date(AD)"},
                        {value: "Applied Date(BS)"},
                        {value: "Destination"},
                        {value: "Requested Amount"},
                        {value: "Requested Type"},
                        {value: "Status"},
                        {value: "Purpose"},
                        {value: "Remarks"},
                        {value: "Recommender"},
                        {value: "Approver"},
                        {value: "Remarks By Recommender"},
                        {value: "Recommended Date"},
                        {value: "Remarks By Approver"},
                        {value: "Approved Date"}
                    ]
                }];
            var dataSource = $("#travelTable").data("kendoGrid").dataSource;
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
                        {value: dataItem.FROM_DATE},
                        {value: dataItem.FROM_DATE_N},
                        {value: dataItem.TO_DATE},
                        {value: dataItem.TO_DATE_N},
                        {value: dataItem.REQUESTED_DATE},
                        {value: dataItem.REQUESTED_DATE_N},
                        {value: dataItem.DESTINATION},
                        {value: dataItem.REQUESTED_AMOUNT},
                        {value: dataItem.REQUESTED_TYPE},
                        {value: dataItem.STATUS},
                        {value: dataItem.PURPOSE},
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
                        title: "Travel Request",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "TravelRequestList.xlsx"});
        }
    });
})(window.jQuery, window.app);


angular.module("hris", [])
        .controller("travelRequestList", function ($scope, $http, $window) {
            $scope.msg = $window.localStorage.getItem("msg");
            if ($window.localStorage.getItem("msg")) {
                window.toastr.success($scope.msg, "Notifications");
            }
            $window.localStorage.removeItem("msg");
        });