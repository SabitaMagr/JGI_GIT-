(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#employeeList');
        app.initializeKendoGrid($table, [
            {field: "EMPLOYEE_CODE", title: "Employee Code", template: "<span>#: (EMPLOYEE_CODE == null) ? '-' : EMPLOYEE_CODE #</span>"},
            {field: "FULL_NAME", title: "Full Name", template: "<span>#: (FULL_NAME == null) ? '-' : FULL_NAME #</span>"},
            {field: "MOBILE_NO", title: "Mobile No", template: "<span>#: (MOBILE_NO == null) ? '-' : MOBILE_NO #</span>"},
            {field: "BIRTH_DATE", title: "Birth Date", template: "<span>#: (BIRTH_DATE == null) ? '-' : BIRTH_DATE #</span>"},
            {field: "COMPANY_NAME", title: "Company", template: "<span>#: (COMPANY_NAME == null) ? '-' : COMPANY_NAME #</span>"},
            {field: "BRANCH_NAME", title: "Branch", template: "<span>#: (BRANCH_NAME == null) ? '-' : BRANCH_NAME #</span>"},
            {field: "DEPARTMENT_NAME", title: "Department", template: "<span>#: (DEPARTMENT_NAME == null) ? '-' : DEPARTMENT_NAME #</span>"},
            {field: "DESIGNATION_TITLE", title: "Designation", template: "<span>#: (DESIGNATION_TITLE == null) ? '-' : DESIGNATION_TITLE #</span>"},
        ], "EmployeeList.xlsx");
        app.searchTable('employeeList', ['EMPLOYEE_CODE', 'FULL_NAME', 'MOBILE_NO', 'BIRTH_DATE', 'COMPANY_NAME', 'BRANCH_NAME', 'DEPARTMENT_NAME', "DESIGNATION_TITLE"]);
        app.pdfExport(
                'employeeList',
                {
                    'EMPLOYEE_CODE': 'Employee Code',
                    'FULL_NAME': 'Full Name',
                    'MOBILE_NO': 'Mobile No',
                    'BIRTH_DATE': 'Birth Date',
                    'COMPANY_NAME': 'Company',
                    'BRANCH_NAME': 'Branch',
                    'DEPARTMENT_NAME': 'Department',
                    'DESIGNATION_TITLE': 'Designation',
                }
        );
        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });$("#export").click(function (e) {
            var rows = [{
                    cells: [
                        {value: "Employee Code"},
                        {value: "Full Name"},
                        {value: "Mobile No"},
                        {value: "Birth Date"},
                        {value: "Company"},
                        {value: "Branch"},
                        {value: "Department"},
                        {value: "Designation"}
                    ]
                }];
            var dataSource = $("#employeeList").data("kendoGrid").dataSource;
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
                        {value: dataItem.EMPLOYEE_CODE},
                        {value: dataItem.FULL_NAME},
                        {value: dataItem.MOBILE_NO},
                        {value: dataItem.BIRTH_DATE},
                        {value: dataItem.COMPANY_NAME},
                        {value: dataItem.BRANCH_NAME},
                        {value: dataItem.DEPARTMENT_NAME},
                        {value: dataItem.DESIGNATION_TITLE}
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
                            {autoWidth: true}
                        ],
                        title: "Employee List",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "EmployeeList.xlsx"});
        }


    });
})(window.jQuery, window.app);
