(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
//        app.startEndDatePicker('startDate', 'endDate');
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate1', 'nepaliEndDate1', 'endDate1');
    });
})(window.jQuery, window.app);


angular.module('hris', [])
        .controller('holidayListController', function ($scope, $http,$window) {
            $scope.holidayList = [];
            var $tableContainer = $("#holidayTable");
            $scope.view = function () {
                var startDate = angular.element($("#startDate1")).val();
                var endDate = angular.element($("#endDate1")).val();
                var branchId = angular.element($("#branchId")).val();
                var genderId = angular.element($("#genderId")).val();
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullHolidayList',
                    data: {
                        'fromDate': startDate,
                        'toDate': endDate,
                        'branchId': branchId,
                        'genderId': genderId
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    $scope.$apply(function () {
                        // console.log(success.data);
                        //$scope.holidayList = success.data;
                        $scope.initializekendoGrid(success.data);
                    });
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            }
            $scope.initializekendoGrid = function (holidayList) {
                $("#holidayTable").kendoGrid({
                    excel: {
                        fileName: "HolidayList.xlsx",
                        filterable: true,
                        allPages: true
                    },
                    dataSource: {
                        data: holidayList,
                        pageSize: 35
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
//                        {field: "HOLIDAY_CODE", title: "Holiday Code", width: 130},
                        {field: "HOLIDAY_ENAME", title: "Holiday", width: 150},
                        {field: "START_DATE", title: "From Date", width: 130},
                        {field: "END_DATE", title: "To Date", width: 130},
                        {field: "GENDER_NAME", title: "Gender", width: 100},
                        {field: "BRANCHES", title: "Branch", width: 200},
                        {field: "HALFDAY", title: "Half Day", width: 100},
                        {title: "Action", width: 100}
                    ]
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
//                                {value: "Holiday Code"},
                                {value: "Holiday Name"},
                                {value: "From Date"},
                                {value: "To Date"},
                                {value: "Gender"},
                                {value: "Branches"},
                                {value: "Half Day"},
                                {value: "Remarks"}
                            ]
                        }];
                    var dataSource = $("#holidayTable").data("kendoGrid").dataSource;
                    var filteredDataSource = new kendo.data.DataSource({
                        data: dataSource.data(),
                        filter: dataSource.filter()
                    });

                    filteredDataSource.read();
                    var data = filteredDataSource.view();

                    for (var i = 0; i < data.length; i++) {
                        var dataItem = data[i];
                        var branch = [];
                        for (var j = 0; j < dataItem.BRANCHES.length; j++) {
                            branch.push(dataItem.BRANCHES[j].BRANCH_NAME);
                        }
                        console.log(branch, "hellow branches");
                        var branch1 = branch.toString();
                        ;
                        console.log(branch1);
                        rows.push({
                            cells: [
//                                {value: dataItem.HOLIDAY_CODE},
                                {value: dataItem.HOLIDAY_ENAME},
                                {value: dataItem.START_DATE},
                                {value: dataItem.END_DATE},
                                {value: dataItem.GENDER_NAME},
                                {value: branch1},
                                {value: dataItem.HALFDAY},
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
//                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true}
                                ],
                                title: "Holiday List",
                                rows: rows
                            }
                        ]
                    });
                    kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "HolidayList.xlsx"});
                }
                window.app.UIConfirmations();
            };
            
            
            $scope.msg =  $window.localStorage.getItem("msg");
            if($window.localStorage.getItem("msg")){
                window.toastr.success($scope.msg, "Notifications");
            }
            $window.localStorage.removeItem("msg");
        });