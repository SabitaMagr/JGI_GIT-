/**
 * Created by root on 11/9/16.
 */

angular.module('hris', [])
        .controller('holidayListController', function ($scope, $http) {
            $scope.holidayList = [];
            $scope.view = function () {
                var startDate = angular.element($("#startDate1")).val();
                var endDate = angular.element($("#endDate1")).val();
                var branchId = angular.element($("#branchId")).val();
                var genderId = angular.element($("#genderId")).val();

                window.app.pullDataById(document.url, {
                    action: 'pullHolidayList',
                    data: {
                        'fromDate': startDate,
                        'toDate': endDate,
                        'branchId': branchId,
                        'genderId': genderId
                    }
                }).then(function (success) {
                    $scope.$apply(function () {
                        console.log(success.data);
                        //$scope.holidayList = success.data;
                        $scope.initializekendoGrid(success.data);
                    });
                }, function (failure) {
                    console.log(failure);
                });
            }
            $scope.initializekendoGrid = function (holidayList) {
                $("#holidayTable").kendoGrid({
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
                        {field: "HOLIDAY_CODE", title: "Holiday Code",width:130},
                        {field: "HOLIDAY_ENAME", title: "Holiday Name",width:150},
                        {field: "START_DATE", title: "From Date",width:130},
                        {field: "END_DATE", title: "To Date",width:130},
                        {field: "GENDER_NAME", title: "Gender",width:100},
                        {field: "BRANCHES", title: "Branch",width:200},
                        {field: "HALFDAY", title: "Half Day",width:100},
                        {title: "Action",width:100}
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
            };
        });