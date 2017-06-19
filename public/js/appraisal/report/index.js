(function ($) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali("nepaliFromDate1", "fromDate1", "nepaliToDate1", "toDate1", null, true);
    });
})(window.jQuery);

angular.module("hris", [])
        .controller("appraisalList", function ($scope, $http, $window) {
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
                var appraisalId = angular.element(document.getElementById('appraisalId')).val();
                var appraisalStageId = angular.element(document.getElementById('appraisalStageId')).val();
                var fromDate = angular.element(document.getElementById('fromDate1')).val();
                var toDate = angular.element(document.getElementById('toDate1')).val();
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.restfulUrl, {
                    action: 'pullAppraisalViewList',
                    data: {
                        'employeeId': employeeId,
                        'companyId': companyId,
                        'branchId': branchId,
                        'departmentId': departmentId,
                        'designationId': designationId,
                        'positionId': positionId,
                        'serviceTypeId': serviceTypeId,
                        'serviceEventTypeId': serviceEventTypeId,
                        'appraisalId': appraisalId,
                        'appraisalStageId': appraisalStageId,
                        'fromDate': fromDate,
                        'toDate': toDate
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    if (displayKendoFirstTime) {
                        $scope.initializekendoGrid();
                        displayKendoFirstTime = false;
                    }
                    var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
                    var grid = $('#appraisalListTable').data("kendoGrid");
                    dataSource.read();
                    grid.setDataSource(dataSource);
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            }
            
            $scope.initializekendoGrid = function () {
                $("#appraisalListTable").kendoGrid({
                    excel: {
                        fileName: "AppraisalViewList.xlsx",
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
                    rowTemplate: kendo.template($("#rowTemplate").html()),
                    columns: [
                        {field: "FIRST_NAME", title: "Employee", width: 150},
                        {field: "APPRAISAL_EDESC", title: "Appraisal", width: 120},
                        {field: "APPRAISAL_TYPE_EDESC", title: "Appraisal Type", width: 150},
                        {field: "STAGE_EDESC", title: "Current Stage", width: 140},
                        {field: "START_DATE", title: "Start Date", width: 120},
                        {field: "END_DATE", title: "End Date", width: 100},
                        {field: "APPRAISER_OVERALL_RATING", title: "Rating", width: 110},
                        {field: "APPRAISER_NAME", title: "Appraiser Name", width: 150},
                        {field: "REVIEWER_NAME", title: "Reviewer Name", width: 150},
                        {title: "Action", width: 90}
                    ]
                });

                app.searchTable('appraisalListTable', ['FIRST_NAME', 'APPRAISAL_EDESC', 'APPRAISAL_TYPE_EDESC', 'STAGE_EDESC', 'START_DATE', 'END_DATE', 'APPRAISER_NAME', 'REVIEWER_NAME']);

                app.pdfExport(
                        'appraisalListTable',
                        {
                            'FIRST_NAME': 'Name',
                            'MIDDLE_NAME': 'MiddleName',
                            'LAST_NAME': 'LastName',
                            'APPRAISAL_EDESC': 'Appraisal',
                            'APPRAISAL_TYPE_EDESC':'Appraisal Type',
                            'STAGE_EDESC': 'Current Stage',
                            'START_DATE': 'Start Date',
                            'END_DATE': 'EndDate',
                            'APPRAISER_OVERALL_RATING':'Rating',
                            'APPRAISER_NAME': 'Appraiser Name',
                            'ALT_APPRAISER_NAME': 'Alt. Appraiser Name',
                            'REVIEWER_NAME': 'Reviewer Name',
                            'ALT_REVIEWER_NAME': 'Alt. Reviewer Name'
                            
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
                                {value: "Appraisal Name"},
                                {value: "Appraisal Type Name"},
                                {value: "Current Stage"},
                                {value: "From Date"},
                                {value: "To Date"},
                                {value: "Rating"},
                                {value: "Appraiser Name"},
                                {value: "Alt. Appraiser Name"},
                                {value: "Reviewer Name"},
                                {value: "Alt. Reviewer Name"},
                            ]
                        }];
                    var dataSource = $("#appraisalListTable").data("kendoGrid").dataSource;
                    var filteredDataSource = new kendo.data.DataSource({
                        data: dataSource.data(),
                        filter: dataSource.filter()
                    });

                    filteredDataSource.read();
                    var data = filteredDataSource.view();

                    for (var i = 0; i < data.length; i++) {
                        var dataItem = data[i];
                        var middleName = dataItem.MIDDLE_NAME != null ? " " + dataItem.MIDDLE_NAME + " " : " ";
                        var mn1 = dataItem.MN1 != null ? " " + dataItem.MN1 + " " : " ";
                        var mn2 = dataItem.MN2 != null ? " " + dataItem.MN2 + " " : " ";
                        rows.push({
                            cells: [
                                {value: dataItem.FIRST_NAME + middleName + dataItem.LAST_NAME},
                                {value: dataItem.APPRAISAL_EDESC},
                                {value: dataItem.APPRAISAL_TYPE_EDESC},
                                {value: dataItem.STAGE_EDESC},
                                {value: dataItem.START_DATE},
                                {value: dataItem.END_DATE},
                                {value: dataItem.APPRAISER_OVERALL_RATING},
                                {value: dataItem.APPRAISER_NAME},
                                {value: dataItem.ALT_APPRAISER_NAME},
                                {value: dataItem.REVIEWER_NAME},
                                {value: dataItem.ALT_REVIEWER_NAME},
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
                                ],
                                title: "Appraisal List",
                                rows: rows
                            }
                        ]
                    });
                    kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "AppraisalViewList.xlsx"});
                }
            };
        });