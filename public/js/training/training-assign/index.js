(function ($, app) {
    'use strict';
    $(document).ready(function () {
        window.app.UIConfirmations();
        $('select').select2();
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller('trainingAssignController', function ($scope, $http, $window) {
            $scope.employeeList = [];
            $scope.all = false;
            $scope.assignShowHide = false;
            var l;
            var $tableContainer = $("#trainingAssignListTable");

            $scope.checkAll = function (item) {
                for (var i = 0; i < $scope.employeeList.length; i++) {
                    $scope.employeeList[i].checked = item;
                }
                $scope.assignShowHide = item && ($scope.employeeList.length > 0);
                if ($scope.assignShowHide) {
                    l = Ladda.create(document.querySelector('#assignBtn'));
                }
            };

            $scope.checkUnit = function (item) {
                for (var i = 0; i < $scope.employeeList.length; i++) {
                    if ($scope.employeeList[i].checked) {
                        $scope.assignShowHide = true;
                        l = Ladda.create(document.querySelector('#assignBtn'));
                        break;
                    }
                    $scope.assignShowHide = false;
                }
            };

            $scope.view = function () {
                var trainingId = angular.element(document.getElementById('trainingId')).val();
                var companyId = angular.element(document.getElementById('companyId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var employeeTypeId = angular.element(document.getElementById('employeeTypeId')).val();

                trainingId = (typeof trainingId === 'undefined' || trainingId === null || trainingId === '') ? -1 : trainingId;

                $scope.all = false;
                $scope.assignShowHide = false;

                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullEmployeeForTrainingAssign',
                    data: {
                        branchId: branchId,
                        departmentId: departmentId,
                        designationId: designationId,
                        employeeId: employeeId,
                        positionId: positionId,
                        serviceTypeId: serviceTypeId,
                        trainingId: trainingId,
                        companyId: companyId,
                        employeeTypeId: employeeTypeId
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    console.log("Employee list for assign", success);
                    $scope.$apply(function () {
                        $scope.employeeList = success.data;
                        for (var i = 0; i < $scope.employeeList.length; i++) {
                            $scope.employeeList[i].checked = false;
                        }

                    });
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log("Employee Get All", failure);
                });
            };

            $scope.assign = function () {
                var trainingId = angular.element(document.getElementById('trainingId')).val();
                if (typeof trainingId === 'undefined' || trainingId === null || trainingId == '' || trainingId == -1) {
                    window.toastr.error("No Training Selected.", "Alert");
                    return;
                }

                var promises = [];
                for (var index in $scope.employeeList) {
                    if ($scope.employeeList[index].checked) {
                        if (trainingId == $scope.employeeList[index].TRAINING_ID) {
                            console.log($scope.employeeList[index].EMPLOYEE_ID + 'is already assigned');
                            continue;
                        }
                        promises.push(window.app.pullDataById(document.url, {
                            action: 'assignEmployeeTraining',
                            data: {
                                employeeId: $scope.employeeList[index].EMPLOYEE_ID,
                                trainingId: trainingId,
                                oldTrainingId: $scope.employeeList[index].TRAINING_ID
                            }
                        }));
                    }
                }

                if (promises.length > 0) {
                    l.start();
                    l.setProgress(0.5);
                } else {
                    window.toastr.success("Already Assigned", "Notification");
                }

                Promise.all(promises).then(function (response) {
                    l.stop();
                    var assignedStatus = false;
                    for (var i in response) {
                        if (response[i].success) {
                            assignedStatus = true;
                            window.toastr.success(response[i].message, "Notification");
                        } else {
                            window.toastr.error(response[i].message, "Error");
                        }
                    }
                    if (assignedStatus) {
                        $scope.$apply(function () {
                            $scope.view();
                        });
                    }

                }, function (error) {
                    l.stop();
                    console.log(error);
                });
            };

            $scope.cancel = function () {
                l.start();
                l.setProgress(0.5);
                var trainingId = angular.element(document.getElementById('trainingId')).val();

                var promises = [];
                for (var index in $scope.employeeList) {
                    if ($scope.employeeList[index].checked) {
                        promises.push(window.app.pullDataById(document.url, {
                            action: 'cancelEmployeeTraining',
                            data: {
                                employeeId: $scope.employeeList[index].EMPLOYEE_ID,
                                trainingId: trainingId,
                            }
                        }));
                    }
                }
                Promise.all(promises).then(function (success) {
                    console.log(success);
                    l.stop();
                    $scope.$apply(function () {
                        $scope.view();
                    });
                    window.toastr.success("Training assign cancelled successfully!", "Notification");
                });
            };
            var employeeIdFromParam = window.location.href.substr(window.location.href.lastIndexOf('/') + 1);
            if (parseInt(employeeIdFromParam) > 0) {
                angular.element(document.getElementById('employeeId')).val(employeeIdFromParam).change();
                $scope.view();
            }
            var displayKendoFirstTime = true;
            $scope.viewTrainingAssignList = function () {
                var companyId = angular.element(document.getElementById('companyId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var trainingId = angular.element(document.getElementById('trainingId')).val();
                var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId')).val();
                var employeeTypeId = angular.element(document.getElementById('employeeTypeId')).val();
                App.blockUI({target: "#hris-page-content1"});
                window.app.pullDataById(document.url, {
                    action: 'pullTrainingAssignList',
                    data: {
                        branchId: branchId,
                        departmentId: departmentId,
                        designationId: designationId,
                        employeeId: employeeId,
                        positionId: positionId,
                        serviceTypeId: serviceTypeId,
                        trainingId: trainingId,
                        companyId: companyId,
                        serviceEventTypeId: serviceEventTypeId,
                        employeeTypeId: employeeTypeId
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content1");
                    console.log("Training Assign List", success);
                    $scope.$apply(function () {
                        App.unblockUI("#hris-page-content1");
                        if (displayKendoFirstTime) {
                            $scope.initializekendoGrid();
                            displayKendoFirstTime = false;
                        }
                        var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
                        var grid = $('#trainingAssignListTable').data("kendoGrid");
                        dataSource.read();
                        grid.setDataSource(dataSource);
                        window.app.UIConfirmations();
                    });
                }, function (failure) {
                    console.log("Employee Get All", failure);
                });
            };
            $scope.initializekendoGrid = function () {
                $("#trainingAssignListTable").kendoGrid({
                    excel: {
                        fileName: "TrainingAssignList.xlsx",
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
                        {field: "FULL_NAME", title: "Employee Name"},
                        {field: "TRAINING_NAME", title: "Training Name"},
//                        {field: "START_DATE", title: "Start Date", width: 80},
                        {title: "Start Date",
                            columns: [{
                                    field: "START_DATE",
                                    title: "AD",
                                    template: "<span>#: (START_DATE == null) ? '-' : START_DATE #</span>"},
                                {field: "START_DATE_N",
                                    title: "BS",
                                    template: "<span>#: (START_DATE_N == null) ? '-' : START_DATE_N #</span>"}]},
                        {title: "End Date",
                            columns: [{
                                    field: "END_DATE",
                                    title: "AD",
                                    template: "<span>#: (END_DATE == null) ? '-' : END_DATE #</span>"},
                                {field: "END_DATE_N",
                                    title: "BS",
                                    template: "<span>#: (END_DATE_N == null) ? '-' : END_DATE_N #</span>"}]},

//                        {field: "END_DATE", title: "End Date"},
                        {field: "DURATION", title: "Duration(in hour)"},
                        {field: "INSTITUTE_NAME", title: "Institute Name", template: "<span>#: (INSTITUTE_NAME == null) ? '-' : INSTITUTE_NAME #</span>"},
                        {field: "TRAINING_TYPE", title: "Training Type"},
                        {field: ["TRAINING_ID", "ALLOW_TO_EDIT"], title: "Action", template: `<span><a class="btn-edit"
        href="` + document.viewLink + `/#: EMPLOYEE_ID #/#: TRAINING_ID #" style="height:17px;" title="view detail">
        <i class="fa fa-search-plus"></i></a>
        </a>
        #if(ALLOW_TO_EDIT == 1){#       
        <a class="confirmation btn-delete"
        href="` + document.deleteLink + `/#: EMPLOYEE_ID #/#: TRAINING_ID #" id="bs_#:SN #" style="height:17px;">
        <i class="fa fa-trash-o"></i></a>
        </a>
        #}#

</span>`}
                    ]
                });

                app.searchTable('trainingAssignListTable', ['FULL_NAME', 'TRAINING_NAME', 'START_DATE', 'END_DATE', 'START_DATE_N', 'END_DATE_N', 'DURATION', 'INSTITUTE_NAME', 'TRAINING_TYPE']);

                app.pdfExport(
                        'trainingAssignListTable',
                        {
                            'FULL_NAME': 'Name',
                            'TRAINING_NAME': 'Training',
                            'START_DATE': 'Start Date(AD)',
                            'START_DATE_N': 'Start Date(BS)',
                            'END_DATE': 'End Date(AD)',
                            'END_DATE_N': 'End Date(BS)',
                            'DURATION': 'Duration',
                            'LOCATION': 'Location',
                            'INSTITUTE_NAME': 'Institute',
                            'TRAINING_TYPE': 'Training Type',
                            'REMARKS': 'Remarks',
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
                                {value: "Training Name"},
                                {value: "Start Date(AD)"},
                                {value: "Start Date(BS)"},
                                {value: "End Date(AD)"},
                                {value: "End Date(BS)"},
                                {value: "Duration"},
                                {value: "Institute Name"},
                                {value: "Location Detail"},
                                {value: "Instructor Name"},
                                {value: "Training Type"},
                                {value: "Remarks"},
                            ]
                        }];
                    var dataSource = $("#trainingAssignListTable").data("kendoGrid").dataSource;
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
                                {value: dataItem.FULL_NAME},
                                {value: dataItem.TRAINING_NAME},
                                {value: dataItem.START_DATE},
                                {value: dataItem.START_DATE_N},
                                {value: dataItem.END_DATE},
                                {value: dataItem.END_DATE_N},
                                {value: dataItem.DURATION},
                                {value: dataItem.INSTITUTE_NAME},
                                {value: dataItem.LOCATION},
                                {value: dataItem.INSTRUCTOR_NAME},
                                {value: dataItem.TRAINING_TYPE},
                                {value: dataItem.REMARKS},
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
                                    {autoWidth: true}
                                ],
                                title: "Training Assign List",
                                rows: rows
                            }
                        ]
                    });
                    kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "TrainingAssignList.xlsx"});
                }
            }
        });