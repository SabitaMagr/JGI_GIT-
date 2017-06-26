(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        $("#export").click(function (e) {
            app.errorMessage("No List to export data from.", "Alert");
        });

        app.searchTable('employeeTable', ['FULL_NAME', 'MOBILE_NO', 'BIRTH_DATE', 'COMPANY_NAME', 'BRANCH_NAME', 'DEPARTMENT_NAME', 'DESIGNATION_TITLE'], true);
        
        app.pdfExport(
                'employeeTable',
                {
                    'FULL_NAME': 'Employee',
                    'GENDER_NAME': 'Gender',
                    'COMPANY_NAME': 'Company',
                    'BRANCH_NAME': 'Branch',
                    'DEPARTMENT_NAME': 'Department',
                    'DESIGNATION_TITLE': 'Designation',
                    'LEVEL_NO': 'Level',
                    'POSITION_NAME': 'Position',
                    'LEVEL_NO': 'Level',
                    'JOIN_DATE': 'Join Date',
                    'BIRTH_DATE': 'Birth Date',
                    'COUNTRY_NAME': 'Country',
                    'RELIGION_NAME': 'Relgion',
                    'BLOOD_GROUP_CODE': 'Blood Group',
                    'MOBILE_NO': 'Mobile',
                    'TELEPHONE_NO': 'Telephone',
                    'SOCIAL_ACTIVITY': 'Social Activity',
                    'EXTENSION_NO': 'Extension No',
                    'EMAIL_OFFICIAL': 'Email Official',
                    'EMAIL_PERSONAL': 'Email Personal',
                    'SOCIAL_NETWORK': 'Social Network',
                    'ADDR_PERM_HOUSE_NO': 'Permanent House No',
                    'ADDR_PERM_WARD_NO': 'Permanent Ward No',
                    'ADDR_PERM_STREET_ADDRESS': 'Permanent Street Address',
                    'ADDR_PERM_ZONE_NAME': 'Permanenet Zone',
                    'ADDR_PERM_DISTRICT_NAME': 'Permanent District',
                    'VDC_MUNICIPALITY_NAME': ' Permanent Municipality',
                    'ADDR_TEMP_HOUSE_NO': 'Temp House No',
                    'ADDR_TEMP_WARD_NO': 'Temp Ward No',
                    'ADDR_TEMP_STREET_ADDRESS': 'Temp Street Address',
                    'ADDR_TEMP_ZONE_NAME': 'Temp Zone',
                    'ADDR_TEMP_DISTRICT_NAME': 'Temp District',
                    'VDC_MUNICIPALITY_NAME_TEMP': 'Temp Municipality',
                    'EMRG_CONTACT_NAME': 'Emergency Contact Name',
                    'EMERG_CONTACT_RELATIONSHIP': 'Emergency Contact Relationship',
                    'EMERG_CONTACT_ADDRESS': 'Emergency Contact Address',
                    'EMERG_CONTACT_NO': 'Emergency Contact No',
                    'FAM_FATHER_NAME': 'Father Name',
                    'FAM_FATHER_OCCUPATION': 'Father Occupation',
                    'FAM_GRAND_FATHER_NAME': 'Grand Father Name',
                    'MARITAL_STATUS': 'Maritual Status',
                    'FAM_SPOUSE_NAME': 'Spouse Name',
                    'FAM_SPOUSE_OCCUPATION': 'Spouse Occupation',
                    'FAM_MOTHER_NAME': 'Mother Name',
                    'FAM_GRAND_MOTHER_NAME': 'Grand Mother Name',
                    'FAM_SPOUSE_BIRTH_DATE': 'Spouse BirthDate',
                    'FAM_SPOUSE_WEDDING_ANNIVERSARY': 'Spouse Wedding Anniversary',
                    'ID_CARD_NO': 'ID Card No',
                    'ID_LBRF': 'ID Lb Rf.',
                    'ID_BAR_CODE': 'Bar Code',
                    'ID_PROVIDENT_FUND_NO': 'Provident Fund No',
                    'ID_DRIVING_LICENCE_NO': 'Driving Licence No',
                    'ID_DRIVING_LICENCE_EXPIRY': 'Driving licence Expiary',
                    'ID_DRIVING_LICENCE_TYPE': 'Driving Licence Type',
                    'ID_PASSPORT_NO': 'Passport No',
                    'ID_CITIZENSHIP_NO': 'Citizenship No',
                    'ID_CIT_ISSUE_PLACE_NAME': 'Citizenship Issued Place',
                    'ID_THUMB_ID': 'Thumb Id',
                    'ID_PAN_NO': 'Pan No',
                    'ID_ACCOUNT_NO': 'Account No',
                    'ID_RETIREMENT_NO': 'Retirement No',
                    'ID_CITIZENSHIP_ISSUE_DATE': 'Citizenship Issue Date',
                    'ID_PASSPORT_EXPIRY': 'Passport Expiry',
                    'SALARY': 'Salary',
                    'SALARY_PF': 'Salary Pf',
                    'SERVICE_TYPE_NAME': 'Service Type',
                    'SERVICE_EVENT_TYPE_NAME': 'Service Event Type',
                }
        );

e
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller('employeeListController', function ($scope, $http, $window) {
            var displayKendoFirstTime = true;
            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var companyId = angular.element(document.getElementById('companyId')).val();
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
                        'companyId': companyId,
                        'serviceEventTypeId': serviceEventTypeId
                    }
                }).then(function (success) {
                    console.log(success.data);
                    App.unblockUI("#hris-page-content");
                    console.log("pullEmployeeList", success.data);
                    if (displayKendoFirstTime) {
                        $scope.initializekendoGrid();
                        displayKendoFirstTime = false;
                        $("#searchFieldDiv").show();
                    }
                    var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
                    var grid = $('#employeeTable').data("kendoGrid");
                    dataSource.read();
                    grid.setDataSource(dataSource)
                    window.app.scrollTo('employeeTable');
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            };


            $scope.initializekendoGrid = function () {
                $("#employeeTable").kendoGrid({
                    excel: {
                        fileName: "EmployeeList.xlsx",
                        filterable: true,
                        allPages: true
                    },
                    height: 500,
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
                        {field: "FULL_NAME", title: "Full Name", width: 180},
                        {field: "MOBILE_NO", title: "Mobile No", width: 110},
                        {field: "BIRTH_DATE", title: "Birth Date", width: 110},
                        {field: "COMPANY_NAME", title: "Company", width: 110},
                        {field: "BRANCH_NAME", title: "Branch", width: 110},
                        {field: "DEPARTMENT_NAME", title: "Department", width: 130},
                        {field: "DESIGNATION_TITLE", title: "Designation", width: 130},
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


                $("#export").unbind("click");
                $("#export").click(function (e) {
                    var rows = [{
                            cells: [
                                {value: "Employee Name"},
//                                {value: "Name in Nepali"},
                                {value: "Gender"},
                                {value: "Company"},
                                {value: "Branch"},
                                {value: "Department"},
                                {value: "Designation"},
                                {value: "Position"},
                                {value: "Level"},
                                {value: "Join Date"},
                                {value: "Birth Date"},
                                {value: "Country"},
                                {value: "Religion"},
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
                                {value: "Salary"},
                                {value: "Salary PF"},
                                {value: "Service Type Name"},
                                {value: "Service Event Type Name"},
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
                        rows.push({
                            cells: [
                                {value: dataItem.FULL_NAME},
//                                {value: dataItem.NAME_NEPALI},
                                {value: dataItem.GENDER_NAME},
                                {value: dataItem.COMPANY_NAME},
                                {value: dataItem.BRANCH_NAME},
                                {value: dataItem.DEPARTMENT_NAME},
                                {value: dataItem.DESIGNATION_TITLE},
                                {value: dataItem.POSITION_NAME},
                                {value: dataItem.LEVEL_NO},
                                {value: dataItem.JOIN_DATE},
                                {value: dataItem.BIRTH_DATE},
                                {value: dataItem.COUNTRY_NAME},
                                {value: dataItem.RELIGION_NAME},
                                {value: dataItem.BLOOD_GROUP_CODE},
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
                                {value: dataItem.SALARY},
                                {value: dataItem.SALARY_PF},
                                {value: dataItem.SERVICE_TYPE_NAME},
                                {value: dataItem.SERVICE_EVENT_TYPE_NAME},
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
//                                    {autoWidth: true},
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

            };
            window.app.UIConfirmations();
//            $scope.initializekendoGrid([]);

//            $scope.msg =  $window.localStorage.getItem("msg");
//            if($window.localStorage.getItem("msg")){
//                window.toastr.success($scope.msg, "Notifications");
//            }
//            $window.localStorage.removeItem("msg");

        });

