(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();


        var $employeeTable = $('#employeeTable');
        var $search = $('#search');

        var actiontemplateConfig = {
            view: {
                'params': ["EMPLOYEE_ID"],
                'url': document.viewLink
            },
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["EMPLOYEE_ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["EMPLOYEE_ID"],
                'url': document.deleteLink
            }
        };
        app.initializeKendoGrid($employeeTable, [
            {field: "EMPLOYEE_CODE", title: "Code", locked: true, width: 70},
            {field: "FULL_NAME", title: "Full Name", locked: true, width: 150},
            {field: "MOBILE_NO", title: "Mobile No", locked: true, width: 100},
            {field: "BIRTH_DATE", title: "Birth Date", locked: true, width: 100},
            {field: "JOIN_DATE", title: "Join Date", locked: true, width: 100},
            {field: "COMPANY_NAME", title: "Company", width: 150},
            {field: "BRANCH_NAME", title: "Branch", width: 150},
            {field: "DEPARTMENT_NAME", title: "Department", width: 150},
            {field: "DESIGNATION_TITLE", title: "Designation", width: 150},
            {field: "POSITION_NAME", title: "Position", width: 150},
            {field: "LEVEL_NO", title: "Level", width: 150},
            {field: "LOCATION_EDESC", title: "Location", width: 150},
            {field: "FUNCTIONAL_TYPE_EDESC", title: "Functional Type", width: 150},
            {field: "FUNCTIONAL_LEVEL_EDESC", title: "Functional Level", width: 150},
            {field: "EMPLOYEE_ID", title: "Action", width: 120, locked: true, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ]);
        app.searchTable('employeeTable', ['EMPLOYEE_CODE', 'FULL_NAME', 'MOBILE_NO', 'BIRTH_DATE', 'COMPANY_NAME', 'BRANCH_NAME', 'DEPARTMENT_NAME', 'DESIGNATION_TITLE'], false);

        var map = {
            'EMPLOYEE_CODE': 'Employee Code',
            'FULL_NAME': 'Employee',
            'GENDER_NAME': 'Gender',
            'COMPANY_NAME': 'Company',
            'BRANCH_NAME': 'Branch',
            'DEPARTMENT_NAME': 'Department',
            'DESIGNATION_TITLE': 'Designation',
            'POSITION_NAME': 'Position',
            'LEVEL_NO': 'Level',
            'LOCATION_EDESC': 'Location',
            'FUNCTIONAL_TYPE_EDESC': 'Functional Type',
            'FUNCTIONAL_LEVEL_EDESC': 'Functional Level',
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
        };
        $('#excelExport').on('click', function () {
            app.excelExport($employeeTable, map, 'Employee List.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($employeeTable, map, 'Employee List.pdf');
        });

        $search.on('click', function () {
            var data = document.searchManager.getSearchValues();
            app.serverRequest(document.pullEmployeeListForEmployeeTableLink, data).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($employeeTable, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

    });
})(window.jQuery, window.app);