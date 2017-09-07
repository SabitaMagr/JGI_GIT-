(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller("travelStatusListController", function ($scope, $http) {
            var $tableContainer = $("#travelRequestStatusTable");
            var displayKendoFirstTime = true;
            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var companyId = angular.element(document.getElementById('companyId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var travelRequestStatusId = angular.element(document.getElementById('travelRequestStatusId')).val();
                var fromDate = angular.element(document.getElementById('fromDate')).val();
                var toDate = angular.element(document.getElementById('toDate')).val();
                var employeeTypeId = angular.element(document.getElementById('employeeTypeId')).val();
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullTravelRequestStatusList',
                    data: {
                        'employeeId': employeeId,
                        'companyId': companyId,
                        'branchId': branchId,
                        'departmentId': departmentId,
                        'designationId': designationId,
                        'positionId': positionId,
                        'serviceTypeId': serviceTypeId,
                        'serviceEventTypeId': serviceEventTypeId,
                        'travelRequestStatusId': travelRequestStatusId,
                        'fromDate': fromDate,
                        'toDate': toDate,
                        'employeeTypeId': employeeTypeId
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    console.log(success.data);
                    if (displayKendoFirstTime) {
                        $scope.initializekendoGrid();
                        displayKendoFirstTime = false;
                    }
                    var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
                    var grid = $('#travelRequestStatusTable').data("kendoGrid");
                    dataSource.read();
                    grid.setDataSource(dataSource);
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            }
            $scope.initializekendoGrid = function () {
                $("#travelRequestStatusTable").kendoGrid({
                    excel: {
                        fileName: "TravelRequestList.xlsx",
                        filterable: true,
                        allPages: true
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
//                    rowTemplate: kendo.template($("#rowTemplate").html()),
                    columns: [
                        {field: "FULL_NAME", title: "Employee"},
//                        {field: "FROM_DATE", title: "From Date", width: 120},
                         {title: "From Date",
                    columns: [{
                            field: "FROM_DATE",
                            title: "AD",
                            template: "<span>#: (FROM_DATE == null) ? '-' : FROM_DATE #</span>"},
                        {field: "FROM_DATE_N",
                            title: "BS",
                            template: "<span>#: (FROM_DATE_N == null) ? '-' : FROM_DATE_N #</span>"}]},
                {title: "To Date",
                    columns: [{
                            field: "TO_DATE",
                            title: "AD",
                            template: "<span>#: (TO_DATE == null) ? '-' : TO_DATE #</span>"},
                        {field: "TO_DATE_N",
                            title: "BS",
                            template: "<span>#: (TO_DATE_N == null) ? '-' : TO_DATE_N #</span>"}]},
                {title: "Requested Date",
                    columns: [{
                            field: "REQUESTED_DATE",
                            title: "AD",
                            template: "<span>#: (REQUESTED_DATE == null) ? '-' : REQUESTED_DATE #</span>"},
                        {field: "REQUESTED_DATE_N",
                            title: "BS",
                            template: "<span>#: (REQUESTED_DATE_N == null) ? '-' : REQUESTED_DATE_N #</span>"}]},
//                        {field: "TO_DATE", title: "To Date", width: 100},
//                        {field: "REQUESTED_DATE", title: "Requested Date", width: 140},
                        {field: "DESTINATION", title: "Destination"},
                        {field: "REQUESTED_AMOUNT", title: "Requested Amt."},
                        {field: "REQUESTED_TYPE", title: "Request For"},
                        {field: "STATUS", title: "Status"},
                        {field: ["TRAVEL_ID","REQUESTED_TYPE"], title: "Action",
                  template: `<span>
                   #if(REQUESTED_TYPE=='Expense'){ #
        <a class="btn-edit"
        href="`+document.expenseDetailLink+`"/#: TRAVEL_ID #" style="height:17px;" title="view detail">
        <i class="fa fa-search-plus"></i>
        </a> #} else{ # <a class="btn-edit"
        href="`+document.viewLink+`/#: TRAVEL_ID #" style="height:17px;" title="view detail">
        <i class="fa fa-search-plus"></i>
        </a>
        # }# </span>`}
                    ]
                });

                app.searchTable('travelRequestStatusTable', ['FULL_NAME', 'FROM_DATE', 'TO_DATE', 'REQUESTED_DATE','FROM_DATE_N', 'TO_DATE_N', 'REQUESTED_DATE_N', 'DESTINATION', 'REQUESTED_AMOUNT', 'REQUESTED_TYPE', 'STATUS']);

                app.pdfExport(
                        'travelRequestStatusTable',
                        {
                            'FULL_NAME': 'Name',
                            'FROM_DATE': 'From Date(AD)',
                            'FROM_DATE_N': 'From Date(BS)',
                            'TO_DATE': 'To Date(AD)',
                            'TO_DATE_N': 'To Date(BS)',
                            'REQUESTED_DATE': 'Request Date(AD)',
                            'REQUESTED_DATE_N': 'Request Date(BS)',
                            'REQUESTED_AMOUNT': 'Request Amt',
                            'REQUESTED_TYPE': 'Request Type',
                            'DESTINATION': 'Destination',
                            'PURPOSE': 'Purpose',
                            'RECOMMENDER_NAME': 'Recommender',
                            'APPROVER_NAME': 'Approver',
                            'STATUS': 'Status',
                            'REMARKS': 'Remarks',
                            'RECOMMENDED_REMARKS': 'Recommender Remarks',
                            'RECOMMENDED_DATE': 'Recommended Date',
                            'APPROVED_REMARKS': 'Approver Remarks',
                            'APPROVED_DATE': 'Approved Date',
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
                                {value: "Employee Name"},
                                {value: "From Date(AD)"},
                                {value: "From Date(BS)"},
                                {value: "To Date(AD)"},
                                {value: "To Date(BS)"},
                                {value: "Requested Date(AD)"},
                                {value: "Requested Date(BS)"},
                                {value: "Destination"},
                                {value: "Purpose"},
                                {value: "Request For"},
                                {value: "Requested Amount"},
                                {value: "Recommender"},
                                {value: "Approver"},
                                {value: "Status"},
                                {value: "Remarks By Employee"},
                                {value: "Remarks By Recommender"},
                                {value: "Recommended Date"},
                                {value: "Remarks By Approver"},
                                {value: "Approved Date"}
                            ]
                        }];
                    var dataSource = $("#travelRequestStatusTable").data("kendoGrid").dataSource;
                    var filteredDataSource = new kendo.data.DataSource({
                        data: dataSource.data(),
                        filter: dataSource.filter()
                    });

                    filteredDataSource.read();
                    var data = filteredDataSource.view();

                    for (var i = 0; i < data.length; i++) {
                        var dataItem = data[i];
                        var mn1 = dataItem.MN1 != null ? " " + dataItem.MN1 + " " : " ";
                        var mn2 = dataItem.MN2 != null ? " " + dataItem.MN2 + " " : " ";
                        rows.push({
                            cells: [
                                {value: dataItem.FULL_NAME},
                                {value: dataItem.FROM_DATE},
                                {value: dataItem.FROM_DATE_N},
                                {value: dataItem.TO_DATE},
                                {value: dataItem.TO_DATE_N},
                                {value: dataItem.REQUESTED_DATE},
                                {value: dataItem.REQUESTED_DATE_N},
                                {value: dataItem.DESTINATION},
                                {value: dataItem.PURPOSE},
                                {value: dataItem.REQUESTED_TYPE},
                                {value: dataItem.REQUESTED_AMOUNT},
                                {value: dataItem.RECOMMENDER_NAME},
                                {value: dataItem.APPROVER_NAME},
                                {value: dataItem.STATUS},
                                {value: dataItem.REMARKS},
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

                window.app.UIConfirmations();
            };
        });