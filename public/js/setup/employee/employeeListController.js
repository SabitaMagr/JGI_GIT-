//angular.module('hris', ["kendo.directives"])
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller('employeeListController', function ($scope, $http) {
//            $scope.gridData = new kendo.data.ObservableArray([
//            ]);
//            $scope.gridColumns = [
//                {field: "employeeCode", title: "Employee Code"},
//                {field: "firstName", title: "Name"},
//                {field: "birthDate", title: "Birth Date"},
//                {field: "mobileNo", title: "Mobile No"},
//                {field: "emailOfficial", title: "Email Official"},
//                {title: "Action"}
//            ];
//            $scope.kendoGridOptions = {
//                height: 550,
//                scrollable: true,
//                sortable: true,
//                filterable: true,
//                rowTemplate: kendo.template($("#rowTemplate").html()),
//                pageable: {
//                    input: true,
//                    numeric: false
//                },
//            };
            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId')).val();

                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullEmployeeListForEmployeeTable',
                    data: {
                        'employeeId': employeeId,
                        'branchId': branchId,
                        'departmentId': departmentId,
                        'designationId': designationId,
                        'positionId': positionId,
                        'serviceTypeId': serviceTypeId,
                        'serviceEventTypeId': serviceEventTypeId
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    console.log("pullEmployeeList", success.data);
                    $scope.initializekendoGrid(success.data);
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            };

            $scope.initializekendoGrid = function (employees) {
                $("#employeeTable").kendoGrid({
                    excel: {
                        fileName: "EmployeeList.xlsx",
                        filterable: true,
                        allPages: true
                    },
                    dataSource: {
                        data: employees,
                        pageSize: 20,
                    },
                    height: 450,
                    scrollable: true,
                    sortable: true,
                    filterable: true,
                    dataBound: gridDataBound,
                    pageable: {
                        input: true,
                        numeric: false
                    },
                    rowTemplate: kendo.template($("#rowTemplate").html()),
                    columns: [
                        {field: "EMPLOYEE_CODE", title: "Employee Code", width: 130},
                        {field: "FIRST_NAME", title: "Name", width: 220},
                        {field: "BIRTH_DATE", title: "Birth Date", width: 120},
                        {field: "MOBILE_NO", title: "Mobile No", width: 130},
                        {field: "EMAIL_ADDRESS", title: "Email Official", width: 200},
                        {title: "Action", width: 120}
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

//                $("#export").click(function (e) {
//                    var grid = $("#employeeTable").data("kendoGrid");
//                    grid.saveAsExcel();
//                });

                $("#export").click(function (e) {
                    var rows = [{
                            cells: [
                                {value: "Employee Code"},
                                {value: "Employee Name"},
                                {value: "Name in Nepali"},
                                {value: "Gender"},
                                {value: "Birth Date"},
                                {value: "Coutry"},
                                {value: "Religion"},
                                {value: "Companies"},
                                {value: "Blood Group"},
                                {value: "Mobile No"},
                                {value: "Telephone No"},
                                {value: "Social Activity"},
                                {value: "Extension Number"},
                                {value: "Email Official"},
                                {value: "Email Personal"},
                                {value: "Social Network"},
                                {value: "Permanent House No."},
                                {value: "Permanent Ward No."},
                                {value: "Permanent Street Address"},
                                {value: "Permanent Zone"},
                                {value: "Permanent District"},
                                {value: "Permanent VDC"},
                                {value: "Temporary House No."},
                                {value: "Temporary Ward No."},
                                {value: "Temporary Street Address"},
                                {value: "Temporary Zone"},
                                {value: "Temporary District"},
                                {value: "Temporary VDC"},
                                {value: "Emergency Contact Name"},
                                {value: "Emergency Contact Member Relationship"},
                                {value: "Emergency Address"},
                                {value: "Emergency Phone No."},
                                {value: "Father Name"},
                                {value: "Father Occupation"},
                                {value: "Grand Father Name"},
                                {value: "Marital Status"},
                                {value: "Spouse Name"},
                                {value: "Spouse Occupation"},
                                {value: "Mother Name"},
                                {value: "Mother Occupation"},
                                {value: "Grand Mother Name"},
                                {value: "Spouse Birth Date"},
                                {value: "Wedding Anniversary"},
                                {value: "ID Card No."},
                                {value: "ID Lb Rf."},
                                {value: "ID Bar Code"},
                                {value: "Provident Fund No."},
                                {value: "Driving License No."},
                                {value: "Driving License Expiry"},
                                {value: "Driving License Type"},
                                {value: "Passport No."},
                                {value: "Citizenship No."},
                                {value: "Citizenship Issue Place"},
                                {value: "Thumb ID"},
                                {value: "Pan No."},
                                {value: "Account ID"},
                                {value: "CIT No."},
                                {value: "Citizenship Issue Date"},
                                {value: "Passport Expiry"},
                                {value: "Join Date"},
                                {value: "Salary"},
                                {value: "Salary PF"},
                                {value: "Service Type Name"},
                                {value: "Service Event Type Name"},
                                {value: "Position Name"},
                                {value: "Designation Name"},
                                {value: "Department Name"},
                                {value: "Branch Name"},
                            ]
                        }];
                    var dataSource = $("#employeeTable").data("kendoGrid").dataSource;
                    var filteredDataSource = new kendo.data.DataSource({
                        data: dataSource.data(),
                        filter: dataSource.filter()
                    });

                    filteredDataSource.read();
                    var data = filteredDataSource.view();

                    for (var i = 0; i < data.length; i++) {
                        var dataItem = data[i];
                        if (dataItem.MIDDLE_NAME != null) {
                            var MIDDLE_NAME = " " + dataItem.MIDDLE_NAME + " ";
                        } else {
                            var MIDDLE_NAME = " ";
                        }
                        var employeeName = dataItem.FIRST_NAME + MIDDLE_NAME + dataItem.LAST_NAME;
                        rows.push({
                            cells: [
                                {value: dataItem.EMPLOYEE_CODE},
                                {value: employeeName},
                                {value: dataItem.NAME_NEPALI},
                                {value: dataItem.GENDER_NAME},
                                {value: dataItem.BIRTH_DATE},
                                {value: dataItem.COUNTRY_NAME},
                                {value: dataItem.RELIGION_NAME},
                                {value: dataItem.COMPANY_NAME},
                                {value: dataItem.BLOOD_GROUP_NAME},
                                {value: dataItem.MOBILE_NO},
                                {value: dataItem.TELEPHONE_NO},
                                {value: dataItem.SOCIAL_ACTIVITY},
                                {value: dataItem.EXTENSION_NO},
                                {value: dataItem.EMAIL_OFFICIAL},
                                {value: dataItem.EMAIL_PERSONAL},
                                {value: dataItem.SOCIAL_NETWORK},
                                {value: dataItem.ADDR_PERM_HOUSE_NO},
                                {value: dataItem.ADDR_PERM_WARD_NO},
                                {value: dataItem.ADDR_PERM_STREET_ADDRESS},
                                {value: dataItem.ADDR_PERM_ZONE_NAME},
                                {value: dataItem.ADDR_PERM_DISTRICT_NAME},
                                {value: dataItem.VDC_MUNICIPALITY_NAME},
                                {value: dataItem.ADDR_TEMP_HOUSE_NO},
                                {value: dataItem.ADDR_TEMP_WARD_NO},
                                {value: dataItem.ADDR_TEMP_STREET_ADDRESS},
                                {value: dataItem.ADDR_TEMP_ZONE_NAME},
                                {value: dataItem.ADDR_TEMP_DISTRICT_NAME},
                                {value: dataItem.VDC_MUNICIPALITY_NAME_TEMP},
                                {value: dataItem.EMRG_CONTACT_NAME},
                                {value: dataItem.EMERG_CONTACT_RELATIONSHIP},
                                {value: dataItem.EMERG_CONTACT_ADDRESS},
                                {value: dataItem.EMERG_CONTACT_NO},
                                {value: dataItem.FAM_FATHER_NAME},
                                {value: dataItem.FAM_FATHER_OCCUPATION},
                                {value: dataItem.FAM_GRAND_FATHER_NAME},
                                {value: dataItem.MARITAL_STATUS},
                                {value: dataItem.FAM_SPOUSE_NAME},
                                {value: dataItem.FAM_SPOUSE_OCCUPATION},
                                {value: dataItem.FAM_MOTHER_NAME},
                                {value: dataItem.FAM_MOTHER_OCCUPATION},
                                {value: dataItem.FAM_GRAND_MOTHER_NAME},
                                {value: dataItem.FAM_SPOUSE_BIRTH_DATE},
                                {value: dataItem.FAM_SPOUSE_WEDDING_ANNIVERSARY},
                                {value: dataItem.ID_CARD_NO},
                                {value: dataItem.ID_LBRF},
                                {value: dataItem.ID_BAR_CODE},
                                {value: dataItem.ID_PROVIDENT_FUND_NO},
                                {value: dataItem.ID_DRIVING_LICENCE_NO},
                                {value: dataItem.ID_DRIVING_LICENCE_EXPIRY},
                                {value: dataItem.ID_DRIVING_LICENCE_TYPE},
                                {value: dataItem.ID_PASSPORT_NO},
                                {value: dataItem.ID_CITIZENSHIP_NO},
                                {value: dataItem.ID_CIT_ISSUE_PLACE_NAME},
                                {value: dataItem.ID_THUMB_ID},
                                {value: dataItem.ID_PAN_NO},
                                {value: dataItem.ID_ACCOUNT_NO},
                                {value: dataItem.ID_RETIREMENT_NO},
                                {value: dataItem.ID_CITIZENSHIP_ISSUE_DATE},
                                {value: dataItem.ID_PASSPORT_EXPIRY},
                                {value: dataItem.JOIN_DATE},
                                {value: dataItem.SALARY},
                                {value: dataItem.SALARY_PF},
                                {value: dataItem.SERVICE_TYPE_NAME},
                                {value: dataItem.SERVICE_EVENT_TYPE_NAME},
                                {value: dataItem.POSITION_NAME},
                                {value: dataItem.DESIGNATION_TITLE},
                                {value: dataItem.DEPARTMENT_NAME},
                                {value: dataItem.BRANCH_NAME}
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
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true}
                                ],
                                title: "Employee",
                                rows: rows
                            }
                        ]
                    });
                    kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "EmployeeList.xlsx"});
                }

                window.app.UIConfirmations();
            };
//            $scope.initializekendoGrid([]);

        });

